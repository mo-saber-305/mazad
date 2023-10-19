<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeQuickBannerResource extends JsonResource
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
            'url' => $this->data_values->url,
            'image' => getImage('assets/images/frontend/quick_banner/' . $this->data_values->image, '700x400'),
            'button' => $this->data_values->button,
        ];
    }
}
