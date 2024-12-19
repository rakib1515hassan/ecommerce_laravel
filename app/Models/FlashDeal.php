<?php

namespace App\Models;

use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FlashDeal extends Model
{
    protected $casts = [
        'product_id' => 'integer',
        'status' => 'integer',
        'featured' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(FlashDealProduct::class, 'flash_deal_id');
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getTitleAttribute($title)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $title;
        }

        return $this->translations[0]->value ?? $title;
    }

    public function is_active(): bool
    {
        return $this->status == 1 && $this->start_date <= date('Y-m-d') && $this->end_date >= date('Y-m-d');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', AdditionalServices::default_lang());
                }
            }]);
        });
    }
}
