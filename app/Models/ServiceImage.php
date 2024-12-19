<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'image_path'];

    // Define the inverse relationship to Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
