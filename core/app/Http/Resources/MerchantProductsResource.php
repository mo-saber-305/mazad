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
            $status = __('Pending');
        elseif ($this->status == 1 && $this->started_at < now() && $this->expired_at > now()):
            $status = __('Live');
        elseif ($this->status == 1 && $this->started_at > now()):
            $status = __('Upcoming');
        else:
            $status = __('Expired');
        endif;

        return [
            'id' => $this->id,
            'name' => __($this->name),
            'category' => __($this->category->name),
            'price' => $this->price,
            'total_bid' => $this->total_bid,
            'status' => $status,
        ];
    }
}
