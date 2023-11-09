<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status == 1) {
            $status = __('Active');
        } else {
            $status = __('Inactive');
        }

        return [
            'id' => $this->id,
            'type' => __($this->type),
            'value' => getImage(imagePath()['advertisement']['path'] . '/' . $this->value),
            'status' => $status,
            'size' => __($this->size),
            'redirect_url' => $this->redirect_url,
            'impression' => $this->impression,
            'click' => $this->click,
        ];
    }
}
