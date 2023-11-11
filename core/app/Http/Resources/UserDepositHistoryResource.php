<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDepositHistoryResource extends JsonResource
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
        if ($this->status == 1):
            $status = __('Complete');
        elseif ($this->status == 2):
            $status = __('Pending');
        elseif ($this->status == 3):
            $status = __('Cancel');
        endif;

        return [
            "id" => $this->id,
            "transaction_id" => $this->trx,
            "gateway" => __($this->gateway->name),
            "amount" => showAmount($this->amount) . ' ' . __($general->cur_text),
            "status" => $status,
            "time" => showDateTime($this->created_at),
            "charge" => showAmount($this->amount + $this->charge) . ' ' . __($general->cur_text),
            "rate" => showAmount($this->rate) . ' ' . __($this->method_currency),
            'payable_amount' => showAmount($this->final_amo) . ' ' . __($this->method_currency)
        ];
    }
}
