<?php

namespace App\Http\Controllers\Reseller;

use App\Services\BackEndHelper;
use App\Services\Converter;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResellerWallet;

class WithdrawController extends Controller
{
    public function w_request(Request $request)
    {
        $wallet = ResellerWallet::where('reseller_id', auth()->guard('reseller')->user()->id)->first();

        // Display the details using json()
        // return response()->json([
        //     'Withdraw Request' => [
        //         'person' => 'reseller',
        //         'person_id' => auth()->guard('reseller')->user()->id,
        //         'amount' => Converter::usd($request['amount']),
        //     ],
        //     'Updated Wallet' => $wallet
        // ]);

        if (($wallet->total_earning) >= Converter::usd($request['amount']) && $request['amount'] > 1) {
            DB::table('withdraw_requests')->insert([
                'person' => 'reseller',
                'person_id' => auth()->guard('reseller')->user()->id,

                'amount' => Converter::usd($request['amount']),
                'transaction_note' => null,
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $wallet->total_earning -= Converter::usd($request['amount']);
            $wallet->pending_withdraw += Converter::usd($request['amount']);
            $wallet->save();
            Toastr::success('Withdraw request has been sent.');
            return redirect()->back();
        }

        Toastr::error('invalid request.!');
        return redirect()->back();
    }

    public function close_request($id)
    {
        $withdraw_request = WithdrawRequest::find($id);
        $wallet = ResellerWallet::where('reseller_id', auth()->guard('reseller')->user()->id)->first();
        if (isset($withdraw_request) && $withdraw_request->approved == 0) {
            $wallet->total_earning += Converter::usd($withdraw_request['amount']);
            $wallet->pending_withdraw -= Converter::usd($withdraw_request['amount']);
            $wallet->save();
            $withdraw_request->delete();
            Toastr::success('Request closed!');
        } else {
            Toastr::error('Invalid request');
        }

        return back();
    }

    public function status_filter(Request $request)
    {
        session()->put('withdraw_status_filter', $request['withdraw_status_filter']);
        return response()->json(session('withdraw_status_filter'));
    }

    public function list()
    {
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_requests = WithdrawRequest::with(['reseller'])
            ->where(['reseller_id' => auth('reseller')->id()])
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
            ->paginate(AdditionalServices::pagination_limit());

        return view('reseller-views.withdraw.list', compact('withdraw_requests'));
    }
}
