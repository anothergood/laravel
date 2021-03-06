<?php

namespace App\Http\Resources;

use App\Traits\GetLocalizationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    use GetLocalizationTrait;
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'body' => $this->getLocalizedField('body'),
            'attachments' => $this->attachments,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
