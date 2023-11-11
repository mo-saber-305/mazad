<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostsRecentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (property_exists($this->data_values, 'blog_image') && $this->data_values->blog_image) {
            $image = getImage('assets/images/frontend/blog/thumb_' . $this->data_values->blog_image, '425x285');
        } else {
            $image = getImage('placeholder-image/425x285', '425x285');
        }
        return [
            'id' => $this->id,
            'title' => __($this->data_values->title),
            'blog_image' => $image,
            'date' => showDateTime($this->created_at),
            'url' => route('api.posts.show', $this->id),
        ];
    }
}
