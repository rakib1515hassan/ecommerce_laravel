<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Brand;
use App\Models\Product;
use App\Services\BrandManager;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function get_brands(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by', 'name');      
            $sortOrder = $request->input('sort_order', 'asc'); 

            $brands = Brand::orderBy($sortBy, $sortOrder)->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching brands: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch brands. Please try again later.'], 500);
        }

        return response()->json($brands, 200);
    }


    public function get_products(Request $request, $brand_id)
    {
        $brand = Brand::where(['id' => $brand_id])->orWhere(['slug' => $brand_id])->first();

        if (!$brand)
            return response()->json(['message' => 'Brand not found'], 404);

        try {
            $products = Product::where(['brand_id' => $brand->id]);


            if ($request->has('limit')) {
                $products = $products->paginate($request->limit);
                return response()->json(AdditionalServices::product_data_formatting_paginate($products), 200);
            } else {
                return response()->json(AdditionalServices::product_data_formatting($products->get(), true), 200);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function brand_details_by_brand_id($brand_id)
    {
        $brands = Brand::withCount('brandProducts')->where(['id' => $brand_id])->orWhere(['slug' => $brand_id])->first();

        return response()->json($brands, 200);
    }
}
