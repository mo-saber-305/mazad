<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestsResource;
use App\Http\Resources\MerchantProfileResource;
use App\Http\Resources\MerchantsResource;
use App\Http\Resources\UserBiddingResource;
use App\Http\Resources\UserDashboardResource;
use App\Http\Resources\UserDepositHistoryResource;
use App\Http\Resources\UserTicketResource;
use App\Http\Resources\UserTransactionsResource;
use App\Http\Resources\UserViewTicketResource;
use App\Http\Resources\UserWinningResource;
use App\Lib\GoogleAuthenticator;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Winner;
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
        $interests = InterestsResource::collection($user->interests);

        $data = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'address' => $user->address->address,
            'state' => $user->address->state,
            'zip' => $user->address->zip,
            'country' => $user->address->country,
            'city' => $user->address->city,
            'image' => imagePath()['profile']['user']['path'] . '/' . $user->image,
            'interests' => $interests,
        ];

        $notify = __('Profile Data');
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

        if ($request->has('interests')) {
            $user->interests()->sync($request->interests);
        }

        $notify = __('Profile updated successfully');
        return responseJson(200, 'success', $notify);
    }

    public function merchants(Request $request)
    {
        $merchants = Merchant::where('status', 1)->paginate(PAGINATION_COUNT);

        $general = MerchantsResource::collection($merchants);

        $notify = __('Merchants data');
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

        $notify = __('Merchant Profile data');
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
            $notify = __('Password changes successfully');
            return responseJson(200, 'success', $notify);
        } else {
            $notify = __("The password doesn't match!");
            return responseJson(422, 'failed', $notify);
        }
    }

    public function depositHistory()
    {
        $deposits = Deposit::where('user_id', auth('api')->user()->id)->where('status', '!=', 0)->with('gateway')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);
        $notify = __('Deposit History');
        $data = UserDepositHistoryResource::collection($deposits);
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($deposits));
    }

    public function transactions()
    {
        $user = auth('api')->user();
        $transactions = $user->transactions()->latest()->paginate(PAGINATION_COUNT);
        $data = UserTransactionsResource::collection($transactions);
        $notify = __('transactions Data');
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($transactions));
    }

    public function dashboard()
    {
        $data = new UserDashboardResource();
        $notify = __('Dashboard Data');
        return responseJson(200, 'success', $notify, $data);
    }

    public function biddingHistory()
    {
        $user = auth('api')->user();
        $biddingHistories = Bid::where('user_id', $user->id)->with('user', 'product')->latest()->paginate(PAGINATION_COUNT);
        $data = UserBiddingResource::collection($biddingHistories);
        $notify = __('Bidding History Data');
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($biddingHistories));
    }

    public function winningHistory()
    {
        $user = auth('api')->user();
        $winningHistories = Winner::where('user_id', $user->id)->with('user', 'product', 'bid')->latest()->paginate(PAGINATION_COUNT);
        $data = UserWinningResource::collection($winningHistories);
        $notify = __('Winning History Data');
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($winningHistories));
    }

    public function ticket()
    {
        $user = auth('api')->user();
        $supports = SupportTicket::where('user_id', $user->id)->orderBy('priority', 'desc')->orderBy('id', 'desc')->paginate(PAGINATION_COUNT);

        $data = UserTicketResource::collection($supports);
        $notify = __('Ticket Data');
        return responseJson(200, 'success', $notify, $data, responseWithPaginagtion($supports));
    }

    public function viewTicket($id)
    {
        $userId = auth('api')->user()->id;
        $my_ticket = SupportTicket::where('id', $id)->where('user_id', $userId)->orderBy('id', 'desc')->firstOrFail();
        $data = new UserViewTicketResource($my_ticket);
        $notify = __('Ticket Details Data');
        return responseJson(200, 'success', $notify, $data);
    }

    public function closeTicket(Request $request, $id)
    {
        $userId = auth('api')->user()->id;
        $ticket = SupportTicket::where('user_id', $userId)->where('id', $id)->firstOrFail();
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
        $notify = __('ticket created successfully!');
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
        $merchant = auth()->guard('api')->user();
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
        $merchant = auth()->guard('api')->user();
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

        $merchant = auth()->guard('api')->user();
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