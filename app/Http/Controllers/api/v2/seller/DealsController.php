<?php

namespace App\Http\Controllers\api\v2\seller;

use App\Models\FlashDeal;
use Illuminate\Http\Request;
use App\Models\FlashDealProduct;
use App\Http\Controllers\Controller;

class DealsController extends Controller
{
    public function addProductToFlashDeal(Request $request, $flashId)
    {
        $validatedData = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'discount'      => 'required|numeric',
            'discount_type' => 'required|in:flat,percent',
        ]);

        $flashDeal = FlashDeal::where('id', $flashId)->where('deal_type', 'flash_deal')->first();
        if ($flashDeal) {
            $validatedData['seller_id'] = auth()->user()->id;
            $validatedData['flash_deal_id'] = $flashId;
            $validatedData['status'] = 0;
            $validatedData['seller_is'] = 'seller';
            $flashDeal = FlashDealProduct::create($validatedData);
            return $this->successResponse('Product Successfully Added To Flash the Deal', $flashDeal);
        } else {
            return $this->error('Flash-deal Not found!', [], 404);
        }
    }

    public function addProductToFeatureDeal(Request $request, $featureId)
    {
        $validatedData = $request->validate([
            'product_id'    => 'required|exists:products,id',
        ]);

        $flashDeal = FlashDeal::where('id', $featureId)->where('deal_type', 'feature_deal')->first();
        if ($flashDeal) {
            $validatedData['seller_id'] = auth()->user()->id;
            $validatedData['flash_deal_id'] = $featureId;
            $validatedData['status'] = 0;
            $validatedData['seller_is'] = 'seller';
            $flashDeal = FlashDealProduct::create($validatedData);
            return $this->successResponse('Product Successfully Added To Flash the Deal', $flashDeal);
        } else {
            return $this->error('Feature-deal Not found!', [], 404);
        }
    }
}
