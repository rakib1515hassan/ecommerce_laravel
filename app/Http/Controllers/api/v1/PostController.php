<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\PostComments;
use App\Models\Posts;
use App\Services\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    public function index(Request $request): JsonResponse
    {
        $posts = Posts::where('is_published', true)->latest()->paginate(10);


        return response()->json($posts, 200);
    }

    public function userPosts(Request $request): JsonResponse
    {
        $posts = Posts::where('created_by_id', Auth::guard('api')->id())->latest()->paginate(10);
        return response()->json($posts, 200);
    }

    public function show($slug): JsonResponse
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->with('created_by', 'comments', 'reactions')->first();
        // total comment
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $post->total_comment = $post->comments->count();
        $post->total_reaction = $post->reactions->where('is_liked', 1)->count();
        $post->is_liked = (bool)$post->reactions->where('user_id', Auth::guard('api')->id())->where('is_liked', 1)->first();


        return response()->json($post, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $post = new Posts();
        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->description = $request->description;
        $post->created_by_id = Auth::guard('api')->id();
        if ($request->hasFile('image')) {
            $post->image = ImageManager::upload("post_image", 'jpeg', $request->file('image'));
        }
        $post->save();

        return response()->json($post, 201);
    }


    public function edit(Posts $post)
    {
        return response()->json($post, 200);
    }

    public function update($slug, Request $request): JsonResponse
    {
        $post = Posts::where('slug', $slug)->where('created_by_id', Auth::guard('api')->id())->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);


        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->description = $request->description;
        $post->created_by_id = Auth::guard('api')->id();
        if ($request->hasFile('image')) {
            $post->image = ImageManager::update("post_image", $post->image, 'jpeg', $request->file('image'));
        }
        $post->save();

        return response()->json($post, 200);
    }


    public function destroy($slug): JsonResponse
    {
        $post = Posts::where('slug', $slug)->where('created_by_id', Auth::guard('api')->id())->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $post->delete();
        return response()->json(null, 204);
    }


    public function showComments($slug)
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->with('comments')->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $comments = PostComments::where('post_id', $post->id)->with('user')->latest()->paginate(10);
        return response()->json($comments, 200);
    }

    public function storeComment($slug, Request $request): JsonResponse
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'comment' => 'required',
        ]);

        $comment = $post->comments()->create([
            'comment' => $request->comment,
            'user_id' => Auth::guard('api')->id(),
        ]);

        return response()->json($comment, 201);
    }


    public function destroyComment($slug, $commentId): JsonResponse
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $comment = $post->comments()->where('id', $commentId)->where('user_id', Auth::guard('api')->id())->first();
        if (!$comment) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $comment->delete();
        return response()->json([
            'message' => 'Deleted'
        ], 204);
    }


    public function storeReaction($slug, Request $request): JsonResponse
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'is_liked' => 'required|boolean',
        ]);

        $reaction = $post->reactions()->updateOrCreate(
            ['user_id' => Auth::guard('api')->id()],
            ['is_liked' => $request->is_liked]
        );

        return response()->json($reaction, 201);
    }


    public function reportPost($slug, Request $request): JsonResponse
    {
        $post = Posts::where('slug', $slug)->orWhere('id', $slug)->first();
        if (!$post) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'report' => 'required',
        ]);


        return response()->json($post->reports()->create([
            'report' => $request->report,
            'user_id' => Auth::guard('api')->id(),
        ]), 201);
    }

}
