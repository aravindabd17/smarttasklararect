<?php

namespace App\Models;

use App\Models\Scopes\ActiveTaskScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;

class Task extends Model
{
    use SoftDeletes, HasFactory, RefreshDatabase;
    
    protected $fillable = ['title','description','status','attachment','user_id'];

    #[Override]
    protected static function booted()
    {
        static::addGlobalScope(new ActiveTaskScope);
    }
    
    public function scopePending(Builder $query)
    {
        $query->where('status','pending');
    }
    public function scopeCompleted(Builder $query)
    {
        $query->where('status','completed');
    }
    public function scopeSearch(Builder $query,$search)
    {
        $query->where(function($q) use ($search){
            $q->where("title","like","%{$search}%")
            ->orWhere("description","like","%{$search}%");
        });
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function formatDueDate():Attribute
    {
        return Attribute::make(
            get:fn()=>$this->dueDate?$this->dueDate->format('d M Y'):null
        );
    }
    protected function title():Attribute
    {
        return Attribute::make(
            set:fn($value)=>ucwords(strtolower($value))
        );
    }
}
