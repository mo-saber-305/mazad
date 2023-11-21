<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $string = $this->data_values->feature_icon;

        $pattern = '/(?:la|fa)-(.*?)"/';

        preg_match($pattern, $string, $matches);
        $new_pattern = (strpos($string, 'fa') !== false) ? 'Fontawesome' : 'Lineawesome';
        if (isset($matches[1])) {
            $result = $matches[1];
        }

        return [
            'title' => __($this->data_values->title),
            'feature_icon' => [
                'name' => $result,
                'package' => $new_pattern,
            ],
        ];
    }
}
