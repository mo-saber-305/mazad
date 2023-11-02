<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWinningResource extends JsonResource
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
            'product_name' => $this->product->name,
            'product_price' => $general->cur_sym . ' ' . showAmount($this->product->price),
            'bid_amount' => $general->cur_sym . ' ' . showAmount($this->bid->amount),
            'bid_time' => showDateTime($this->bid->created_at) . ' --- ' . diffForHumans($this->bid->created_at),
            'view' => route('api.products.show', $this->product->id)
        ];
    }
}
