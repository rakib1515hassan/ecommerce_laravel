<?php

namespace App\Http\Controllers\api\v1;

use App\Library\Redx\RedxShippingSystem;
use App\Models\AddressArea;
use App\Models\Brand;
use App\Models\FlashDeal;
use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Services\ImageManager;
use App\Services\ProductManager;
use App\Services\CategoryManager;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Validator;
use App\Models\RecommendedProduct;
use App\Models\User;

class ProductController extends Controller
{
    public function get_latest_products(Request $request)
    {
        $products = ProductManager::get_latest_products($request['limit'], $request['offset']);
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_featured_products(Request $request)
    {
        $products = ProductManager::get_featured_products($request['limit'], $request['offset']);
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_top_rated_products(Request $request)
    {
        $products = ProductManager::get_top_rated_products($request['limit'], $request['offset']);
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }



    public function get_searched_products(Request $request)
    {
        // \Log::info("Searching products");
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'nullable|integer|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        \Log::info("request =", $request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        // Get products based on the search name
        $products = ProductManager::search_products($request['name'], $request['limit'], $request['offset']);

        if ($products['products'] == null) {
            $products = ProductManager::translated_product_search($request['name'], $request['limit'], $request['offset']);
        }
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);

        // Save to recommended_products if user_id is provided and keyword is not already there
        if ($request->filled('user_id')) {
            $existingRecommendation = RecommendedProduct::where('user_id', $request->user_id)
                ->where('keyword', $request->name)
                ->first();

            if (!$existingRecommendation) {
                $recommendedProduct = new RecommendedProduct();
                $recommendedProduct->user_id = $request->user_id;
                $recommendedProduct->keyword = $request->name;
                $recommendedProduct->save();
            }
        }

        return response()->json($products, 200);
    }


    public function get_product($id, Request $request)
    {
        // todo: flash_sell
        $product = Product::with('seller')->where('id', $id)->orWhere('slug', $id)->first();

        $product->applied_flash_sell = null;
        if ($request->has('flash_deal_slug')) {
            $flash_deal = FlashDeal::where('slug', $request->flash_deal_slug)->first();

            if ($flash_deal && $flash_deal->is_active()) {
                $flash_deal_product = $flash_deal->products->where('product_id', $product->id)->first();

                if ($flash_deal_product != null) {
                    $product->applied_flash_sell = $flash_deal;
                    $product->discount = $flash_deal_product->discount;
                    $product->discount_type = $flash_deal_product->discount_type;
                }
            }
        }

        if (isset($product)) {
            $product = AdditionalServices::product_data_formatting($product, false);
            $product['brand'] = Brand::find($product['brand_id']);

            unset($product['purchase_price']);


            if ($product['added_by'] == 'seller') {
                $product['seller'] = $product->seller;
                $product['seller']['order'] = $product->seller->shop;
            } else {
                $product['seller'] = array(['order' => null]);
            }

            $avg_rating = 0;

            if ($product['reviews']){
                foreach ($product['reviews'] as $review) {
                    if ($review->customer){
                        $review['f_name'] = $review->customer->f_name ?? 'Anonymous';
                        $review['profile_image'] = $review->customer->image ?? '';
                        unset($review['customer']);
                        $avg_rating += $review['rating'];
                    }
                }
                $count = count($product['reviews']);
                if ($count == 0)
                    $product['avg_rating'] = 0;
                else
                    $product['avg_rating'] = $avg_rating / count($product['reviews']);
            }
        }

        return response()->json($product, 200);
    }


    public function get_product_reviews($id)
    {
        $product = Product::where('id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $reviews = Review::where('product_id', $product->id)
            ->with('customer')
            ->get();

        $formattedReviews = $reviews->map(function ($review) {
            $customerFirstName = optional($review->customer)->f_name ?: 'Anonymous';
            $customerImage = optional($review->customer)->image ?: '';
            return [
                'id' => $review->id,
                'product_id' => $review->product_id,
                'customer_id' => $review->customer_id,
                'customer_first_name' => $customerFirstName,
                'customer_image' => $customerImage,
                'comment' => $review->comment,
                'attachment' => json_decode($review->attachment),
                'rating' => $review->rating,
                'status' => $review->status,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at,
            ];
        });

        return response()->json($formattedReviews, 200);
    }




    public function get_best_sellings(Request $request)
    {
        $products = ProductManager::get_best_selling_products($request['limit'], $request['offset']);
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_home_categories()
    {
        $categories = Category::where('home_status', true)->get();
        $categories->map(function ($data) {
            $data['products'] = AdditionalServices::product_data_formatting(CategoryManager::products($data['id']), true);
            return $data;
        });
        return response()->json($categories, 200);
    }



    public function get_related_products($categoryId)
    {
        // Check if the category exists
        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json([
                'error' => 'Category not found.'
            ], 404);
        }

        // Retrieve products based on the category ID
        $products = Product::whereJsonContains('category_ids', [['id' => $categoryId]])
            ->active()
            ->with(['reviews'])
            ->limit(20)
            ->get();

        return response()->json($products, 200);
    }


    public function get_product_shipping_cost(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'area_id' => 'required|exists:address_areas,id',
            'quantity' => 'nullable|numeric|min:1',
        ]);

        $user_area = AddressArea::find($request->area_id);

        $product = Product::with('seller')->where('id', $request->product_id)->orWhere('slug', $request->product_id)->first();

        if ($request->quantity == null) {
            $request->quantity = 1;
        }
        $cost = RedxShippingSystem::getShippingCostProduct($product, $user_area, $request->quantity);

        return response()->json([
            'shipping_cost' => intval($cost)
        ], 200);
    }


    // Product by Slug
    public function get_product_by_slug($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if (isset($product)) {
            $product = AdditionalServices::product_info_formatting($product, false);
        }
        return response()->json($product, 200);
    }


    // Product by Link
    // public function get_product_by_link($link)
    // {
    //     $product = ResellerProduct::where('slug', $link)->first();
    //     if (isset($product)) {
    //         $product = AdditionalServices::product_info_formatting($product, false);
    //     }
    //     return response()->json($product, 200);
    // }


    // Product Rating
    public function get_product_rating($id)
    {
        try {
            $product = Product::where('id', $id)->orWhere('slug', $id)->first();
            $overallRating = ProductManager::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }


    public function get_product_rating_per_star($id)
    {

        $product = Product::where('id', $id)->orWhere('slug', $id)->first();

        $star = ["1" => 0, "2" => 0, "3" => 0, "4" => 0, "5" => 0];
        foreach ($product->reviews as $review) {
            if (isset($star[$review->rating])) {
                $star[$review->rating] += 1;
            } else {
                $star[$review->rating] = 1;
            }
        }

        $totalReview = 0;
        foreach ($star as $key => $value) {
            $totalReview += $value;
        }

        $avgRating = 0;
        foreach ($star as $key => $value) {
            $avgRating += $key * $value;
        }
        if ($totalReview != 0) {
            $avgRating = $avgRating / $totalReview;
        } else {
            $avgRating = 0;
        }


        $star['avgRating'] = $avgRating;
        $star['totalReview'] = $totalReview;

        return response()->json($star, 200);
    }

    public function counter($product_id)
    {
        try {
            $countOrder = OrderDetail::where('product_id', $product_id)->count();
            $countWishlist = Wishlist::where('product_id', $product_id)->count();
            return response()->json(['order_count' => $countOrder, 'wishlist_count' => $countWishlist], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }


    // Shipping Methods
    public function get_shipping_methods(Request $request)
    {
        $methods = ShippingMethod::where(['status' => 1])->get();
        return response()->json($methods, 200);
    }


    // Social Share Link
    public function social_share_link($product_id)
    {
        $product = Product::where('id', $product_id)->orWhere('slug', $product_id)->first();
        if ($product) {
            $link = asset('product/' . $product->slug);
            return response()->json($link, 200);
        }
        return response()->json('Product Not Found!', 202);
    }



    public function submit_product_review(Request $request)
    {
        // dd($request->all());
        // \Log::info(" First = ", $request->all());

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'fileUpload' => 'array|max:4',
            'fileUpload.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        \Log::info("Product review data =", ['data' => $request->all()]);

        $image_array = [];
        if ($request->hasFile('fileUpload')) {
            foreach ($request->file('fileUpload') as $image) {
                if ($image) {
                    $image_path = ImageManager::upload('review/', 'png', $image);
                    if ($image_path) {
                        $image_array[] = $image_path;
                    }
                }
            }
        }

        $review = new Review();
        $review->customer_id = $request->user()->id;
        $review->product_id = $request->product_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($image_array);
        $review->save();

        return response()->json(['message' => translate('Successfully review submitted!')], 200);
    }



    // Discounted Products
    public function get_discounted_product(Request $request)
    {
        $products = ProductManager::get_discounted_product($request['limit'], $request['offset']);
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }


    public function products(Request $request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews']);

        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if ($request['data_from'] == 'latest') {
            $query = $porduct_data->orderBy('id', 'DESC');
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $query = $porduct_data->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query;
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [AdditionalServices::convert_currency_to_usd($request['min_price']), AdditionalServices::convert_currency_to_usd($request['max_price'])]);
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];

        $products = $fetched->paginate(20)->appends($data);

        if ($request->ajax()) {
            return response()->json([
                'view' => view('web-views.products._ajax-products', compact('products'))->render()
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int) $request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $data['brand_name'] = Brand::find((int) $request['id'])->name;
        }

        return response()->json([
            'products' => $products,
            'data' => $data
        ], 200);
    }




    public function product_for_you(Request $request)
    {
        $limit = 20;
        $productsQuery = Product::active()->with(['reviews']);

        if ($request->has('limit')) {
            $limit = $request->limit;
        }

        // Check if user_id is provided
        if ($request->has('user_id')) {
            $userId = $request->user_id;

            // Fetch recommended keywords for the user
            $keywords = RecommendedProduct::where('user_id', $userId)->pluck('keyword')->toArray();
            \Log::info("KeyWords:" . json_encode($keywords));
            // \Log::info("KeyWords: " . implode(', ', $keywords));
            // \Log::info("KeyWords: " . print_r($keywords, true));

            if (!empty($keywords)) {
                $recommendedProducts = Product::where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere('name', 'like', '%' . $keyword . '%')
                            ->orWhere('details', 'like', '%' . $keyword . '%');
                    }
                })->get();
                // \Log::info("recommendedProducts: " . print_r($recommendedProducts, true));


                // Convert to an array of IDs for easy merging
                $recommendedProductIds = $recommendedProducts->pluck('id')->toArray();
                // \Log::info("recommendedProductIds: " . print_r($recommendedProductIds, true));

                $productsQuery = $productsQuery->whereIn('id', $recommendedProductIds);
            }

            // if (!empty($keywords)) {
            //     // Fetch categories that match the keywords
            //     $categories = Category::whereIn('name', $keywords)->pluck('id')->toArray();

            //     // Search for products by keywords and category names
            //     $recommendedProducts = Product::where(function ($query) use ($keywords, $categories) {
            //         foreach ($keywords as $keyword) {
            //             $query->orWhere('name', 'like', '%' . $keyword . '%')
            //                 ->orWhere('details', 'like', '%' . $keyword . '%');
            //         }

            //         if (!empty($categories)) {
            //             $query->orWhereHas('categories', function ($q) use ($categories) {
            //                 $q->whereIn('id', $categories);
            //             });
            //         }
            //     })->get();

            //     // Convert to an array of IDs for easy merging
            //     $recommendedProductIds = $recommendedProducts->pluck('id')->toArray();

            //     // Filter the products by the recommended IDs
            //     $productsQuery = $productsQuery->whereIn('id', $recommendedProductIds);
            // }
        } else {
            // Include random products
            $productsQuery = Product::active()->with(['reviews'])->inRandomOrder();

        }

        $products = $productsQuery->paginate($limit);

        return response()->json(AdditionalServices::product_data_formatting_paginate($products), 200);
    }

}
