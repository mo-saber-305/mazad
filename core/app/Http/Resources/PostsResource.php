<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $title = __($this->data_values->title);
        $trim_title = Str::words($title, 7);
        $description = __(strip_tags($this->data_values->description_nic));
        $trim_description = Str::words($description, 20);
        if (property_exists($this->data_values, 'blog_image') && $this->data_values->blog_image) {
            $image = getImage('assets/images/frontend/blog/thumb_' . $this->data_values->blog_image, '425x285');
        } else {
            $image = getImage('placeholder-image/425x285', '425x285');
        }
        return [
            'id' => $this->id,
            'title' => $trim_title,
            'description_nic' => $trim_description,
            'blog_image' => $image,
            'date' => showDateTime($this->created_at),
            'url' => route('api.posts.show', $this->id),
        ];
    }
}
