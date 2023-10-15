<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'total_bid' => $this->total_bid,
            'started_at' => showDateTime($this->started_at, 'd-m-Y h:i A'),
            'expired_at' => showDateTime($this->expired_at, 'd-m-Y h:i A'),
            'image' => getImage(imagePath()['product']['path'] . '/' . $this->image, imagePath()['product']['size']),
        ];
    }
}
