<?php

namespace App\Models;

use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BlogCategory extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'blog_categories';

    protected $fillable = ["id","name","slug","is_filterable","order"];

    public function blog_posts()
    {
        return $this->hasMany(BlogPost::class);
    }


}
