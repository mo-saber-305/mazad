<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
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
            'heading' => $this->data_values->heading,
            'subheading' => $this->data_values->subheading,
            'first_button' => $this->data_values->button,
            'second_button' => $this->data_values->link,
            'background_image' => getImage('assets/images/frontend/banner/' . $this->data_values->background_image, '1920x1280'),
        ];
    }
}
