<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressDistrict extends Model
{
    use HasFactory;

    protected $guarded = [];

    // disable timestamps
    public $timestamps = false;

    public function division()
    {
        return $this->belongsTo(AddressDivision::class);
    }
}
