<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWithdrawMethodsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $general = GeneralSetting::first();

        return [
            'id' => $this->id,
            'name' => __($this->name),
            'image' => getImage(imagePath()['withdraw']['method']['path'] . '/' . $this->image, imagePath()['withdraw']['method']['size']),
            'limit' => showAmount($this->min_limit) . ' - ' . showAmount($this->max_limit) . ' ' . __($general->cur_text),
            'charge' => showAmount($this->fixed_charge) . ' ' . __($general->cur_text) . ' + ' . showAmount($this->percent_charge) . '%',
            'processing_time' => __($this->delay),
            'currency' => __($general->cur_text),
            'card_title' => __('Withdraw Via') . ' ' . $this->name,
            'card_limit_text' => __('Withdraw Limit') . ': ' . showAmount($this->min_limit) . ' - ' . showAmount($this->max_limit) . ' ' . __($general->cur_text),
            'card_charge_text' => __('Charge') . ': ' . showAmount($this->fixed_charge) . ' ' . __($general->cur_text) . ((0 < showAmount($this->percent_charge)) ? ' + ' . showAmount($this->percent_charge) . ' %' : ''),
        ];
    }
}
