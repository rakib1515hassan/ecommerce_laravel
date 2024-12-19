<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressArea extends Model
{
    use HasFactory;


    protected $guarded = [];


    public function district()
    {
        return $this->belongsTo(AddressDistrict::class);
    }

    public function division()
    {
        return $this->belongsTo(AddressDivision::class);
    }

    public function zone()
    {
        return $this->belongsTo(AddressZone::class);
    }

    public function get_full_address()
    {
        return $this->belongsTo(AddressZone::class);
    }

}
