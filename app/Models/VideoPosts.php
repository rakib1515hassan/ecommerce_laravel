<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPosts extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function created_by()
    {
        // call it when is_created_admin = 0 otherwise created_by_admin()
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id'); // Adjust the foreign key if needed

    }

    public function comments()
    {
        return $this->hasMany(VideoPostComments::class, 'video_post_id');
    }

    public function reactions()
    {
        return $this->hasMany(VideoPostReactions::class, 'video_post_id');

    }
}
