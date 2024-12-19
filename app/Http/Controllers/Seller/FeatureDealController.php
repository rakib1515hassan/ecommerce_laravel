<?php

namespace App\Http\Controllers\Seller;

use App\Models\Product;
use App\Models\FlashDeal;
use App\Models\DealOfTheDay;
use Illuminate\Http\Request;
use App\Models\FlashDealProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices;
use Brian2694\Toastr\Facades\Toastr;

class FeatureDealController extends Controller
{
    // Feature deals
    public function featureIndex(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $feature_deal = FlashDeal::where('deal_type', 'feature_deal')
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('title', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $feature_deal = FlashDeal::where('deal_type', 'feature_deal');
        }
        $feature_deal = $feature_deal->where('status', 1)->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);

        return view('seller-views.deal.feature-deal-seller.feature-index', compact('feature_deal', 'search'));
    }

    public function addProduct($deal_id)
    {
        $seller_id = auth('seller')->id();

        $feature_deal_products = FlashDealProduct::with('product')
            ->where('flash_deal_id', $deal_id)
            ->where('seller_id', $seller_id)
            ->paginate(AdditionalServices::pagination_limit());

        $deal = FlashDeal::with(['products.product'])->where('id', $deal_id)->first();

        return view('seller-views.deal.feature-deal-seller.add-product', compact('deal', 'feature_deal_products'));
    }

    public function addProductSubmit(Request $request, $deal_id)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $feature_deal_product = FlashDealProduct::where('flash_deal_id', $deal_id)
            ->where('seller_id', auth('seller')->id())
            ->where('product_id', $request->product_id)->first();

        if (!$feature_deal_product) {
            FlashDealProduct::create([
                'flash_deal_id' => $deal_id,
                'product_id' => $request->product_id,
                'seller_is' => 'seller',
                'seller_id' => auth('seller')->id(),
                'status' => 0
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => 1,
            ], 200);
        }

        return back();
    }

    public function deleteProduct($id)
    {
        $fdp = DB::table('flash_deal_products')->where('id', $id)->delete();
        Toastr::success('Deal removed successfully!');
        return back();
    }
}
