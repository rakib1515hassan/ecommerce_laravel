<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Models\QuestionAnswering;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\QAResource;
use App\Models\Product;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionAndAnsweringController extends Controller
{
    public function fetchQAByProductId($productId)
    {
        if (\is_numeric($productId)) {
            $product = Product::find($productId);
            if ($product) {
                $productQA = new ProductResource($product);
                return $productQA;
            } else {
                return [];
            }
        } else {
            $product = Product::where('slug', $productId)->first();
            if ($product) {
                $productQA = new ProductResource($product);
                return $productQA;
            } else {
                return [];
            }
        }
    }

    
    public function createQAUnderTheProduct(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 422);
        }

        if (is_integer($productId)) {
            if (Product::find($productId)) {
                $data = $validator->validated();
                $data['customer_id'] = Auth::id();
                $data['product_id']  = $productId;
                $data['answer']  = null;
                $data['status']  = 'unread';
                $data['awswered_by_admin']  = 0;
                $qa = QuestionAnswering::create($data);
                return \response()->json($qa, 200);
            }
        } else {
            $product = Product::where('slug', $productId)->first();
            if ($product) {
                $data = $validator->validated();
                $data['customer_id'] = Auth::id();
                $data['product_id']  = $product->id;
                $data['answer']  = null;
                $data['status']  = 'unread';
                $data['awswered_by_admin']  = 0;
                $qa = QuestionAnswering::create($data);
                return \response()->json($qa, 200);
            }
        }

        return response()->json('Product Not Found!', 200);
    }
}
