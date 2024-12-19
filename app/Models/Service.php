<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceCategory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'short_description', 'description', 'price', 'category_id'];

    // Define relationships
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function features()
    {
        return $this->hasMany(ServiceFeature::class);
    }
}
