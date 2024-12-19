<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressDivision extends Model
{
    use HasFactory;

    protected $guarded = [];

    // disable timestamps
    public $timestamps = false;
}
