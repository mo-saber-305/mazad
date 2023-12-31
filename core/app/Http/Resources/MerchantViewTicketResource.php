<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use App\Models\SupportMessage;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantViewTicketResource extends JsonResource
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
            $status = __('Open');
        } elseif ($this->status == 1) {
            $status = __('Customer Reply');
        } elseif ($this->status == 2) {
            $status = __('Answered');
        } elseif ($this->status == 3) {
            $status = __('Closed');
        }

        if ($this->priority == 1):
            $priority = __('Low');
        elseif ($this->priority == 2):
            $priority = __('Medium');
        elseif ($this->priority == 3):
            $priority = __('High');
        endif;

        return [
            'id' => $this->id,
            'subject' => '[' . __('Ticket') . '#' . $this->ticket . '] ' . __($this->subject),
            'status' => $status,
            'priority' => $priority,
            'last_replay' => \Carbon\Carbon::parse($this->last_reply)->diffForHumans(),
            'messages' => $this->messages(),
        ];
    }

    public function messages()
    {
        $messages = SupportMessage::where('supportticket_id', $this->id)->with('ticket', 'attachments')->orderBy('id', 'desc')->get();
        return MerchantTicketMessagesResource::collection($messages);
    }
}
