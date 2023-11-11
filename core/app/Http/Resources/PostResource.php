<?php

namespace App\Http\Resources;

use App\Models\Frontend;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            $image = getImage('assets/images/frontend/blog/thumb_' . $this->data_values->blog_image, '850x570');
        } else {
            $image = getImage('placeholder-image/850x570', '850x570');
        }

        return [
            'id' => $this->id,
            'title' => __($this->data_values->title),
            'description_nic' => __($this->data_values->description_nic),
            'blog_image' => $image,
            'date' => showDateTime($this->created_at),
            'date_for_human' => diffForHumans($this->created_at),
            'recent_posts' => $this->recentposts(),
        ];
    }

    public function recentposts()
    {
        $recentBlogs = Frontend::where('data_keys', 'blog.element')->where('id', '!=', $this->id)->latest()->limit(PAGINATION_COUNT)->get();
        return PostsRecentResource::collection($recentBlogs);
    }
}
