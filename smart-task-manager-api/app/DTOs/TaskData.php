<?php
namespace App\DTOs;

use App\Http\Requests\StoreTaskRequest;

class TaskData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly mixed $attachment,
        public readonly string $status,
        public readonly int $userid,
    ){}

    public static function fromRequest(StoreTaskRequest $request):self
    {
        return new self(
            title:$request->title,
            description:$request->description,
            attachment:$request->attachment,
            status:$request->status,
            userid:auth()->id(),
        );
    }
    public function toArray():array
    {
        return [
            'title'=>$this->title,
            'description'=>$this->description,
            'attachment'=>$this->attachment,
            'status'=>$this->status,
            'user_id'=>auth()->id(),
        ];
    }
}