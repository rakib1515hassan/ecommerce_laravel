<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPost extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = ["id","video_category_id","title","slug","description","video","created_by_id","is_published","is_approved"];

    public function translations()
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getNameAttribute($name)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[0]->value ?? $name;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', AdditionalServices::default_lang());
                }
            }]);
        });
    }

    public function video_category()
    {
        return $this->belongsTo(VideoCategory::class);
    }

    public function video_comments()
    {
        return $this->hasMany(BlogComment::class, 'video_id');
    }

    public function video_visitors()
    {
        return $this->hasMany(VideoVisitor::class, 'video_id');
    }

    public function created_by_admin()
    {
        // call it when is_created_admin = 1 otherwise created_by_user()
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    public function created_by()
    {
        // call it when is_created_admin = 0 otherwise created_by_admin()
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
