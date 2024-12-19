<?php

namespace App\Http\Controllers\Admin;


use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Models\VideoPosts;
use App\Models\User;
use App\Models\Translation;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function index(Request $request)
    {
        $data['search'] = $request->input('search', '');
        // If there's a search query, filter the posts
        if ($data['search']) {
            $key = explode(' ', $data['search']);
            $video_posts = VideoPosts::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });

        } else {
            // If no search query, retrieve all posts where title is not empty
            $video_posts = VideoPosts::where('title', '!=', '');

        }

        // Retrieve and paginate the posts
        $data['posts'] = $video_posts->latest()->with('user')->paginate(AdditionalServices::pagination_limit());

        return view('admin-views.video_post.view', $data);
    }
    public function delete(Request $request)
    {
        $postId = $request->input('id');

        $post = VideoPosts::find($postId);

        if (!$post) {
            return response()->json(['error' => 'post not found'], 404);
        }

        $post->delete();

        return response()->json(['success' => 'post deleted successfully'], 200);
    }
    public function status(Request $request)
    {
        $video_post = VideoPosts::find($request->id);
        $video_post->is_approved = $request->status;
        $video_post->save();
        return response()->json([], 200);
    }
}
