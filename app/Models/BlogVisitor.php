<?php

namespace App\Models;

use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class BlogVisitor extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'blog_visitors';

    protected $fillable = ["blog_id","visitor_id"];

}
