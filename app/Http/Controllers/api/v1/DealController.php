<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function get_featured_deal()
    {
        $featured_deal = FlashDeal::with(['products.product.reviews'])
            ->where(['status' => 1])
            ->where(['deal_type' => 'feature_deal'])->first();

        if(isset($featured_deal)){
            $featured_deal = $featured_deal->products->map( function ($data) {
                try{
                    $data->product = AdditionalServices::product_data_formatting($data->product,false);
                }catch(\Exception $ex){}
                return $data;
            });
        }

        return response()->json($featured_deal, 200);
    }
}
