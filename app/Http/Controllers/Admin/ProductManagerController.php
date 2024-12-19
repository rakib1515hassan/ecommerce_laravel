<?php

namespace App\Http\Controllers\Admin;

use App\Services\Converter;
use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Services\BackEndHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductManager;
use App\Models\WithdrawRequest;
use App\Models\ProductManagerWallet;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\OrderTransaction;

class ProductManagerController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $product_managers = ProductManager::with(['orders', 'product'])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $product_managers = ProductManager::with(['orders', 'product']);
        }
        $product_managers = $product_managers->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.product_manager.index', compact('product_managers', 'search'));
    }

    public function view(Request $request, $id, $tab = null)
    {
        $product_manager = ProductManager::findOrFail($id);
        if ($tab == 'order') {
            $id = $product_manager->id;
            $orders = Order::where(['product_manager_is'=>'product_manager'])->where(['product_manager_id'=>$id])->latest()->paginate(AdditionalServices::pagination_limit());
            // $orders->map(function ($data) {
            //     $value = 0;
            //     foreach ($data->details as $detail) {
            //         $value += ($detail['price'] * $detail['qty']) + $detail['tax'] - $detail['discount'];
            //     }
            //     $data['total_sum'] = $value;
            //     return $data;
            // });
            return view('admin-views.product_manager.view.order', compact('product_manager', 'orders'));
        } else if ($tab == 'product') {
            $products = Product::where('added_by', 'product_manager')->where('user_id', $product_manager->id)->paginate(AdditionalServices::pagination_limit());
            return view('admin-views.product_manager.view.product', compact('product_manager', 'products'));
        } else if ($tab == 'setting') {
            $commission = $request['commission'];
            if ($request->has('commission')) {
                request()->validate([
                    'commission' => 'required | numeric | min:1',
                ]);

                if ($request['commission_status'] == 1 && $request['commission'] == null) {
                    Toastr::error('You did not set commission percentage field.');
                    //return back();
                } else {
                    $product_manager = ProductManager::find($id);
                    $product_manager->sales_commission_percentage = $request['commission_status'] == 1 ? $request['commission'] : null;
                    $product_manager->save();

                    Toastr::success('Commission percentage for this product_manager has been updated.');
                }
            }
            $commission = 0;
            if ($request->has('gst')) {
                if ($request['gst_status'] == 1 && $request['gst'] == null) {
                    Toastr::error('You did not set GST number field.');
                    //return back();
                } else {
                    $product_manager = ProductManager::find($id);
                    $product_manager->gst = $request['gst_status'] == 1 ? $request['gst'] : null;
                    $product_manager->save();

                    Toastr::success('GST number for this product_manager has been updated.');
                }
            }

            //return back();
            return view('admin-views.product_manager.view.setting', compact('product_manager'));
        } else if ($tab == 'transaction') {
            $transactions = OrderTransaction::where('product_manager_is','product_manager')->where('product_manager_id',$product_manager->id);

            $query_param = [];
            $search = $request['search'];
            if ($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $transactions = $transactions->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('order_id', 'like', "%{$value}%")
                            ->orWhere('transaction_id', 'like', "%{$value}%");
                    }
                });
                $query_param = ['search' => $request['search']];
            }else{
                $transactions = $transactions;
            }
            $status = $request['status'];
            if ($request->has('status'))
            {
                $key = explode(' ', $request['status']);
                $transactions = $transactions->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('status', 'like', "%{$value}%");
                    }
                });
                $query_param = ['status' => $request['status']];
            }
               $transactions = $transactions->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);

            return view('admin-views.product_manager.view.transaction', compact('product_manager', 'transactions','search','status'));

        } else if ($tab == 'review') {
            $product_managerId = $product_manager->id;

            $query_param = [];
            $search = $request['search'];
            if ($request->has('search')) {
                $key = explode(' ', $request['search']);
                $product_id = Product::where('added_by','product_manager')->where('user_id',$product_managerId)->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                })->pluck('id')->toArray();

                $reviews = Review::with(['product'])
                    ->whereIn('product_id',$product_id);

                $query_param = ['search' => $request['search']];
            } else {
                $reviews = Review::with(['product'])->whereHas('product', function ($query) use ($product_managerId) {
                    $query->where('user_id', $product_managerId)->where('added_by', 'product_manager');
                });
            }
            //dd($reviews->count());
            $reviews = $reviews->paginate(AdditionalServices::pagination_limit())->appends($query_param);

            return view('admin-views.product_manager.view.review', compact('product_manager', 'reviews', 'search'));
        }
        return view('admin-views.product_manager.view', compact('product_manager'));
    }

    public function updateStatus(Request $request)
    {
        $order = ProductManager::findOrFail($request->id);
        $order->status = $request->status;
        if ($request->status == "approved") {
            Toastr::success('product_manager has been approved successfully');
        } else if ($request->status == "rejected") {
            Toastr::info('product_manager has been rejected successfully');
        } else if ($request->status == "suspended") {
            $order->auth_token = Str::random(80);
            Toastr::info('product_manager has been suspended successfully');
        }
        $order->save();
        return back();
    }

    public function order_list($product_manager_id)
    {
        $orders = Order::where('product_manager_id', $product_manager_id)->where('product_manager_is', 'product_manager');

        $orders = $orders->latest()->paginate(AdditionalServices::pagination_limit());
        $product_manager = ProductManager::findOrFail($product_manager_id);
        return view('admin-views.product_manager.order-list', compact('orders', 'product_manager'));
    }

    public function product_list($product_manager_id)
    {
        $product = Product::where(['user_id' => $product_manager_id, 'added_by' => 'product_manager'])->latest()->paginate(AdditionalServices::pagination_limit());
        $product_manager = ProductManager::findOrFail($product_manager_id);
        return view('admin-views.product_manager.porduct-list', compact('product', 'product_manager'));
    }

    public function order_details($order_id, $product_manager_id)
    {
        $order = Order::with('shipping')->where(['id' => $order_id])->first();
        return view('admin-views.product_manager.order-details', compact('order', 'product_manager_id'));
    }

    public function withdraw()
    {
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_req = WithdrawRequest::with(['product_manager'])
            ->when($all, function ($query) {
                return $query;
            })
            ->when($active, function ($query) {
                return $query->where('approved', 1);
            })
            ->when($denied, function ($query) {
                return $query->where('approved', 2);
            })
            ->when($pending, function ($query) {
                return $query->where('approved', 0);
            })
            ->orderBy('id', 'desc')
            ->latest()
            ->paginate(AdditionalServices::pagination_limit());

        return view('admin-views.product_manager.withdraw', compact('withdraw_req'));
    }

    public function withdraw_view($withdraw_id, $product_manager_id)
    {
        $product_manager = WithdrawRequest::with(['product_manager'])->where(['id' => $withdraw_id])->first();
        return view('admin-views.product_manager.withdraw-view', compact('product_manager'));
    }

    public function withdrawStatus(Request $request, $id)
    {
        $withdraw = WithdrawRequest::find($id);
        $withdraw->approved = $request->approved;
        $withdraw->transaction_note = $request['note'];
        if ($request->approved == 1) {
            ProductManagerWallet::where('product_manager_id', $withdraw->product_manager_id)->increment('withdrawn', $withdraw['amount']);
            ProductManagerWallet::where('product_manager_id', $withdraw->product_manager_id)->decrement('pending_withdraw', $withdraw['amount']);
            $withdraw->save();
            Toastr::success('product_manager Payment has been approved successfully');
            return redirect()->route('admin.product_managers.withdraw_list');
        }

        ProductManagerWallet::where('product_manager_id', $withdraw->product_manager_id)->increment('total_earning', $withdraw['amount']);
        ProductManagerWallet::where('product_manager_id', $withdraw->product_manager_id)->decrement('pending_withdraw', $withdraw['amount']);
        $withdraw->save();
        Toastr::info('product_manager Payment request has been Denied successfully');
        return redirect()->route('admin.product_managers.withdraw_list');

    }

    public function sales_commission_update(Request $request, $id)
    {
        if ($request['status'] == 1 && $request['commission'] == null) {
            Toastr::error('You did not set commission percentage field.');
            return back();
        }

        $product_manager = ProductManager::find($id);
        $product_manager->sales_commission_percentage = $request['status'] == 1 ? $request['commission'] : null;
        $product_manager->save();

        Toastr::success('Commission percentage for this product_manager has been updated.');
        return back();
    }
}
