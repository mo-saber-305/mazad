<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWinnersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->product_delivered == 0):
            $status = 'Pending';
        else:
            $status = 'Delivered';
        endif;

        return [
            'id' => $this->id,
            'product_name' => $this->product->name,
            'product_id' => $this->product->id,
            'winning_date' => showDateTime($this->created_at),
            'product_delivered' => __($status),
            'user_name' => __($this->user->fullname),
            'user_email' => $this->user->email,
            'user_mobile' => $this->user->mobile,
            'user_address' => __($this->user->address->address),
            'user_state' => __($this->user->address->state),
            'user_zip' => $this->user->address->zip,
            'user_city' => __($this->user->address->city),
            'user_country' => __($this->user->address->country),
        ];
    }
}
