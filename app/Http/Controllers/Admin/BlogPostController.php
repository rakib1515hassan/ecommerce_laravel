<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogVisitor;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $data['search'] = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $blog_posts = BlogPost::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $blog_posts = BlogPost::where('title', '!=', '');
        }

        $data['blog_posts'] = $blog_posts->latest()->with('blog_category')->with('created_by_admin')->with('created_by')->paginate(AdditionalServices::pagination_limit())->appends($query_param);

        $data['blog_categories'] = BlogCategory::latest()->get();

        return view('admin-views.blog_post.view', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required',
            'title' => 'required|unique:blog_posts',
            'content' => 'required',
            'thumbnail' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'required',
            'is_approved' => 'required',
            'is_featured' => 'required',
            'is_commentable' => 'required',
        ]);

        $blog_post = new BlogPost;
        $blog_post->blog_category_id = $request->blog_category_id;
        $blog_post->title = $request->title;
        $blog_post->slug = Str::slug($request->title);
        $blog_post->content = $request->get('content');
        $blog_post->thumbnail = ImageManager::upload('blog_post/', 'png', $request->file('thumbnail'));

        $blog_post->created_by_id = auth('admin')->user()->id;
        $blog_post->is_created_admin = 1;

        $blog_post->is_published = $request->is_published;
        $blog_post->is_approved = $request->is_approved;
        $blog_post->is_featured = $request->is_featured;
        $blog_post->is_commentable = $request->is_commentable;
        $blog_post->save();

        Toastr::success('Blog Posts added successfully!');

        return back();
    }

    public function edit(Request $request, $id)
    {
        $data['blog_post'] = BlogPost::find($id);

        $data['blog_categories'] = BlogCategory::latest()->get();

        return view('admin-views.blog_post.edit', $data);
    }

    public function update(Request $request)
    {
        $blog_post = BlogPost::find($request->id);
        $blog_post->blog_category_id = $request->blog_category_id;
        $blog_post->title = $request->title;
        $blog_post->slug = Str::slug($request->title);
        $blog_post->content = $request->get('content');
        if ($request->has('thumbnail')) {
            $blog_post->thumbnail = ImageManager::update('blog_post/', $blog_post['thumbnail'], 'png', $request->file('thumbnail'));
        }

        $blog_post->is_published = $request->is_published;
        $blog_post->is_approved = $request->is_approved;
        $blog_post->is_featured = $request->is_featured;
        $blog_post->is_commentable = $request->is_commentable;

        $blog_post->save();

        Toastr::success('Blog Posts updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        BlogPost::destroy($request->id);

        return response()->json();
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = BlogPost::orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }


    public function bloge_create(Request $request)
    {
        $data['blog_categories'] = BlogCategory::latest()->get();
        return view('admin-views.blog_post.bolg_create', $data);
    }


}
