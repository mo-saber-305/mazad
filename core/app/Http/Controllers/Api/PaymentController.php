<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositResource;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    public function depositMethods()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $data = DepositResource::collection($gatewayCurrency);
        $notify = 'Payment Methods';
//        $data = [
//            'methods' => $gatewayCurrency,
//            'image_path' => imagePath()['gateway']['path']
//        ];
        return responseJson(200, 'success', $notify, $data);
    }

    public function depositInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api')->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify = 'Invalid gateway';
            return responseJson(422, 'failed', $notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify = 'Please follow deposit limit';
            return responseJson(200, 'success', $notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->from_api = 1;
        $data->save();

        $notify = 'Deposit Created';
        return responseJson(202, 'created', $notify, $data);
    }

    public function depositConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }
        $deposit = Deposit::where('trx', $request->transaction)->where('status', 0)->orderBy('id', 'DESC')->with('gateway')->first();
        if (!$deposit) {
            $notify = 'Deposit not found';
            return responseJson(404, 'error', $notify);
        }
        $dirName = $deposit->gateway->alias;
        $new = substr(__NAMESPACE__, 0, -4) . '\\Gateway' . '\\' . $dirName . '\\ProcessController';
        $data = (array)json_decode($new::process($deposit));
        if (array_key_exists('view', $data)) {
            unset($data['view']);
        }
        $notify = 'gateway data';
        return responseJson(200, 'success', $notify, ['gateway_data' => $data]);
    }


    public function manualDepositConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $request->transaction)->where('method_code', '>=', 1000)->first();
        if (!$data) {
            $notify = 'Deposit not found';
            return responseJson(404, 'error', $notify);
        }
        $method = $data->gatewayCurrency();
        $notify = 'Manual payment details';
        $data = [
            'deposit' => $data,
            'payment_method' => $method
        ];

        return responseJson(200, 'success', $notify, $data);
    }

    public function manualDepositUpdate(Request $request)
    {
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $request->transaction)->where('method_code', '>=', 1000)->first();
        if (!$data) {
            $notify = 'Deposit not found';
            return responseJson(404, 'error', $notify);
        }

        $params = json_decode($data->gatewayCurrency()->gateway_parameter);

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $custom) {
                $rules[$key] = [$custom->validation];
                if ($custom->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg', 'jpeg', 'png']));
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }
                if ($custom->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($custom->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }


        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['deposit']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
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
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
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
            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }


        $data->status = 2; // pending
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        $general = GeneralSetting::first();
        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify = 'Deposit request sent successfully';
        return responseJson(200, 'success', $notify);
    }

}