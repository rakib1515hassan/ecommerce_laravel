<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class AcceptRegistrationController extends Controller
{

    public function product_manager_list()
    {
        $product_manager = ProductManager::where('seller_id', auth('seller')->id())->paginate(25);
        return view('seller-views.product_manager.product_manager-list', compact('product_manager'));
    }

    public function updateStatus(Request $request)
    {
        $product_manager = ProductManager::findOrFail($request->id);

        if ($request->status == 1) {
            $product_manager->is_active = 1;
            Toastr::success('Product Manager has been approved successfully');
        } else if ($request->status == 0) {
            $product_manager->is_active = 0;
            Toastr::info('Seller has been rejected successfully');
        } else if ($request->status == 2) {
            $product_manager->is_active = 2;
            Toastr::info('Product Manager has been suspended successfully');
        }
        $product_manager->save();
        return response()->json(['success' => 'Status changed successfully', 'status' => true]);
    }
}
