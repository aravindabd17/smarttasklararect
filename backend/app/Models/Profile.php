<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id','age','dob',"city","education","experience","role","image"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
