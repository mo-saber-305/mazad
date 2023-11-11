<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductBidsResource extends JsonResource
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
            'user_name' => __($this->user->fullname),
            'product_name' => __($this->product->name),
            'product_price' => $general->cur_sym . showAmount($this->product->price),
            'amount' => $general->cur_sym . showAmount($this->amount),
            'bid_time' => showDateTime($this->created_at),
            'winner' => $this->winner,
        ];
    }
}
