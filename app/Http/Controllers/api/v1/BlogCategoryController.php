<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function get_blog_categories()
    {
        $categories = BlogCategory::get();

        return response()->json($categories, 200);
    }

    public function get_blog_category_by_id($id)
    {
        $blog_category = BlogCategory::find($id);

        return response()->json($blog_category, 200);
    }

    public function get_blog_category_by_slug($slug)
    {
        $blog_category = BlogCategory::where('slug', $slug)->first();

        return response()->json($blog_category, 200);
    }

    public function getBlogCategories()
    {
        $categories = BlogCategory::select('id', 'name')->get();
        return $this->successResponse('Categories', $categories);
    }
}
