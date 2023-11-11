<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTransactionsResource extends JsonResource
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
            'id' => $this->id,
            'date' => showDateTime($this->created_at),
            'trx' => $this->trx,
            'details' => __($this->details),
            'amount' => $this->trx_type . showAmount($this->amount) . ' ' . __($general->cur_text),
            'balance' => showAmount($this->post_balance) . ' ' . __($general->cur_text),
        ];
    }
}
