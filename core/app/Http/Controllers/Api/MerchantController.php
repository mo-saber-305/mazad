<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboardResource;
use App\Http\Resources\MerchantProductBidsResource;
use App\Http\Resources\MerchantProductsResource;
use App\Http\Resources\MerchantTransactionsResource;
use App\Http\Resources\MerchantViewTicketResource;
use App\Http\Resources\MerchantWinnersResource;
use App\Http\Resources\MerchantWithdrawLogResource;
use App\Http\Resources\MerchantWithdrawMethodsResource;
use App\Http\Resources\UserTicketResource;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Winner;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MerchantController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('api_merchant')->user();
    }

    public function dashboard()
    {
        $data = new MerchantDashboardResource();
        $notify = __('Dashboard Data');
        return responseJson(200, 'success', $notify, $data);
    }

    protected function filterProducts($type, $search)
    {

        $products = Product::query();

        if ($type != 'all') {
            $products = $products->$type();
        }

        if ($search) {
            $products = $products->orWhere('name', 'like', '%' . $search . '%')
                ->orWhereHas('merchant', function ($merchant) use ($search) {
                    $merchant->where('username', 'like', "%$search%");
                })->orWhereHas('admin', function ($admin) use ($search) {
                    $admin->where('username', 'like', "%$search%");
                });

        }

        return $products->with('category')->where('merchant_id', $this->user->id)->latest()->paginate(PAGINATION_COUNT);
    }

    public function products(Request $request)
    {
        $search_key = null;
        $product_type = 'all';
        if ($request->has('search_key')) {
            $search_key = $request->search_key;
        }
        if ($request->has('product_type')) {
            $product_type = $request->product_type;
        }

        $products = $this->filterProducts($product_type, $search_key);
        $data = MerchantProductsResource::collection($products);
        $notify = __('Products Data');
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($products));
    }

    public function storeProduct(Request $request)
    {
        $this->productValidation($request, 'required');
        $product = new Product();

        $this->saveProduct($request, $product);
        $notify = __('Product added successfully');
        return responseJson('200', 'success', $notify);
    }

    public function updateProduct(Request $request, $id)
    {
        $this->productValidation($request, 'nullable');
        $product = Product::findOrFail($id);
        $this->saveProduct($request, $product);
        $notify = __('Product updated successfully');
        return responseJson('200', 'success', $notify);
    }

    public function saveProduct($request, $product)
    {
        if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify = __('Image could not be uploaded.');
                return responseJson('200', 'failed', $notify);
            }
        }

        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->merchant_id = $this->user->id;
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->specification = $request->specification ?? null;

        $product->save();

        if ($request->has('images')) {
            try {
                if ($product->images) {
                    $location = imagePath()['product']['path'];
                    foreach ($product->images as $image) {
                        removeFile($location . '/' . $image->image);
                        removeFile($location . '/thumb_' . $image->image);
                        $image->delete();
                    }
                }
                foreach ($request->images as $image) {
                    $image = uploadImage($image, imagePath()['product']['path'], imagePath()['product']['size'], null, imagePath()['product']['thumb']);
                    $product->images()->create(['image' => $image]);
                }
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
    }


    protected function productValidation($request, $imgValidation)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|exists:categories,id',
            'price' => 'required|numeric|gte:0',
            'expired_at' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'specification' => 'nullable|array',
            'started_at' => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
            'image' => [$imgValidation, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp'])]
        ]);
    }

    public function productBids($id)
    {
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantProductBidsResource::collection($bids);
        $notify = __('Product Bids data');
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($bids));
    }

    public function bidWinner(Request $request)
    {
        $request->validate([
            'bid_id' => 'required'
        ]);


        $bid = Bid::with('user', 'product')
            ->whereHas('product', function ($product) {
                $product->where('merchant_id', $this->user->id);
            })->findOrFail($request->bid_id);

        $product = $bid->product;

        $winner = Winner::where('product_id', $product->id)->exists();

        if ($winner) {
            $notify = __('Winner for this product id already selected');
            return responseJson('200', 'failed', $notify);
        }

        if ($product->expired_at > now()) {
            $notify = __('This product is not expired till now');
            return responseJson('200', 'failed', $notify);
        }

        $winner = new Winner();
        $winner->user_id = $bid->user_id;
        $winner->product_id = $bid->product_id;
        $winner->bid_id = $bid->id;
        $winner->save();

        $notify = __('Winner published successfully');
        return responseJson('200', 'success', $notify);

    }

    public function productWinner()
    {
        $winners = Winner::with('product', 'user')
            ->whereHas('product', function ($product) {
                $product->where('merchant_id', $this->user->id);
            })
            ->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantWinnersResource::collection($winners);
        $notify = __('Winners data');
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($winners));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::whereHas('product', function ($product) {
            $product->where('merchant_id', $this->user->id);
        })->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify = __('Product mark as delivered');
        return responseJson('200', 'success', $notify);

    }

    public function transactions()
    {
        $transactions = Transaction::where('merchant_id', $this->user->id)->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantTransactionsResource::collection($transactions);
        $notify = __('transactions Data');
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($transactions));

    }

    public function submitProfile(Request $request)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|integer|min:1',
            'city' => 'sometimes|required|max:50',
            'social_links' => 'nullable|array',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'cover_image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required' => 'Last name field is required'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $merchant = Auth::guard('api_merchant')->user();

        $merchant->firstname = $request->firstname;
        $merchant->lastname = $request->lastname;
        $merchant->social_links = $request->social_links ?? null;

        $user = Auth::guard('api_merchant')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['merchant']['path'], imagePath()['profile']['merchant']['size'], $old);
            } catch (\Exception $exp) {
                $notify = __('Image could not be uploaded.');
                return responseJson(422, 'failed', $notify);
            }
        }

        if ($request->hasFile('cover_image')) {
            try {
                $old = $user->cover_image ?: null;
                $user->cover_image = uploadImage($request->cover_image, imagePath()['profile']['merchant_cover']['path'], imagePath()['profile']['merchant_cover']['size'], $old);
            } catch (\Exception $exp) {
                $notify = __('Image could not be uploaded.');
                return responseJson(422, 'failed', $notify);
            }
        }

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$merchant->address->country,
            'city' => $request->city,
        ];

        $merchant->fill($in)->save();

        $notify = __('Profile updated successfully.');
        return responseJson('200', 'success', $notify);

    }

    public function submitPassword(Request $request)
    {
        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $password_validation]
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api_merchant')->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify = __('Password changes successfully');
            return responseJson(200, 'success', $notify);
        } else {
            $notify = __("The password doesn't match!");
            return responseJson(422, 'failed', $notify);
        }
    }

    public function withdrawMethods()
    {
        $withdrawMethod = WithdrawMethod::where('status', 1)->get();
        $notify = __('Withdraw methods');
        $data = MerchantWithdrawMethodsResource::collection($withdrawMethod);

        return responseJson(200, 'success', $notify, $data);
    }

    public function withdrawStore(Request $request)
    {
        $general = GeneralSetting::first();
        $validator = Validator::make($request->all(), [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->first();
        if (!$method) {
            $notify = __('Method not found.');
            return responseJson(404, 'error', $notify);
        }
        $user = auth('api_merchant')->user();
        if ($request->amount < $method->min_limit) {
            $notify = __('Your requested amount is smaller than minimum amount.');
            return responseJson(422, 'failed', $notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify = __('Your requested amount is larger than maximum amount.');
            return responseJson(422, 'failed', $notify);
        }

        if ($request->amount > $user->balance) {
            $notify = __('You do not have sufficient balance for withdraw.');
            return responseJson(422, 'failed', $notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->merchant_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        $user_data = [];
        foreach ($withdraw->method->user_data as $item) {
            $user_data[] = [
                'field_name' => $item->field_name,
                'field_level' => $item->field_level,
                'type' => $item->type,
                'validation' => $item->validation,
            ];
        }
        $data = [
            'method_id' => $withdraw->method_id,
            'description' => $withdraw->method->description,
            'currency' => showAmount(auth()->guard('api_merchant')->user()->balance) . ' ' . __($general->cur_text),
            'currency_will_be' => showAmount($withdraw->merchant->balance - ($withdraw->amount)),
            'request_amount' => showAmount($withdraw->amount) . ' ' . __($general->cur_text),
            'withdrawal_charge' => showAmount($withdraw->charge) . ' ' . __($general->cur_text),
            'after_charge' => showAmount($withdraw->after_charge) . ' ' . __($general->cur_text),
            'conversion_rate' => '1 ' . __($general->cur_text) . ' = ' . showAmount($withdraw->rate) . ' ' . __($withdraw->currency),
            'you_will_get' => showAmount($withdraw->final_amount) . ' ' . __($withdraw->currency),
            'user_data' => $user_data,
        ];

        $notify = __('Withdraw request sent successfully');
        return responseJson(202, 'created', $notify, $data);
    }

    public function withdrawConfirm(Request $request)
    {

        $withdraw = Withdrawal::with('method', 'user')->where('trx', $request->transaction)->where('status', 0)->orderBy('id', 'desc')->first();

        if (!$withdraw) {
            $notify = __('Withdraw request not found');
            return responseJson(404, 'error', $notify);
        }

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg', 'jpeg', 'png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $rules['transaction'] = 'required';
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api_merchant')->user();
        if ($withdraw->amount > $user->balance) {
            $notify = __('Your balance is less than the amount you want to withdraw');
            return responseJson(422, 'failed', $notify);
        }
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify = __('Wrong verification code');
                return responseJson(422, 'failed', $notify);
            }
        }


        if ($withdraw->amount > $user->balance) {
            $notify = __('Your request amount is larger then your current balance.');
            return responseJson(422, 'failed', $notify);
        }

        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['withdraw']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify = __('Could not upload your ') . $request[$inKey];
                                    return responseJson(422, 'failed', $notify);
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $user->balance -= $withdraw->amount;
        $user->save();


        $transaction = new Transaction();
        $transaction->merchant_id = $withdraw->merchant_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->merchant_id = $user->id;
        $adminNotification->title = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details', $withdraw->id);
        $adminNotification->save();

        $general = GeneralSetting::first();
        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify = __('Withdraw request sent successfully');
        return responseJson(200, 'success', $notify);
    }

    public function withdrawLog()
    {
        $withdrawals = Withdrawal::where('merchant_id', auth('api_merchant')->user()->id)->where('status', '!=', 0)->with('method')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);
        $data = MerchantWithdrawLogResource::collection($withdrawals);
        $notify = __('Withdraw Log');
//        $data = [
//            'withdrawals' => $withdrawals,
//            'verification_file_path' => imagePath()['verify']['withdraw']['path'],
//        ];
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($withdrawals));
    }

    public function ticket()
    {
        $user = auth('api_merchant')->user();
        $supports = SupportTicket::where('merchant_id', $user->id)->orderBy('priority', 'desc')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);

        $data = UserTicketResource::collection($supports);
        $notify = __('Ticket Data');
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($supports));
    }

    public function viewTicket($id)
    {
        $userId = auth('api_merchant')->user()->id;
        $my_ticket = SupportTicket::where('id', $id)->where('merchant_id', $userId)->orderBy('id', 'desc')->firstOrFail();
        $data = new MerchantViewTicketResource($my_ticket);
        $notify = __('Ticket Details Data');
        return responseJson(200, 'success', $notify, $data);
    }

    public function closeTicket(Request $request, $id)
    {
        $userId = auth('api_merchant')->user()->id;
        $ticket = SupportTicket::where('merchant_id', $userId)->where('id', $id)->firstOrFail();
        $ticket->status = 3;
        $ticket->last_reply = Carbon::now();
        $ticket->save();
        $notify = __('Support ticket closed successfully!');
        return responseJson(200, 'success', $notify);
    }

    public function storeTicket(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $files = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx');

        $validator = Validator::make($request->all(), [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($files, $allowedExts) {
                    foreach ($files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > 2) {
                            return $fail("Miximum 2MB file size allowed!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
                        }
                    }
                    if (count($files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
            'priority' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api_merchant')->user();
        $ticket->merchant_id = $user->id;
        $random = rand(100000, 999999);
        $ticket->ticket = $random;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->priority = $request->priority;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();


        $adminNotification = new AdminNotification();
        $adminNotification->merchant_id = $user->id;
        $adminNotification->title = 'New support ticket has opened';
        $adminNotification->click_url = urlPath('admin.merchant.ticket.view', $ticket->id);
        $adminNotification->save();


        $path = imagePath()['ticket']['path'];
        if ($request->hasFile('attachments')) {
            foreach ($files as $file) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = uploadFile($file, $path);
                    $attachment->save();
                } catch (\Exception $exp) {
                    $notify = __('Could not upload your file');
                    return responseJson(422, 'failed', $notify);
                }
            }
        }
        $notify = __('ticket created successfully!');
        return responseJson(200, 'success', $notify);
    }


    public function replyTicket(Request $request, $id)
    {
        $userId = auth('api_merchant')->user()->id;
        $ticket = SupportTicket::where('merchant_id', $userId)->where('id', $id)->firstOrFail();
        $message = new SupportMessage();

        $attachments = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx');

        $validator = Validator::make($request->all(), [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($attachments, $allowedExts) {
                    foreach ($attachments as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > 2) {
                            return $fail("Miximum 2MB file size allowed!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf doc docx files are allowed");
                        }
                    }
                    if (count($attachments) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $ticket->status = 2;
        $ticket->last_reply = Carbon::now();
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $path = imagePath()['ticket']['path'];

        if ($request->hasFile('attachments')) {
            foreach ($attachments as $file) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = uploadFile($file, $path);
                    $attachment->save();

                } catch (\Exception $exp) {
                    $notify = __('Could not upload your ') . $file;
                    return responseJson(422, 'failed', $notify);
                }
            }
        }

        $notify = __('Support ticket replied successfully!');
        return responseJson(200, 'success', $notify);
    }

    public function twoFactor()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $merchant = auth()->guard('api_merchant')->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($merchant->username . '@' . $general->sitename, $secret);
        $data = [
            'secret' => $secret,
            'qr_code' => $qrCodeUrl,
            'google_authenticator_app' => 'https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en',
        ];
        $notify = __('Two Factor Data');
        return responseJson(200, 'success', $notify, $data);
    }

    public function enableTwoFactor(Request $request)
    {
        $merchant = auth()->guard('api_merchant')->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($merchant, $request->code, $request->key);
        if ($response) {
            $merchant->tsc = $request->key;
            $merchant->ts = 1;
            $merchant->save();
            $merchantAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($merchant, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$merchantAgent['ip'],
                'time' => @$merchantAgent['time']
            ], 'merchant');
            $notify = __('Google authenticator enabled successfully');
            return responseJson(200, 'success', $notify);
        } else {
            $notify = __('Wrong verification code');
            return responseJson(422, 'failed', $notify);
        }
    }


    public function disbleTwoFactor(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $merchant = auth()->guard('api_merchant')->user();
        $response = verifyG2fa($merchant, $request->code);
        if ($response) {
            $merchant->tsc = null;
            $merchant->ts = 0;
            $merchant->save();
            $merchantAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($merchant, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$merchantAgent['ip'],
                'time' => @$merchantAgent['time']
            ], 'merchant');
            $notify = __('Two factor authenticator disable successfully');
            return responseJson(200, 'success', $notify);
        } else {
            $notify = __('Wrong verification code');
            return responseJson(422, 'failed', $notify);
        }
    }
}