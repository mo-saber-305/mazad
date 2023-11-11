<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWithdrawLogResource extends JsonResource
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

        if ($this->status == 2):
            $status = __('Pending');
        elseif ($this->status == 1):
            $status = __('Completed');
        elseif ($this->status == 3):
            $status = __('Rejected');
        endif;

        return [
            'transaction_id' => $this->trx,
            'gateway' => __($this->method->name),
            'amount' => showAmount($this->amount) . __($general->cur_text),
            'charge' => showAmount($this->charge) . __($general->cur_text),
            'after_charge' => showAmount($this->after_charge) . __($general->cur_text),
            'rate' => showAmount($this->rate) . __($this->currency),
            'receivable' => showAmount($this->final_amount) . __($this->currency),
            'status' => $status,
            'time' => showDateTime($this->created_at),
        ];
    }
}
