<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Services\AdditionalServices;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function seller_shop(Request $request, $id)
    {
        $product_ids = Product::active()
            ->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })
            ->pluck('id')->toArray();

        $avg_rating = Review::whereIn('product_id', $product_ids)->avg('rating');
        $total_review = Review::whereIn('product_id', $product_ids)->count();
        $total_order = OrderDetail::whereIn('product_id', $product_ids)->groupBy('order_id')->count();

        //finding category ids
        $products = Product::whereIn('id', $product_ids)->paginate(12);

        $category_info = [];
        foreach ($products as $product) {
            $category_info[] = $product['category_ids'];
        }

        $category_info_decoded = [];
        foreach ($category_info as $info) {
            $category_info_decoded[] = json_decode($info);
        }

        $category_ids = [];
        foreach ($category_info_decoded as $decoded) {
            foreach ($decoded as $info) {
                $category_ids[] = $info->id;
            }
        }

        $categories = [];
        foreach ($category_ids as $category_id) {
            $category = Category::with(['childes.childes'])->where('position', 0)->find($category_id);
            if ($category != null) {
                $categories[] = $category;
            }
        }
        $categories = array_unique($categories);
        //end

        //products search
        if ($request->product_name) {
            $products = Product::active()
                ->when($id == 0, function ($query) {
                    return $query->where(['added_by' => 'admin']);
                })
                ->when($id != 0, function ($query) use ($id) {
                    return $query->where(['added_by' => 'seller'])
                        ->where('user_id', $id);
                })
                ->where('name', 'like', $request->product_name . '%')
                ->paginate(12);
        } elseif ($request->category_id) {
            $products = Product::active()
                ->when($id == 0, function ($query) {
                    return $query->where(['added_by' => 'admin']);
                })
                ->when($id != 0, function ($query) use ($id) {
                    return $query->where(['added_by' => 'seller'])
                        ->where('user_id', $id);
                })
                ->whereJsonContains('category_ids', [
                    ['id' => strval($request->category_id)],
                ])->paginate(12);
        }

        if ($id == 0) {
            $shop = [
                'id' => 0,
                'name' => AdditionalServices::get_business_settings('company_name'),
            ];
        } else {
            $shop = Shop::where('seller_id', $id)->first();
            if (isset($shop) == false) {
                Toastr::error("Shop doesn't exist");
                return back();
            }
        }

        return view('web-views.shop-page', compact('products', 'shop', 'categories'))
            ->with('seller_id', $id)
            ->with('total_review', $total_review)
            ->with('avg_rating', $avg_rating)
            ->with('total_order', $total_order);
    }

    //ajax filter (category based)
    public function seller_shop_product(Request $request, $id)
    {
        $products = Product::active()->with('orders')->where(['added_by' => 'seller'])
            ->where('user_id', $id)
            ->whereJsonContains('category_ids', [
                ['id' => strval($request->category_id)],
            ])
            ->paginate(12);
        $shop = Shop::where('seller_id', $id)->first();
        if ($request['sort_by'] == null) {
            $request['sort_by'] = 'latest';
        }

        if ($request->ajax()) {
            return response()->json([
                'view' => view('web-views.products._ajax-products', compact('products'))->render(),
            ], 200);

        }

        return view('web-views.shop-page', compact('products', 'shop'))->with('seller_id', $id);
    }


}
