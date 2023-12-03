<?php

namespace App\Http\Controllers\Gateway\Stripe;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;


class ProcessController extends Controller
{

    /*
     * Stripe Gateway
     */
    public static function process($deposit)
    {

        $alias = $deposit->gateway->alias;

        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.' . $alias);
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        if ($request->has('request_type') && $request->get('request_type') == 'api') {
            $track = $request->get('track');
        } else {
            $track = Session::get('Track');
        }


        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($deposit->status == 1) {
            if ($request->has('request_type') && $request->get('request_type') == 'api') {
                $notify = __('Invalid request.');
                return responseJson(422, 'failed', $notify);
            } else {
                $notify[] = ['error', 'Invalid request.'];
                return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
            }
        }


        if ($request->has('request_type') && $request->get('request_type') == 'api') {
            $validator = Validator::make($request->all(), [
                'cardNumber' => 'required',
                'cardExpiry' => 'required',
                'cardCVC' => 'required',
            ]);

            if ($validator->fails()) {
                return responseJson(422, 'failed', $validator->errors()->all());
            }
        } else {
            $this->validate($request, [
                'cardNumber' => 'required',
                'cardExpiry' => 'required',
                'cardCVC' => 'required',
            ]);
        }


        $cc = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        $exp = $pieces = explode("/", $_POST['cardExpiry']);
        $emo = trim($exp[0]);
        $eyr = trim($exp[1]);
        $cnts = round($deposit->final_amo, 2) * 100;

        $stripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        Stripe::setApiKey($stripeAcc->secret_key);

        Stripe::setApiVersion("2020-03-02");

        try {
            $token = Token::create(array(
                "card" => array(
                    "number" => "$cc",
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => "$cvc"
                )
            ));
            try {
                $charge = Charge::create(array(
                    'card' => $token['id'],
                    'currency' => $deposit->method_currency,
                    'amount' => $cnts,
                    'description' => 'item',
                ));

                if ($charge['status'] == 'succeeded') {
                    PaymentController::userDataUpdate($deposit->trx);
                    if ($request->has('request_type') && $request->get('request_type') == 'api') {
                        $notify = __('Payment captured successfully.');
                        return responseJson(200, 'success', $notify);
                    } else {
                        $notify[] = ['success', 'Payment captured successfully.'];
                        return redirect()->route(gatewayRedirectUrl(true))->withNotify($notify);
                    }
                }
            } catch (\Exception $e) {
                if ($request->has('request_type') && $request->get('request_type') == 'api') {
                    return responseJson(500, 'error', $e->getMessage());
                } else {
                    $notify[] = ['error', $e->getMessage()];
                }

            }
        } catch (\Exception $e) {
            if ($request->has('request_type') && $request->get('request_type') == 'api') {
                return responseJson(500, 'error', $e->getMessage());
            } else {
                $notify[] = ['error', $e->getMessage()];
            }
        }

        return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
}
