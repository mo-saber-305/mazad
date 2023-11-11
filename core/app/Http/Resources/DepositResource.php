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
            "name" => __($this->name),
            "currency" => __($this->currency),
            "symbol" => $this->symbol,
            "method_code" => $this->method_code,
            "gateway_alias" => __($this->gateway_alias),
            "min_amount" => $this->min_amount,
            "max_amount" => $this->max_amount,
            "percent_charge" => $this->percent_charge,
            "fixed_charge" => $this->fixed_charge,
            "rate" => $this->rate,
            "image" => $this->methodImage(),
            "currency_text" => __($general->cur_text),
        ];
    }
}
