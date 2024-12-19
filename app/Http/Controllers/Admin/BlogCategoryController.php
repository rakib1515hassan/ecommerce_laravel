<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $blog_categories = BlogCategory::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $blog_categories = BlogCategory::where('name', '!=', '');
        }

        $blog_categories = $blog_categories->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.blog_category.view', compact('blog_categories', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Blog Category name is required!',
        ]);

        $blog_category = new BlogCategory;
        $blog_category->name = $request->name;
        $blog_category->slug = Str::slug($request->name);
        $blog_category->is_filterable = $request->is_filterable;
        $blog_category->order = 0;
        $blog_category->save();

        Toastr::success('BlogCategory added successfully!');
        return back();
    }

    public function edit(Request $request, $id)
    {
        $blog_category = BlogCategory::find($id);
        return view('admin-views.blog_category.blog_category-edit', compact('blog_category'));
    }

    public function update(Request $request)
    {
        $blog_category = BlogCategory::find($request->id);
        $blog_category->name = $request->name;
        $blog_category->slug = Str::slug($request->name);
        $blog_category->is_filterable = $request->is_filterable;
        $blog_category->order = 0;

        $blog_category->save();

        Toastr::success('BlogCategory updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        BlogCategory::destroy($request->id);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'BlogCategory deleted successfully!'
            ]
        );
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = BlogCategory::orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

}
