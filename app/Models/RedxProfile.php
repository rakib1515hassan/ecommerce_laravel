<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedxProfile extends Model
{
    use HasFactory;

    protected $table = 'redx_profiles';
    protected $fillable = [
        'seller_id',
        'redx_id',
        'division_id',
        'district_id',
        'area_id',
        'store_name',
        'phone',
        'address',
    ];

    public function area()
    {
        return $this->belongsTo(AddressArea::class, 'area_id', 'id');
    }
}
