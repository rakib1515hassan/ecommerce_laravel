<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductManagerWallet;
use App\Models\ResellerWallet;
use App\Models\SellerWallet;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{

    public function index(Request $request)
    {
        $search = $request['search'];
        // if ($request->has('search')) {
        //     $withdraws = [];
        // } else {
        //     $withdraws = WithdrawRequest::all();
        // }

        $withdraws = WithdrawRequest::orderBy('created_at', 'desc')->get();

        return view('admin-views.withdraw.list', compact('withdraws', 'search'));
    }


    // public function update(Request $request, $id)
    // {
    //     $w = WithdrawRequest::find($id);
    //     if ($w->approved1 != 1) {
    //         SellerWallet::where('seller_id', $w->person_id)->increment('withdrawn', $w->amount);
    //         ResellerWallet::where('reseller_id', $w->person_id)->increment('withdrawn', $w->amount);
    //         ProductManagerWallet::where('id', $w->person_id)->increment('withdrawn', $w->amount);
    //     }
    //     $w->approved = $request['approved'];
    //     $w->transaction_note = $request['note'];
    //     $w->save();
    //     Toastr::success('Updated!');
    //     return redirect()->back();
    // }

    public function StatusUpdate(Request $request)
    {
        // dd($request->all());
        // \Log::info('Request data:', $request->all());

        $id = $request->input('id');
        $w = WithdrawRequest::find($id);

        if ($w->approved !== 1) {
            $person = $w->person;

            switch ($person) {
                case 'seller':
                    $sellerWallet = SellerWallet::where('seller_id', $w->person_id)->firstOrFail();
                    $sellerWallet->increment('withdrawn', $w->amount);
                    $sellerWallet->decrement('pending_withdraw', $w->amount);
                    $sellerWallet->save();

                    $w->approved = 1;
                    $w->transaction_note = $request->input('note');
                    $w->save();
                    return response()->json(['message' => 'Status updated successfully']);
                // break;
                case 'reseller':
                    $resellerWallet = ResellerWallet::where('reseller_id', $w->person_id)->firstOrFail();
                    $resellerWallet->increment('withdrawn', $w->amount);
                    $resellerWallet->decrement('pending_withdraw', $w->amount);
                    $resellerWallet->save();

                    $w->approved = 1;
                    $w->transaction_note = $request->input('note');
                    $w->save();
                    return response()->json(['message' => 'Status updated successfully']);
                // break;
                case 'product_manager':
                    $productManagerWallet = ProductManagerWallet::where('id', $w->person_id)->firstOrFail();
                    $productManagerWallet->increment('withdrawn', $w->amount);
                    $productManagerWallet->decrement('pending_withdraw', $w->amount);
                    $productManagerWallet->save();

                    $w->approved = 1;
                    $w->transaction_note = $request->input('note');
                    $w->save();
                    return response()->json(['message' => 'Status updated successfully']);
                // break;
                default:
                    // break;
            }
        }

        // $w->approved = $request->input('approved') === 'true' || $request->input('approved') === 1;
        // $approved = $request->input('status') === '1';
        // $w->approved = $approved;
        // $w->transaction_note = $request->input('note');
        // $w->save();

        // Toastr::success('Updated!');
        // return redirect()->back();

        return response()->json(['message' => 'Status updated successfully']);
    }


    public function status_filter(Request $request)
    {
        session()->put('withdraw_status_filter', $request['withdraw_status_filter']);
        return response()->json(session('withdraw_status_filter'));
    }
}
