<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status == 1):
            $status = __('Active');
        elseif ($this->status == 0):
            $status = __('Banned');
        endif;

        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'country_code' => $this->country_code,
            'mobile' => $this->mobile,
            'balance' => $this->balance,
            'image' => getImage(imagePath()['profile']['user']['path'] . '/' . $this->image, null, true),
            'address' => __($this->address),
            'status' => $status,
            'ev' => $this->ev,
            'sv' => $this->sv,
            'ver_code' => $this->ver_code,
            'ver_code_send_at' => $this->ver_code_send_at,
            'ts' => $this->ts,
            'tv' => $this->tv,
            'tsc' => $this->tsc,
            'interests' => $this->interests(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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

    public function interests()
    {
        $interests = $this->interests;
        return InterestsResource::collection($interests);
    }

    public function reviews(): array
    {
        $reviews = $this->reviews;
        $data = [];
        foreach ($reviews as $review) {
            $data['id'] = $review->id;
            $data['rating'] = $review->rating;
            $data['description'] = $review->description;
            $data['posted_on'] = showDateTime($review->created_at);
            $user_type = $this->merchant_id == 0 ? 'merchant' : 'user';

            if ($user_type == 'merchant') {
                $user = Merchant::find($this->merchant_id);
                $image = getImage(imagePath()['profile']['merchant']['path'] . '/' . $review->user->image, null, true);
            } else {
                $user = User::find($this->user_id);
                $image = getImage(imagePath()['profile']['user']['path'] . '/' . $review->user->image, null, true);

            }

            $data['user'] = [
                'name' => $review->user->fullname,
                'image' => $image,
            ];
        }

        return $data;
    }
}
