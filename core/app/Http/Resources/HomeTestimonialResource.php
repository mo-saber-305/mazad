<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeTestimonialResource extends JsonResource
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
            'image' => getImage('assets/images/frontend/testimonial/' . $this->data_values->user_image, '120x120'),
            'name' => $this->data_values->name,
            'designation' => $this->data_values->designation,
            'star' => $this->data_values->star,
        ];
    }
}
