<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $string = $this->icon;

        $pattern = '/(?:la|fa)-(.*?)"/';

        preg_match($pattern, $string, $matches);
        $new_pattern = (strpos($string, 'fa') !== false) ? 'fontawesome' : 'line-awesome';
        if (isset($matches[1])) {
            $result = $matches[1];
        }

        return [
            'id' => $this->id,
            'name' => __($this->name),
            'icon' => [
                'name' => $result,
                'package' => $new_pattern,
            ],
            'products_count' => $this->products_count,
            'url' => route('api.products.index', ['category_id' => $this->id]),
        ];
    }
}
