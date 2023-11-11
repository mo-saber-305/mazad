<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeCounterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $string = $this->data_values->counter_icon;

        $pattern = '/(?:la|fa)-(.*?)"/';

        preg_match($pattern, $string, $matches);
        $new_pattern = (strpos($string, 'fa') !== false) ? 'fontawesome' : 'line-awesome';
        if (isset($matches[1])) {
            $result = $matches[1];
        }
        return [
            'counter_digit' => $this->data_values->counter_digit,
            'title' => __($this->data_values->title),
            'icon' => [
                'name' => $result,
                'package' => $new_pattern,
            ],
        ];
    }
}
