<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboardResource;
use App\Http\Resources\MerchantProfileResource;
use App\Http\Resources\MerchantsResource;
use App\Http\Resources\UserBiddingResource;
use App\Http\Resources\UserDashboardResource;
use App\Http\Resources\UserDepositHistoryResource;
use App\Http\Resources\UserTicketResource;
use App\Http\Resources\UserTransactionsResource;
use App\Http\Resources\UserViewTicketResource;
use App\Http\Resources\UserWinningResource;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Merchant;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function profile(Request $request)
    {

        $user = auth('api')->user();

        $data = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'address' => $user->address->address,
            'state' => $user->address->state,
            'zip' => $user->address->zip,
            'country' => $user->address->country,
            'city' => $user->address->city,
            'image' => imagePath()['profile']['user']['path'] . '/' . $user->image,
        ];

        $notify = 'Profile Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required' => 'Last name field is required'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api')->user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify = 'Profile updated successfully';
        return responseJson(200, 'success', $notify);
    }

    public function merchants(Request $request)
    {
        $merchants = Merchant::where('status', 1)->paginate(PAGINATION_COUNT);

        $general = MerchantsResource::collection($merchants);

        $notify = 'Merchants data';
        return responseJson(200, 'success', $notify, $general, responseWithPaginagtion($merchants));
    }

    public function merchantProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required',
            'merchant_type' => 'required|in:merchant,admin',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        if ($request->merchant_type == 'admin') {
            $merchant = Admin::findOrFail($request->merchant_id);
        } else {
            $merchant = Merchant::findOrFail($request->merchant_id);
        }

        $general = new MerchantProfileResource($merchant);

        $notify = 'Merchant Profile data';
        return responseJson(200, 'success', $notify, $general);
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

        $user = auth('api')->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify = 'Password changes successfully';
            return responseJson(200, 'success', $notify);
        } else {
            $notify = 'The password doesn\'t match!';
            return responseJson(422, 'failed', $notify);
        }
    }

    public function withdrawMethods()
    {
        $withdrawMethod = WithdrawMethod::where('status', 1)->get();
        $notify = 'Withdraw methods';
        $data = [
            'methods' => $withdrawMethod,
            'image_path' => imagePath()['withdraw']['method']['path']
        ];

        return responseJson(200, 'success', $notify, $data);
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->first();
        if (!$method) {
            $notify = 'Method not found.';
            return responseJson(404, 'error', $notify);
        }
        $user = auth('api')->user();
        if ($request->amount < $method->min_limit) {
            $notify = 'Your requested amount is smaller than minimum amount.';
            return responseJson(422, 'failed', $notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify = 'Your requested amount is larger than maximum amount.';
            return responseJson(422, 'failed', $notify);
        }

        if ($request->amount > $user->balance) {
            $notify = 'You do not have sufficient balance for withdraw.';
            return responseJson(422, 'failed', $notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();

        $notify = 'Withdraw request stored successfully';
        return responseJson(202, 'created', $notify, $withdraw);
    }

    public function withdrawConfirm(Request $request)
    {

        $withdraw = Withdrawal::with('method', 'user')->where('trx', $request->transaction)->where('status', 0)->orderBy('id', 'desc')->first();

        if (!$withdraw) {
            $notify = 'Withdraw request not found';
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

        $user = auth('api')->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify = 'Wrong verification code';
                return responseJson(422, 'failed', $notify);
            }
        }


        if ($withdraw->amount > $user->balance) {
            $notify = 'Your request amount is larger then your current balance.';
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
                                    $notify = 'Could not upload your ' . $request[$inKey];
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
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
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

        $notify = 'Withdraw request sent successfully';
        return responseJson(200, 'success', $notify);
    }

    public function withdrawLog()
    {
        $withdrawals = Withdrawal::where('user_id', auth('api')->user()->id)->where('status', '!=', 0)->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify = 'Withdraw Log';
        $data = [
            'withdrawals' => $withdrawals,
            'verification_file_path' => imagePath()['verify']['withdraw']['path'],
        ];
        return responseJson(200, 'success', $notify, $data);
    }

    public function depositHistory()
    {
        $deposits = Deposit::where('user_id', auth('api')->user()->id)->where('status', '!=', 0)->with('gateway')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);
        $notify = 'Deposit History';
        $data = UserDepositHistoryResource::collection($deposits);
//        $data = [
//            'deposit' => $deposits,
//            'verification_file_path' => imagePath()['verify']['deposit']['path'],
//        ];
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($deposits));
    }

    public function transactions()
    {
        $user = auth('api')->user();
        $transactions = $user->transactions()->latest()->paginate(PAGINATION_COUNT);
        $data = UserTransactionsResource::collection($transactions);
        $notify = 'transactions data';
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($transactions));
    }

    public function dashboard()
    {
        $data = new UserDashboardResource();
        $notify = 'Dashboard Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function merchantDashboard()
    {
        $data = new MerchantDashboardResource();
        $notify = 'Dashboard Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function biddingHistory()
    {
        $user = auth('api')->user();
        $biddingHistories = Bid::where('user_id', $user->id)->with('user', 'product')->latest()->paginate(PAGINATION_COUNT);
        $data = UserBiddingResource::collection($biddingHistories);
        $notify = 'Bidding History Data';
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($biddingHistories));
    }

    public function winningHistory()
    {
        $user = auth('api')->user();
        $winningHistories = Winner::where('user_id', $user->id)->with('user', 'product', 'bid')->latest()->paginate(PAGINATION_COUNT);
        $data = UserWinningResource::collection($winningHistories);
        $notify = 'Winning History Data';
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($winningHistories));
    }

    public function ticket()
    {
        $user = auth('api')->user();
        $supports = SupportTicket::where('user_id', $user->id)->orderBy('priority', 'desc')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);

        $data = UserTicketResource::collection($supports);
        $notify = 'Ticket Data';
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($supports));
    }

    public function viewTicket($id)
    {
        $userId = auth('api')->user()->id;
        $my_ticket = SupportTicket::where('id', $id)->where('user_id', $userId)->orderBy('id', 'desc')->firstOrFail();
        $data = new UserViewTicketResource($my_ticket);
        $notify = 'Ticket Details Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function closeTicket(Request $request, $id)
    {
        $userId = auth('api')->user()->id;
        $ticket = SupportTicket::where('user_id', $userId)->where('id', $id)->firstOrFail();
        $ticket->status = 3;
        $ticket->last_reply = Carbon::now();
        $ticket->save();
        $notify = 'Support ticket closed successfully!';
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

        $user = auth('api')->user();
        $ticket->user_id = $user->id;
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
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New support ticket has opened';
        $adminNotification->click_url = urlPath('admin.user.ticket.view', $ticket->id);
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
                    $notify = 'Could not upload your file';
                    return responseJson(422, 'failed', $notify);
                }
            }
        }
        $notify = 'ticket created successfully!';
        return responseJson(200, 'success', $notify);
    }


    public function replyTicket(Request $request, $id)
    {
        $userId = auth('api')->user()->id;
        $ticket = SupportTicket::where('user_id', $userId)->where('id', $id)->firstOrFail();
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
                    $notify = 'Could not upload your ' . $file;
                    return responseJson(422, 'failed', $notify);
                }
            }
        }

        $notify = 'Support ticket replied successfully!';
        return responseJson(200, 'success', $notify);
    }
}