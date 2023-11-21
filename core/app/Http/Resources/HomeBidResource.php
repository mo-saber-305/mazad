<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeBidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $string = $this->data_values->icon;

        $pattern = '/(?:la|fa)-(.*?)"/';

        preg_match($pattern, $string, $matches);
        $new_pattern = (strpos($string, 'fa') !== false) ? 'Fontawesome' : 'Lineawesome';
        if (isset($matches[1])) {
            $result = $matches[1];
        }

        return [
            'heading' => __($this->data_values->heading),
            'icon' => [
                'name' => $result,
                'package' => $new_pattern,
            ],
        ];
    }
}
