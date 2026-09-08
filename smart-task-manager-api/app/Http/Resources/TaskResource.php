<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'status'=>$this->status,
            'attachment'=>$this->attachment,
            'attachment_url'=>$this->attachment?Storage::url($this->attachment):null,
            'user'=>new UserResource($this->whenLoaded('user')),
            'user_id'=>$this->user_id,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
