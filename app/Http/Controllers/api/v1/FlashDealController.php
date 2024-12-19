<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashDealController extends Controller
{
    public function get_flash_deal()
    {
        try {
            $flash_deals = FlashDeal::where(['status' => 1])
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))->first();
            return response()->json($flash_deals, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

    }

    public function get_all_flash_deal()
    {
        try {
            $flash_deals = FlashDeal::where(['status' => 1])
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))->get();
            return response()->json($flash_deals, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function get_all_featured_flash_deal()
    {
        try {
            $flash_deals = FlashDeal::where(['status' => 1, 'featured' => 1])
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))->get();
            return response()->json($flash_deals, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function get_products(Request $request, $deal_id)
    {
        $flash_sell = FlashDeal::where('id', $deal_id)->orWhere('slug', $deal_id)->first();

        if (!$flash_sell || !$flash_sell->is_active()) {
            return response()->json([
                'success' => 0,
                'message' => 'Flash sell not found'
            ]);
        }

        $p_ids = FlashDealProduct::with(['product'])
            ->where(['flash_deal_id' => $flash_sell->id])
            ->pluck('product_id')->toArray();


        if (count($p_ids) > 0) {
            $products = Product::join('flash_deal_products', 'flash_deal_products.product_id', '=', 'products.id')
                ->where('flash_deal_products.flash_deal_id', $flash_sell->id)
                ->select('products.*', 'flash_deal_products.discount', 'flash_deal_products.discount_type')
                ->paginate(10);

//            dd($products[0]);
            $products = Product::with(['rating', 'brand', 'flash_deal'])->whereIn('id', $p_ids);
            if ($request->limit) {
                $limit = $request->limit;
                $products = $products->paginate($limit);
                return response()->json(AdditionalServices::product_data_formatting_paginate($products), 200);
            } else {
                $products = $products->get();
                return response()->json(AdditionalServices::product_data_formatting($products, true), 200);
            }

        }

        return response()->json([], 200);
    }

    public function flash_deal_details($deal_id)
    {
        $flash_deal = FlashDeal::where('id', $deal_id)->orWhere('slug', $deal_id)->first();
        return response()->json($flash_deal, 200);
    }
}
