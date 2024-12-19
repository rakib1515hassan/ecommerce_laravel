<?php

namespace App\Http\Controllers\ProductManager;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function view()
    {
        $seller_id = auth('product_manager')->user()->seller_id;
        $shop = Shop::where(['seller_id' => $seller_id])->first();
        $seller = Seller::where(['id' => $seller_id])->first();

        if (isset($shop) == false) {
            DB::table('shops')->insert([
                'seller_id' => $seller_id,
                'name' => $seller->f_name . ' ' . $seller->l_name . ' Shop',
                'address' => '',
                'contact' => $seller->contact,
                'image' => 'def.png',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $shop = Shop::where(['seller_id' => $seller_id])->first();
        }

        return view('product_manager-views.shop.shopInfo', compact('shop'));
    }
}
