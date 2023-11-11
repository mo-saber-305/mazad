<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTicketMessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = auth('api')->check() ? auth('api')->user()->image : '';
        if ($this->admin_id == 0) {
            $user_image = getImage(imagePath()['profile']['user']['path'] . '/' . $image, null, true);
        } else {
            $user_image = getImage(imagePath()['profile']['admin']['path'] . '/' . $this->admin->image, null, true);
        }

        return [
            'id' => $this->id,
            'user_image' => $user_image,
            'user_name' => __($this->ticket->name),
            'post_on' => $this->created_at->format('l, dS F Y @ H:i'),
            'message' => $this->message,
            'attachments' => $this->attachments(),
        ];
    }


    public function attachments()
    {
        $data = [];
        foreach ($this->attachments as $item) {
            $data[] = [
                'id' => $item->id,
                'attachment' => $item->attachment,
                'download' => route('ticket.download', encrypt($item->id))
            ];
        }

        return $data;
    }
}
