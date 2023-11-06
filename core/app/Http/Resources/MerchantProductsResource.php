<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status == 0 && $this->expired_at > now()):
            $status = 'Pending';
        elseif ($this->status == 1 && $this->started_at < now() && $this->expired_at > now()):
            $status = 'Live';
        elseif ($this->status == 1 && $this->started_at > now()):
            $status = 'Upcoming';
        else:
            $status = 'Expired';
        endif;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->name,
            'price' => $this->price,
            'total_bid' => $this->total_bid,
            'status' => $status,
        ];
    }
}
