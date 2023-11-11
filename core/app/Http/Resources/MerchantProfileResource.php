<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProfileResource extends JsonResource
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
        $seller_type = $request->merchant_type;

        if ($seller_type == 'admin') {
            $cover_image = getImage(imagePath()['profile']['admin_cover']['path'] . '/' . $general->merchant_profile->cover_image);
            $image = getImage(imagePath()['profile']['admin']['path'] . '/' . $general->merchant_profile->image, null, true);
            $name = __($general->merchant_profile->name);
            $address = __($general->merchant_profile->address);
            $mobile = $general->merchant_profile->mobile;
        } else {
            $cover_image = getImage(imagePath()['profile']['merchant_cover']['path'] . '/' . $this->cover_image);
            $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $this->image, null, true);
            $name = __($this->fullname);
            $address = __($this->address->address);
            $mobile = $this->mobile;
        }

        return [
            'type' => $seller_type,
            'name' => $name,
            'address' => $address,
            'mobile' => $mobile,
            'image' => $image,
            'cover_image' => $cover_image,
            'avg_rating' => $seller_type != 'admin' ? $this->avg_rating : null,
            'review_count' => $seller_type != 'admin' ? $this->review_count : null,
            'since' => showDateTime($this->created_at, 'd M Y'),
            'product_count' => $this->products->where('status', 1)->count(),
            'total_sale' => $this->products->sum('total_bid'),
        ];
    }
}
