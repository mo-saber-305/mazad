<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use App\Models\SupportMessage;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTicketResource extends JsonResource
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
        if ($this->status == 0) {
            $status = 'Open';
        } elseif ($this->status == 1) {
            $status = 'Customer Reply';
        } elseif ($this->status == 2) {
            $status = 'Answered';
        } elseif ($this->status == 3) {
            $status = 'Closed';
        }

        if ($this->priority == 1):
            $priority = 'Low';
        elseif ($this->priority == 2):
            $priority = 'Medium';
        elseif ($this->priority == 3):
            $priority = 'High';
        endif;

        return [
            'id' => $this->id,
            'subject' => '[#' . $this->ticket . '] ' . __($this->subject),
            'status' => __($status),
            'priority' => __($priority),
            'last_replay' => Carbon::parse($this->last_reply)->diffForHumans(),
        ];
    }


}
