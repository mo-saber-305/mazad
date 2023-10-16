<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $this->image, null, true);
        $name = $this->fullname;
        $address = $this->address->address;
        $mobile = $this->mobile;


        return [
            'name' => $name,
            'address' => $address,
            'mobile' => $mobile,
            'image' => $image,
            'avg_rating' => $this->avg_rating,
            'profile_url' => route('api.merchant.profile', ['merchant_type' => 'merchant', 'merchant_id' => $this->id]),
        ];
    }
}
