<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Currency;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('admin-views.business-settings.payment-method.index');
    }

    public function update(Request $request, $name)
    {

        if ($name == 'cash_on_delivery') {
            $payment = BusinessSetting::where('type', 'cash_on_delivery')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'type' => 'cash_on_delivery',
                    'value' => json_encode([
                        'status' => $request['status']
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('business_settings')->where(['type' => 'cash_on_delivery'])->update([
                    'type' => 'cash_on_delivery',
                    'value' => json_encode([
                        'status' => $request['status']
                    ]),
                    'updated_at' => now()
                ]);
            }
        } elseif ($name == 'digital_payment') {
            DB::table('business_settings')->updateOrInsert(['type' => 'digital_payment'], [
                'value' => json_encode([
                    'status' => $request['status']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } elseif ($name == 'ssl_commerz_payment') {
            $payment = BusinessSetting::where('type', 'ssl_commerz_payment')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'type' => 'ssl_commerz_payment',
                    'value' => json_encode([
                        'status' => 1,
                        'store_id' => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                if (Currency::where(['code' => 'BDT'])->first() == false) {
                    Toastr::error('Please add BDT currency before enable this gateway.');
                    return back();
                }
                if ($request['status'] == 1) {
                    $request->validate([
                        'store_id' => 'required',
                        'store_password' => 'required'
                    ]);
                }
                DB::table('business_settings')->where(['type' => 'ssl_commerz_payment'])->update([
                    'type' => 'ssl_commerz_payment',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'store_id' => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now()
                ]);
            }
        } elseif ($name == 'bkash') {
            DB::table('business_settings')->updateOrInsert(['type' => 'bkash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ]),
                'updated_at' => now()
            ]);
        }

        return back();
    }
}
