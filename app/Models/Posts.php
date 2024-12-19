<?php

namespace App\Models;


use App\Services\AdditionalServices;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $guarded = [];

    public function created_by()
    {
        // call it when is_created_admin = 0 otherwise created_by_admin()
        return $this->belongsTo(User::class, 'created_by_id');
    }


    public function comments()
    {
        return $this->hasMany(PostComments::class, 'post_id');
    }

    public function reactions()
    {
        return $this->hasMany(PostReactions::class, 'post_id');
    }

    public function reports()
    {
        return $this->hasMany(PostReports::class, 'post_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id'); // Adjust the foreign key if needed
    }
}
