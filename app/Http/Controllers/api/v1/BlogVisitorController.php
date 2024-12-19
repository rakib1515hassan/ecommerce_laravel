<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogVisitor;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class BlogVisitorController extends Controller
{
    public function get_blog_visitors()
    {
        $blog_visitors = BlogVisitor::get();

        return response()->json($blog_visitors, 200);
    }

    public function get_blog_visitor_by_id($id)
    {
        $blog_visitor = BlogVisitor::find($id);

        return response()->json($blog_visitor, 200);
    }

    public function get_blog_visitors_by_blog_post_id($blog_post_id)
    {
        $blog_visitors = BlogVisitor::where('blog_id', $blog_post_id)->get();

        return response()->json($blog_visitors, 200);
    }

    public function get_blog_visitors_by_blog_post_slug($blog_post_slug)
    {
        $blog_post = BlogPost::where('slug', $blog_post_slug)->first();

        $blog_visitors = [];

        if($blog_post)
            $blog_visitors = BlogVisitor::where('blog_id', $blog_post->id)->get();

        return response()->json($blog_visitors, 200);
    }



}
