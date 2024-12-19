<?php

namespace App\Models;


use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = ["id","name","slug","is_filterable","order"];

    public function blog_posts()
    {
        return $this->hasMany(Posts::class);
    }
}
