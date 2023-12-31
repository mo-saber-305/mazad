<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }


    public function sendResetCodeEmail(Request $request)
    {
        if ($request->type == 'email') {
            $validationRule = [
                'value' => 'required|email'
            ];
            $validationMessage = [
                'value.required' => __('Email field is required'),
                'value.email' => __('Email must be an valid email')
            ];
        } elseif ($request->type == 'username') {
            $validationRule = [
                'value' => 'required'
            ];
            $validationMessage = ['value.required' => __('Username field is required')];
        } else {
            $notify = __('Invalid selection');
            return responseJson(422, 'failed', $notify);
        }
        $validator = Validator::make($request->all(), $validationRule, $validationMessage);
        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = User::where($request->type, $request->value)->first();

        if (!$user) {
            $notify = __('User not found.');
            return responseJson(404, 'failed', $notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        sendEmail($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser' => @$userBrowserInfo['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ]);
        $email = $user->email;
        $notify = __('Password reset email sent successfully');

        return responseJson(200, 'success', $notify, ['email' => $email]);
    }


    public function merchantSendResetCodeEmail(Request $request)
    {
        if ($request->type == 'email') {
            $validationRule = [
                'value' => 'required|email'
            ];
            $validationMessage = [
                'value.required' => __('Email field is required'),
                'value.email' => __('Email must be an valid email')
            ];
        } elseif ($request->type == 'username') {
            $validationRule = [
                'value' => 'required'
            ];
            $validationMessage = ['value.required' => __('Username field is required')];
        } else {
            $notify = __('Invalid selection');
            return responseJson(422, 'failed', $notify);
        }
        $validator = Validator::make($request->all(), $validationRule, $validationMessage);
        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = Merchant::where($request->type, $request->value)->first();

        if (!$user) {
            $notify = __('Merchant not found.');
            return responseJson(404, 'failed', $notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        sendEmail($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser' => @$userBrowserInfo['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ]);
        $email = $user->email;
        $notify = __('Password reset email sent successfully');

        return responseJson(200, 'success', $notify, ['email' => $email]);
    }


    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $code = $request->code;

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            $notify = __('Invalid token');
            return responseJson(401, 'failed', $notify);
        }

        $notify = __('You can change your password');
        $data = [
            'token' => $code,
            'email' => $request->email,
        ];

        return responseJson(200, 'success', $notify, $data);
    }

}
