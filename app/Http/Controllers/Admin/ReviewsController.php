<?php

namespace App\Http\Controllers\Admin;

use Brian2694\Toastr\Facades\Toastr;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class ReviewsController extends Controller
{
    function list(Request $request) {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $product_id = Product::where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                })->pluck('id')->toArray();

            $customer_id = User::where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                    }
                })->pluck('id')->toArray();

            $reviews = Review::WhereIn('product_id',  $product_id )->orWhereIn('customer_id',$customer_id);

            $query_param = ['search' => $request['search']];
        }else{
            $reviews = Review::with(['product', 'customer']);
        }
        $reviews = $reviews->paginate(AdditionalServices::pagination_limit());
        return view('admin-views.reviews.list', compact('reviews','search'));
    }



    public function review_delete($id)
    {

        $review = Review::findOrFail($id);
        $review->delete();
        Toastr::success("Review delete successfully!", "Success");

        return redirect()->back();
    }
}
