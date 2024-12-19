<?php

namespace App\Http\Controllers\Admin;


use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Models\BlogCategory;
use App\Models\Posts;
use App\Models\User;
use App\Models\BlogVisitor;
use App\Models\Translation;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $data['search'] = $request->input('search', '');

        // If there's a search query, filter the posts
        if ($data['search']) {
            $key = explode(' ', $data['search']);
            $blog_posts = Posts::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });

        } else {
            // If no search query, retrieve all posts where title is not empty
            $blog_posts = Posts::where('title', '!=', '');

        }

        // Retrieve and paginate the posts
        $data['posts'] = $blog_posts->latest()->with('user')->paginate(AdditionalServices::pagination_limit());

        return view('admin-views.post.view', $data);
    }
    public function delete(Request $request)
    {
        $postId = $request->input('id');

        $post = Posts::find($postId);

        if (!$post) {
            return response()->json(['error' => 'post not found'], 404);
        }

        $post->delete();

        return response()->json(['success' => 'post deleted successfully'], 200);
    }
    public function status(Request $request)
    {
        $post = Posts::find($request->id);
        $post->is_approved = $request->status;
        $post->save();
        return response()->json([], 200);
    }
}
