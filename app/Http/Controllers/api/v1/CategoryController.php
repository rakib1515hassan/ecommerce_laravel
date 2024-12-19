<?php

namespace App\Http\Controllers\api\v1;

use App\Services\CategoryManager;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories()
    {
        try {
            $categories = Category::with(['childes.childes'])->where(['position' => 0])->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_featured_categories()
    {
        $categories = Category::where('home_status', true)->get();

        return response()->json($categories, 200);
    }

    public function get_products(Request $request, $id)
    {
        $sub_category_ids = $request->sub_category_ids;
        $filters = $request->filters;
        $price_range = $request->price_range;
        $sort_by = $request->sort_by;
        $limit = $request->limit;

        $category = Category::where('id', $id)->orWhere('slug', $id)->first();
        $products = Product::active()->whereJsonContains('category_ids', ["id" => (string)$category->id]);

        if ($sub_category_ids != null) {
            $sub_category_ids = explode(',', $sub_category_ids);
            $products = $products->where(function ($query) use ($sub_category_ids) {
                foreach ($sub_category_ids as $sub_category_id) {
                    $query->orWhereJsonContains('category_ids', ["id" => (string)$sub_category_id]);
                }
            });
        }

        if ($filters) {
            $filters = explode(",", $filters);

            $products = $products->where(function ($query) use ($filters) {
                foreach ($filters as $filter) {
                    $query->orWhere('choice_options', 'like', '%' . $filter . '%');
                }
            });
        }

        if ($price_range) {
            $price_range = explode(",", $price_range);
            $products = $products->whereBetween('unit_price', $price_range);
        }


        if ($sort_by == 'price_low_to_high') {
            $products = $products->orderBy('unit_price', 'asc');
        }
        if ($sort_by == 'price_high_to_low') {
            $products = $products->orderBy('unit_price', 'desc');
        }
        if ($sort_by == 'newest') {
            $products = $products->orderBy('id', 'desc');
        }
        if ($sort_by == 'oldest') {
            $products = $products->orderBy('id', 'asc');
        }
        if ($sort_by == 'a_to_z') {
            $products = $products->orderBy('name', 'asc');
        }
        if ($sort_by == 'z_to_a') {
            $products = $products->orderBy('name', 'desc');
        }


        if ($limit) {
            $products = $products->paginate($limit);
            return response()->json($products, 200);
        } else {
            $products = $products->get();
            return response()->json(AdditionalServices::product_data_formatting($products, true), 200);
        }
    }


    public function category_details_by_id($category_id)
    {
        try {

            $categories = Category::with(['childes.childes'])->where('id', $category_id)->orWhere('slug', $category_id)->first();

            $filters = collect();
            $max_price = 0;
            $min_price = 9999999;
            $products = CategoryManager::products($category_id);
            foreach ($products as $product) {
                if ($product->choice_options != "[]") {
                    foreach (json_decode($product->choice_options) as $choice) {
                        if ($filters->has($choice->title)) {
                            $filters[$choice->title] = $filters[$choice->title]->merge($choice->options)->map(function ($item) {
                                return trim((string)$item);
                            })->unique();
                        } else {
                            $filters[$choice->title] = collect($choice->options)->map(function ($item) {
                                return trim((string)$item);
                            })->unique();
                        }

                        $variations = json_decode($product->variation);

                        if ($variations) {
                            foreach ($variations as $variation) {
                                if ($variation->price > $max_price) {
                                    $max_price = $variation->price;
                                }
                                if ($variation->price < $min_price) {
                                    $min_price = $variation->price;
                                }
                            }
                        } else {
                            if ($product->unit_price > $max_price) {
                                $max_price = $product->unit_price;
                            }
                            if ($product->unit_price < $min_price) {
                                $min_price = $product->unit_price;
                            }
                        }
                    }
                }
            }


            $categories['filters'] = $filters;
            $categories['max_price'] = $max_price;
            $categories['min_price'] = $min_price;

            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
