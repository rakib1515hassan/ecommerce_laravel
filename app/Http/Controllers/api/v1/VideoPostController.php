<?php

namespace App\Http\Controllers\api\v1;


use App\Models\User;
use App\Models\Product;
use App\Models\VideoPost;
use App\Models\VideoVisitor;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use App\Services\ImageManager;
use Illuminate\Http\JsonResponse;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function get_video_posts(): JsonResponse
    {
        $video_posts = VideoPost::with('created_by', 'video_category')->withCount('video_comments')->withCount('blog_visitors')->get()->map(function ($blog_post) {
            $video_post['creator'] = $video_post['created_by']['name'];
            unset($video_post['created_by']);
            return $video_post;
        });

        return response()->json($video_posts, 200);
    }

    public function get_video_post_by_id($id): JsonResponse
    {
        $video_post = VideoPost::with('created_by')->find($id);
        return response()->json($video_post, 200);
    }

    public function get_video_post_by_slug($slug): JsonResponse
    {
        $video_post = VideoPost::with('created_by')->where('slug', $slug)->first();
        return response()->json($video_post, 200);
    }

    public function get_video_posts_by_blog_category_id($video_category_id): JsonResponse
    {
        $video_posts = VideoPost::with('created_by')->where('video_category_id', $video_category_id)->get();

        return response()->json($video_posts, 200);
    }

    public function get_video_posts_by_video_category_slug($video_category_slug): JsonResponse
    {
        $video_category = VideoCategory::where('slug', $video_category_slug)->first();

        $video_posts = [];

        if ($video_category)
            $video_posts = VideoPost::with('created_by')->where('video_category_id', $video_category->id)->get();

        return response()->json($video_posts, 200);
    }

    public function random_video_posts(Request $request): JsonResponse
    {
        if ($request->has('limit'))
            $limit = $request->limit;
        else
            $limit = 12;

        $video_posts = BlogPost::inRandomOrder()->paginate($limit);

        return response()->json($video_posts, 200);
    }

    public function categoryWiseVideoPosts(Request $request): JsonResponse
    {
        $data['video_categories'] = VideoCategory::all();

        $data['video_posts'] = BlogPost::with('created_by')->paginate(10);

        return response()->json($data, 200);
    }

    public function save_video_post(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'video_category_id' => 'required',
            'title' => 'required|min:6',
            'description' => 'required',
            'video' => 'mimes:mp4,mov,avi,wmv,flv|max:10240',
        ]);;

        if (!Auth::check()) {
            $validator->errors()->add('customer_id', 'Customer not found');
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $video_post = new VideoPost;
        $video_post->video_category_id = $request->video_category_id;
        $video_post->title = $request->title;
        $video_post->slug = Str::slug($request->title);
        $video_post->description = $request->description;
        // $video_post->video = Storage::putFile('video_post/', $request->file('video'));
        $video_post->video = ImageManager::upload('video_post/', 'mp4', $request->file('video'));

        $auth_customer = Auth::user();
        $video_post->created_by_id = $auth_customer->id;

        $video_post->is_published = 0;
        $video_post->is_approved = 0;
        $video_post->save();

        $video_visitor = new VideoVisitor;
        $video_visitor->video_id = $video_post->id;
        $video_visitor->visitor_id = $video_post->created_by_id;
        $video_visitor->save();

        return response()->json($video_post, 200);
    }

    public function updateVideoPost(Request $request, $videoId)
    {
        $validator = Validator::make($request->all(), [
            'video_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|min:6',
            'description' => 'required'
        ]);

        if (!Auth::check()) {
            $validator->errors()->add('customer_id', 'Customer not found');
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $video_post = VideoPost::find($videoId);
        if ($video_post) {
            $video_post->video_category_id = $request->video_category_id;
            $video_post->title = $request->title;
            $video_post->slug = Str::slug($request->title);
            if ($request->file('video')) {
                $video_post->video = Storage::putFile('video_post/', $request->file('video'));
            }
            $video_post->save();

            return response()->json($video_post, 200);
        } else {
            return response()->json([]);
        }
    }

    public function get_products(Request $request): JsonResponse
    {
        $request->validate([
            'blog_video_slug' => 'required'
        ]);


        $query = Product::query();
        collect(explode("-", $request->video_post_slug))->map(function ($item) use ($query) {
            if ($item != "") {
                $query->orWhere('name', 'like', '%' . $item . '%');
            }
        });

        $limit = 10;
        if ($request->has('limit'))
            $limit = $request->limit;

        $products = $query->active()->paginate($limit);
        return response()->json($products, 200);
    }

    public function authUserVideoPosts()
    {
        $video_posts = User::findOrFail(Auth::id())
            ->blogPosts()
            ->withCount('video_comments')
            ->withCount('video_visitors')
            ->get();

        return response()->json($video_posts, 200);
    }

    public function searchVideoByTitle($title)
    {
        $videoPost = VideoPost::where('title', 'like', "%{$title}%")->where('created_by_id', Auth::id())->get();
        if ($videoPost->count() > 0) {
            return \response()->json($videoPost, 200);
        } else {
            return \response()->json([], 200);
        }
    }

    public function destroyVideoPost($videoId)
    {
        $video = VideoPost::find($videoId);
        Storage::delete("video_post/" . $video->video);
        $video->delete();
        return $this->successResponse('Successfully Deleted');
    }

    public function makeShareLink($videoId)
    {
        try {
            $videopost = VideoPost::findOrFail($videoId);
            $link = asset("/video/" . $videopost->slug);
            return response()->json($link, 200);
        } catch (\Exception $e) {
            return response()->json("Post Not Found!", 200);
        }
    }
}
