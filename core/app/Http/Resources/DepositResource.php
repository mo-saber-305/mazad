<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $general = GeneralSetting::first();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "currency" => $this->currency,
            "symbol" => $this->symbol,
            "method_code" => $this->method_code,
            "gateway_alias" => $this->gateway_alias,
            "min_amount" => $this->min_amount,
            "max_amount" => $this->max_amount,
            "percent_charge" => $this->percent_charge,
            "fixed_charge" => $this->fixed_charge,
            "rate" => $this->rate,
            "image" => $this->methodImage(),
            "currency_text" => $general->cur_text,
//            "gateway_parameter" => $this->gateway_parameter,
//            "method" => [
//                "id" => $this->method->id,
//                "code" => $this->method->code,
//                "name" => $this->method->name,
//                "alias" =>$this->method->alias,
//                "image" => getImage(imagePath()['gateway']['path'] . '/' . $this->method->image),
//                "status" => $this->method->status,
//                "gateway_parameters" => $this->method->gateway_parameters,
//                "supported_currencies" => $this->method->supported_currencies,
//                "crypto" => $this->method->crypto,
//                "extra" => $this->method->extra,
//                "description" => $this->method->description,
//                "input_form" => $this->method->input_form,
//            ]
        ];
    }
}
