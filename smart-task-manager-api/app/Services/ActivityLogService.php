<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }
    public function log(
        string $action,
        Model $model,
        string $description,
        array $properties=[]
    )
    {
        ActivityLog::create([
            'user_id'=>auth()->id(),
            'action'=>$action,
            'description'=>$description,
            'subject_type'=>class_basename($model),
            'subject_code'=>$model->id,
            'properties'=>$properties
        ]);
    }
}
