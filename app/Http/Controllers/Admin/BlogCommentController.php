<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Models\BlogVisitor;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class BlogCommentController extends Controller
{
    public function blog_comments_by_blog_post_id($id, Request $request)
    {
        $data['blog_post'] = BlogPost::with('blog_category')->with('created_by_admin')->with('created_by_user')->with('blog_comments')->find($id);

        $data['replies'] = BlogComment::where(['blog_id' => $id, 'is_reply' => 1])->get();

        return view('admin-views.blog_comments.edit', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required',

        ]);

        $blog_comment = new BlogComment;

        $blog_comment->save();

        Toastr::success('Blog Posts added successfully!');

        return back();
    }

    public function edit(Request $request, $id)
    {
        $data['blog_comment'] = BlogComment::find($id);

        $data['blog_categories'] = BlogCategory::latest()->get();

        return view('admin-views.blog_comment.edit', $data);
    }

    public function update(Request $request)
    {
        $blog_comment = BlogComment::find($request->id);
        $blog_comment->blog_category_id = $request->blog_category_id;


        $blog_comment->save();

        Toastr::success('Blog Posts updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        BlogComment::destroy($request->id);

        return response()->json();
    }

    public function delete_comment(Request $request)
    {
        BlogComment::destroy($request->comment_id);

        return response()->json(['success'=>1, 'message'=>'Comment deleted successfully!']);
    }

    public function reply_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        $blog_comment = new BlogComment;
        $blog_comment->comment = $request->comment;
        $blog_comment->parent_id = $request->blog_comment_id;
        $blog_comment->blog_id = $request->blog_id;
        $blog_comment->is_reply = 1;
        $blog_comment->created_by_id = auth('admin')->user()->id;
        $blog_comment->is_created_admin = 1;
        $blog_comment->is_approved = 1;

        $blog_comment->save();

        Toastr::success('Reply added successfully!');

        return back();

    }

    public function update_approvement(Request $request)
    {
        if($request->ajax()){


            $flag = ($request->status=='true') ? 1 : 0;

            $result = BlogComment::find($request->comment_id)->update([
                'is_approved' => $flag,
            ]);

            return response()->json(['message'=> translate('Status updated success.')], 200);
        }
    }



}
