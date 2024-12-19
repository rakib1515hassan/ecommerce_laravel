<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Services\ProductManager;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function get_seller_info(Request $request)
    {
        $seller = Seller::with(['order'])->where(['id' => $request['seller_id']])->first(['id', 'f_name', 'l_name', 'phone', 'image']);
        return response()->json($seller, 200);
    }

    public function get_seller_products($seller_id, Request $request)
    {
        $data = ProductManager::get_seller_products($seller_id, $request['limit'], $request['offset']);
        $data['products'] = AdditionalServices::product_data_formatting($data['products'], true);
        return response()->json($data, 200);
    }

    public function get_top_sellers()
    {
        $top_sellers = Shop::whereHas('seller',function ($query){return $query->approved();})->take(15)->get();
        return response()->json($top_sellers, 200);
    }

    public function get_all_sellers()
    {
        $top_sellers = Shop::whereHas('seller',function ($query){return $query->approved();})->get();
        return response()->json($top_sellers, 200);
    }
}
