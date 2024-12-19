<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

class BlogCommentController extends Controller
{
    public function get_blog_comments(Request $request)
    {
        $request->validate([
            'blog_post_id' => 'required|integer'
        ]);

        $blog_comments = BlogComment::with('created_by_user')->where('blog_id', $request->blog_post_id)->get()->map(function ($blog_comment) {
            $blog_comment['created_by'] = $blog_comment['created_by_user']->name;
            $blog_comment['approve_by'] = $blog_comment['created_by_admin']->name;

            unset($blog_comment['created_by_user']);
            unset($blog_comment['created_by_admin']);
            return $blog_comment;
        });

        return response()->json($blog_comments, 200);
    }

    public function get_blog_comment_by_id($id)
    {
        $blog_comment = BlogComment::find($id);

        return response()->json($blog_comment, 200);
    }

    public function get_blog_comments_by_blog_post_id($blog_post_id)
    {
        $blog_comments = BlogComment::where('blog_id', $blog_post_id)->get();

        return response()->json($blog_comments, 200);
    }

    public function get_blog_comments_by_blog_post_slug($blog_post_slug)
    {
        $blog_post = BlogPost::where('slug', $blog_post_slug)->first();

        $blog_comments = [];

        if ($blog_post)
            $blog_comments = BlogComment::where('blog_id', $blog_post->id)->get();

        return response()->json($blog_comments, 200);
    }

    public function save_blog_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_post_id' => 'required|exists:blog_posts,id',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $blog_post = BlogPost::find($request->blog_post_id);
        if (!$blog_post->is_commentable) {
            return response()->json(['errors' => ['code' => 1, 'message' => 'The blog post is not commentable!']], 403);
        }

        $blog_comment = new BlogComment;
        $blog_comment->blog_id = $request->blog_post_id;
        $blog_comment->comment = $request->comment;
        $blog_comment->created_by_id = Auth::id();
        $blog_comment->is_approved = 0;

        $blog_comment->save();

        return response()->json($blog_comment, 200);
    }


}
