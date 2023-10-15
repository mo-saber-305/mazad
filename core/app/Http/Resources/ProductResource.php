<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $relatedProducts = Product::live()->where('category_id', $this->category_id)->where('id', '!=', $this->id)->limit(10)->get();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'total_bid' => $this->total_bid,
            'started_at' => showDateTime($this->started_at, 'd-m-Y h:i A'),
            'expired_at' => showDateTime($this->expired_at, 'd-m-Y h:i A'),
            'avg_rating' => $this->avg_rating,
            'total_rating' => $this->total_rating,
            'review_count' => $this->review_count,
            'image' => getImage(imagePath()['product']['path'] . '/' . $this->image, imagePath()['product']['size']),
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'specification' => $this->specification,
            'reviews' => $this->reviews,
            'seller' => $this->sellerData(),
            'related_products' => $relatedProducts,
        ];
    }

    public function sellerData(): array
    {
        $general = GeneralSetting::first();
        $seller_type = $this->admin_id == 0 ? 'merchant' : 'admin';

        if ($seller_type == 'admin') {
            $seller = Admin::find($this->admin_id);
            $image = getImage(imagePath()['profile']['admin']['path'] . '/' . $general->merchant_profile->image, null, true);
            $name = $general->merchant_profile->name;
        } else {
            $seller = Merchant::find($this->merchant_id);
            $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $seller->image, null, true);
            $name = $seller->fullname;

        }

        return [
            'type' => $seller_type,
            'name' => $name,
            'image' => $image,
            'avg_rating' => $seller_type != 'admin' ? $seller->avg_rating : null,
            'review_count' => $seller_type != 'admin' ? $seller->review_count : null,
            'since' => showDateTime($seller->created_at, 'd M Y'),
            'product_count' => $seller->products->where('status', 1)->count(),
            'total_sale' => $seller->products->sum('total_bid'),
        ];
    }
}
