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
        $relatedProducts = ProductsResource::collection($relatedProducts);
        $bidsCount = $this->bids->count();
        $general = GeneralSetting::first();

        if (auth('api')->check()) {
            if ($this->productDeposits()->where('user_id', auth('api')->user()->id)->first()) {
                $is_deposit = 1;
            } else {
                $is_deposit = 0;
            }
        } else {
            $is_deposit = 0;
        }

        return [
            'id' => $this->id,
            'name' => __($this->name),
            'price' => showAmount($this->price) . ' ' . __($general->cur_text),
            'max_price' => showAmount($this->max_price) . ' ' . __($general->cur_text),
            'deposit_amount' => showAmount(($this->price / 100) * (int)$this->deposit_amount) . ' ' . __($general->cur_text),
            'greater_bid' => $bidsCount ? showAmount($this->bids->max('amount')) . ' ' . __($general->cur_text) : null,
            'total_bid' => $this->total_bid,
            'is_deposit' => $is_deposit,
            'category_id' => $this->category_id,
            'payment_method' => $this->payment_method,
            'started_at' => showDateTime($this->started_at, 'Y_m_d h:i A'),
            'expired_at' => showDateTime($this->expired_at, 'Y_m_d h:i A'),
            'avg_rating' => $this->avg_rating,
            'total_rating' => $this->total_rating,
            'review_count' => $this->review_count,
            'sponsor' => __($this->sponsor),
            'report_file' => $this->report_file ? asset(imagePath()['reports']['path'] . '/' . $this->report_file) : null,
            'file_type' => $this->file_type,
            'image' => $this->file_type == 'image' ? getImage(imagePath()['product']['path'] . '/' . $this->image, imagePath()['product']['size']) : getImage(imagePath()['product']['path'] . '/' . $this->image),
            'short_description' => __($this->short_description),
            'long_description' => __($this->long_description),
            'specification' => $this->specification,
            'user_bid' => $bidsCount,
            'reviews' => $this->reviews(),
            'seller' => $this->sellerData(),
            'related_products' => $relatedProducts,
            'share' => [
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . route('product.details', [$this->id, slug($this->name)]),
                'pinterest' => "https://pinterest.com/pin/create/button/?url=" . route('product.details', [$this->id, slug($this->name)]) . "&description=" . __($this->name) . "&media=" . getImage('assets/images/product/' . $this->main_image),
                'linkedin' => "https://www.linkedin.com/shareArticle?mini=true&url=" . route('product.details', [$this->id, slug($this->name)]) . "&title=" . __($this->name) . "&summary=" . shortDescription(__($this->summary)),
                'twitter' => "https://twitter.com/intent/tweet?text=" . __($this->name) . "%0A" . route('product.details', [$this->id, slug($this->name)]),
            ],
        ];
    }

    public function sellerData(): array
    {
        $general = GeneralSetting::first();
        $seller_type = $this->admin_id == 0 ? 'merchant' : 'admin';

        if ($seller_type == 'admin') {
            $seller = Admin::find($this->admin_id);
            $image = getImage(imagePath()['profile']['admin']['path'] . '/' . $general->merchant_profile->image, null, true);
            $name = __($general->merchant_profile->name);
        } else {
            $seller = Merchant::find($this->merchant_id);
            $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $seller->image, null, true);
            $name = __($seller->fullname);

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

    public function reviews(): array
    {
        $reviews = $this->reviews;
        $data = [];
        foreach ($reviews as $review) {

            $user_type = $this->merchant_id == 0 ? 'merchant' : 'user';

            if ($user_type == 'merchant') {
                $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $review->user->image, null, true);
            } else {
                $image = getImage(imagePath()['profile']['user']['path'] . '/' . $review->user->image, null, true);
            }

            $data[] = [
                'id' => $review->id,
                'rating' => $review->rating,
                'description' => __($review->description),
                'posted_on' => showDateTime($review->created_at),
                'user' => [
                    'name' => __($review->user->fullname),
                    'image' => $image,
                ]
            ];
        }

        return $data;
    }
}
