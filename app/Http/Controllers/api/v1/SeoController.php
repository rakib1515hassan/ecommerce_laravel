<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function product($id)
    {
        $product = Product::where('id', $id)->orWhere('slug', $id)->first();

        // search in cache
        if (Cache::has('seo_product' . $id)) {
            return response()->json(Cache::get('seo_product' . $id));
        }

        if ($product) {
            $description = trim(empty($product->meta_description) ? strip_tags($product->details) : $product->meta_description);
            $description = preg_replace('/<[^>]*>/', '', $description);
            $description = preg_replace('/\s+/', ' ', $description);
            $description = preg_replace('/\n+/', ' ', $description);
            $description = substr($description, 0, 250);

            $seo = [
                'title' => $product->name,
                'description' => $description,
                'image' => asset_storage("product/" . $product->thumbnail)
            ];

            // store in cache
            Cache::put('seo_product' . $id, $seo, 60 * 24 * 7);

            return response()->json($seo);
        } else {
            return response()->json([
                'title' => 'Product not found',
                'description' => 'Product not found',
                'image' => ''
            ]);
        }
    }

    public function blog($id)
    {
        $blog = BlogPost::where('id', $id)->orWhere('slug', $id)->first();



        if ($blog) {
            return response()->json([
                'title' => $blog->title,
                'description' => empty($blog->seo_description) ? strip_tags($blog->content) : $blog->seo_description,
                'image' => storage_asset(empty($blog->seo_image) ? "blog_post/" . $blog->thumbnail : "blog_post/" . $blog->seo_image)
            ]);
        } else {
            return response()->json([
                'title' => 'Blog not found',
                'description' => 'Blog not found',
                'image' => ''
            ]);
        }
    }
}
