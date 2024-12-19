<?php

namespace App\Models;

use App\Services\AdditionalServices;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class BlogPost extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'blog_posts';

    protected $fillable = ["id","blog_category_id","title","slug","content","seo_title","seo_description","seo_keywords","seo_image_id","thumbnail_id","created_by_id","is_created_admin","is_published","is_approved","is_featured","is_commentable"];

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

    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function blog_comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    public function blog_visitors()
    {
        return $this->hasMany(BlogVisitor::class, 'blog_id');
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
