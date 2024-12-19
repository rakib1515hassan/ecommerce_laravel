<?php

namespace App\Models;


use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPostComments extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = ["comment","is_reply","is_approved","customer_id","created_by_id"];

    protected $with = array('created_by_admin', 'created_by_user');

    public function blog_post()
    {
        return $this->belongsTo(Posts::class, 'customer_id');
    }

    public function created_by_admin()
    {
        // call it when is_created_admin = 1 otherwise created_by_user()
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    public function created_by_user()
    {
        // call it when is_created_admin = 0 otherwise created_by_admin()
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
