<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $casts = [
        'exchange_rate' => 'float',
        'status'        => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
    protected $table = 'currencies';

}
