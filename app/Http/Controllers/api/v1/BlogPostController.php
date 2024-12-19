<?php

namespace App\Http\Controllers\api\v1;

use App\Models\User;
use App\Models\Product;
use App\Models\BlogPost;
use App\Models\BlogVisitor;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Services\ImageManager;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogPostController extends Controller
{
    // public function get_blog_posts(): JsonResponse
    // {
    //     $blog_posts = BlogPost::with('created_by', 'blog_category')
    //         ->withCount('blog_comments')
    //         ->withCount('blog_visitors')
    //         ->get()->
    //         map(function ($blog_post) {
    //             $blog_post['creator'] = $blog_post['created_by']['name'];
    //             unset($blog_post['created_by']);
    //             return $blog_post;
    //         });

    //     return response()->json($blog_posts, 200);
    // }


    public function get_blog_posts(): JsonResponse
    {
        $blog_posts = BlogPost::with('created_by', 'blog_category')
            ->withCount('blog_comments')
            ->withCount('blog_visitors')
            ->where('is_approved', true)
            ->where('is_published', true)
            ->get()
            ->map(function ($blog_post) {
                $blog_post['creator'] = $blog_post['created_by']['name'];
                unset ($blog_post['created_by']);
                return $blog_post;
            });

        return response()->json($blog_posts, 200);
    }

    public function get_blog_post_by_id($id): JsonResponse
    {
        $blog_post = BlogPost::with('created_by')->find($id);
        return response()->json($blog_post, 200);
    }

    public function get_blog_post_by_slug($slug): JsonResponse
    {
        $blog_post = BlogPost::with('created_by')->where('slug', $slug)->first();
        return response()->json($blog_post, 200);
    }

    public function get_blog_posts_by_blog_category_id($blog_category_id): JsonResponse
    {
        $blog_posts = BlogPost::with('created_by')->where('blog_category_id', $blog_category_id)->get();

        return response()->json($blog_posts, 200);
    }

    public function get_blog_posts_by_blog_category_slug($blog_category_slug): JsonResponse
    {
        $blog_category = BlogCategory::where('slug', $blog_category_slug)->first();

        $blog_posts = [];

        if ($blog_category)
            $blog_posts = BlogPost::with('created_by')->where('blog_category_id', $blog_category->id)->get();

        return response()->json($blog_posts, 200);
    }

    public function random_blog_posts(Request $request): JsonResponse
    {
        if ($request->has('limit'))
            $limit = $request->limit;
        else
            $limit = 12;

        $blog_posts = BlogPost::inRandomOrder()->paginate($limit);

        return response()->json($blog_posts, 200);
    }

    public function categoryWiseBlogPosts(Request $request): JsonResponse
    {
        $data['blog_categories'] = BlogCategory::all();

        $data['blog_posts'] = BlogPost::with('created_by')->paginate(10);

        return response()->json($data, 200);
    }

    public function save_blog_post(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required',
            'title' => 'required|min:6',
            'content' => 'required',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        ;

        if (!Auth::check()) {
            $validator->errors()->add('customer_id', 'Customer not found');
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $blog_post = new BlogPost;
        $blog_post->blog_category_id = $request->blog_category_id;
        $blog_post->title = $request->title;
        $blog_post->slug = Str::slug($request->title);
        $blog_post->content = $request->get('content');
        $blog_post->thumbnail = ImageManager::upload('blog_post/', 'png', $request->file('thumbnail'));

        $auth_customer = Auth::user();
        $blog_post->created_by_id = $auth_customer->id;
        $blog_post->is_created_admin = 0;

        $blog_post->is_published = 0;
        $blog_post->is_approved = 0;
        $blog_post->is_featured = 0;
        $blog_post->is_commentable = 0;
        $blog_post->save();

        $blog_visitor = new BlogVisitor;
        $blog_visitor->blog_id = $blog_post->id;
        $blog_visitor->visitor_id = $blog_post->created_by_id;
        $blog_visitor->save();

        return response()->json($blog_post, 200);
    }

    public function updateBlogPost(Request $request, $blogId)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|min:6',
            'content' => 'required'
        ]);

        if (!Auth::check()) {
            $validator->errors()->add('customer_id', 'Customer not found');
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $blog_post = BlogPost::find($blogId);
        if ($blog_post) {
            $blog_post->blog_category_id = $request->blog_category_id;
            $blog_post->title = $request->title;
            $blog_post->slug = Str::slug($request->title);
            $blog_post->content = $request->get('content');
            if ($request->file('thumbnail')) {
                $blog_post->thumbnail = ImageManager::update('blog_post/', $blog_post->thumbnail, 'png', $request->file('thumbnail'));
            }
            $blog_post->save();

            return response()->json($blog_post, 200);
        } else {
            return response()->json([]);
        }
    }

    public function get_products(Request $request): JsonResponse
    {
        $request->validate([
            'blog_post_slug' => 'required'
        ]);


        $query = Product::query();
        collect(explode("-", $request->blog_post_slug))->map(function ($item) use ($query) {
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

    public function authUserBlogPosts()
    {
        $blog_posts = User::findOrFail(Auth::id())
            ->blogPosts()
            ->withCount('blog_comments')
            ->withCount('blog_visitors')
            ->get();

        return response()->json($blog_posts, 200);
    }

    public function searchBlogByTitle($title)
    {
        $blogPost = BlogPost::where('title', 'like', "%{$title}%")->where('created_by_id', Auth::id())->get();
        if ($blogPost->count() > 0) {
            return \response()->json($blogPost, 200);
        } else {
            return \response()->json([], 200);
        }
    }

    public function destroyBlogPost($blogId)
    {
        $blog = BlogPost::find($blogId);
        ImageManager::delete("blog_post/" . $blog->thumbnail);
        $blog->delete();
        return $this->successResponse('Successfully Deleted');
    }

    public function makeShareLink($blogId)
    {
        try {
            $blogpost = BlogPost::findOrFail($blogId);
            $link = asset("/blog/" . $blogpost->slug);
            return response()->json($link, 200);
        } catch (\Exception $e) {
            return response()->json("Posts Not Found!", 200);
        }
    }
}
