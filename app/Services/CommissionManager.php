<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\Reseller;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PointHistory;
use App\Models\Membership;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationSendManager;
use App\Services\SmsModule;

class CommissionManager
{
    public static function set_commission(Order $order): void
    {
        if ($order->is_delivered == 1) {
            return;
        }

        $order_details = $order->details;

        $sale_commission = BusinessSetting::where('type', 'sale_commission')->first()->value ?? 0;

        //$admin = Admin::where('id', 1)->first();

        $seller = null;
        if ($order->seller_id != null && $order->seller_is == 'seller') {
            $seller = Seller::where('id', $order->seller_id)->first();

            // wallet exists
            DB::table('seller_wallets')->where('seller_id', $seller->id)->exists() || DB::table('seller_wallets')->insert([
                'seller_id' => $seller->id,
                'total_earning' => 0,
                'commission_given' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $reseller = null;
        if ($order->reseller_id != null) {
            $reseller = Reseller::where('id', $order->reseller_id)->first();

            // wallet exists
            DB::table('reseller_wallets')->where('reseller_id', $reseller->id)->exists() || DB::table('reseller_wallets')->insert([
                'reseller_id' => $reseller->id,
                'total_earning' => 0,
                'commission_given' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        foreach ($order_details as $order_detail) {
            $product = json_decode($order_detail->product_details);

            $admin_amount = 0;
            $seller_amount = 0;
            $product_manager_amount = 0;
            $reseller_amount = 0;


            $total_amount = $order_detail->price * $order_detail->qty;
            $qty = $order_detail->qty;

            if ($product->product_manager_id != null && $product->product_manager_amount) {
                $pa = $product->product_manager_amount * $qty;
                $product_manager_amount += $pa;
                $total_amount -= $pa;
            }

            if ($reseller) {
                $extra_amount = $order_details->price - $product->price;
                $ra = ($extra_amount + ($product->reseller_amount ?? 0)) * $qty;
                $reseller_amount += $ra;
                $total_amount -= $ra;
            }

            if ($order->seller_is == 'admin') {
                $admin_amount += $total_amount;
            } else {
                if ($seller && $seller->admin_manage == 1) {
                    $sa = $product->seller_amount * $qty;
                    $seller_amount += $sa;
                    $total_amount -= $sa;
                    $admin_amount += $total_amount;
                } else {
                    // commission
                    $sa = ($total_amount * $sale_commission) / 100;

                    $seller_amount += ($total_amount - $sa);
                    $admin_amount += $sa;
                }
            }

            /// admin balance disbursement
            if ($admin_amount > 0) {
                if ($order->seller_is == 'admin') {
                    DB::table('admin_wallets')->where('admin_id', 1)->update([
                        'inhouse_earning' => DB::raw('inhouse_earning + ' . $admin_amount)
                    ]);


                } else {
                    DB::table('admin_wallets')->where('admin_id', 1)->update([
                        'commission_earned' => DB::raw('commission_earned + ' . $admin_amount)
                    ]);
                }

                DB::table('admin_wallet_histories')->insert([
                    'admin_id' => 1,
                    'amount' => $admin_amount,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'payment' => 'received',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            /// seller balance disbursement
            if ($seller && $seller_amount > 0) {
                DB::table('seller_wallets')->where('seller_id', $seller->id)->update([
                    'total_earning' => DB::raw('total_earning + ' . $seller_amount),
                    'commission_given' => DB::raw('commission_given + ' . $admin_amount)
                ]);

                DB::table('seller_wallet_histories')->insert([
                    'seller_id' => $seller->id,
                    'amount' => $seller_amount,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'payment' => 'received',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            /// product manager balance disbursement

            if ($product->product_manager_id != null && $product_manager_amount > 0) {
                // wallet exists
                DB::table('product_manager_wallets')->where('product_manager_id', $product->product_manager_id)->exists() || DB::table('product_manager_wallets')->insert([
                    'product_manager_id' => $product->product_manager_id,
                    'total_earning' => 0,
                    'commission_given' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);


                DB::table('product_manager_wallets')->where('product_manager_id', $product->product_manager_id)->update([
                    'total_earning' => DB::raw('total_earning + ' . $product_manager_amount),
                    'commission_given' => DB::raw('commission_given + ' . $admin_amount)
                ]);

                DB::table('product_manager_wallet_histories')->insert([
                    'product_manager_id' => $product->product_manager_id,
                    'amount' => $product_manager_amount,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'payment' => 'received',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            /// reseller balance disbursement

            if ($reseller && $reseller_amount > 0) {
                DB::table('reseller_wallets')->where('reseller_id', $reseller->id)->update([
                    'total_earning' => DB::raw('total_earning + ' . $reseller_amount),
                    'commission_given' => DB::raw('commission_given + ' . $admin_amount)
                ]);

                DB::table('reseller_wallet_histories')->insert([
                    'reseller_id' => $reseller->id,
                    'amount' => $reseller_amount,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'payment' => 'received',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // admin will get shipping cost
        DB::table('admin_wallets')->where('admin_id', 1)->update([
            'delivery_charge_earned' => DB::raw('delivery_charge_earned + ' . $order->shipping_cost)
        ]);

        DB::table('admin_wallet_histories')->insert([
            'admin_id' => 1,
            'amount' => $order->shipping_cost,
            'order_id' => $order->id,
            'payment' => 'received',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $order->is_delivered = 1;
        $order->save();
    }

    public static function customer_point_calculations(Order $order): void
    {
        $customer_id = $order->customer_id;
        $customer_type = $order->customer_type;
        $is_paid = $order->is_paid;
        $amount = $order->order_amount - $order->shipping_cost;

        $user = User::find($customer_id);
        $membership = $user ? $user->membership : null;

        if ($membership) {
            $pointForuser = $amount * 0.02;      // Calculate 2% of the amount
            $pointForreferred = $amount * 0.02;  // Calculate 2% of the amount


            // $pointForuser = $amount * env('POINT_FOR_USER');
            // $pointForreferred = $amount * env('POINT_FOR_REFERRED');

            $membership->points += $pointForuser;
            $membership->save();

            // Point For Self
            PointHistory::create([
                'user_id' => $user->id,
                'points' => $pointForuser,
                'order_id' => $order->id,
                'order_amount' => $amount,
                'status' => 'self',
            ]);

            //? SMS Send
            if ($user->phone) {
                $msg = "প্রিয় গ্রাহক, আপনি " . $amount . " টাকার পণ্য ক্রয় করায় " . $pointForuser . " পয়েন্ট রিওয়ার্ড পেয়েছেন এবং আপনার বর্তমান রিওয়ার্ড ব্যালেন্স " . $membership->points . " । \nস্বজনের সাথে থাকার জন্য ধন্যবাদ।";

                $res = SmsModule::sendSms_greenweb($user, $msg);
                Log::info('SMS Response = ' . $res);
            }

            // Instantiate NotificationSendManager
            $notificationManager = new NotificationSendManager();

            //? Send notification to user about points earned
            $token = $user->cm_firebase_token;

            if ($token) {
                try {

                    $title = "Bonus points earned!";
                    $message = 'You earned ' . $pointForuser . ' points for your order.';

                    $notificationManager->sendNotification($title, $message, $token);
                    Log::info('Notification sent successfully to user ' . $user->id);

                } catch (\Exception $e) {
                    Log::error('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            if (isset($membership->referred_from)) {
                $referredUser = User::find($membership->referred_from);
                $referredMembership = $referredUser ? $referredUser->membership : null;

                if ($referredMembership) {
                    $referredMembership->points += $pointForreferred;
                    $referredMembership->save();

                    // Point For Feferred Members
                    PointHistory::create([
                        'user_id' => $referredUser->id,
                        'referred_user' => $user->id,
                        'points' => $pointForreferred,
                        'order_id' => $order->id,
                        'order_amount' => $amount,
                        'status' => 'referred',
                    ]);

                    //? SMS Send
                    if ($referredUser->phone) {
                        $msg = "প্রিয় গ্রাহক, আপনার রেফারেলে " . $user->fullname . " " . $amount . " টাকার পণ্য ক্রয় করায় আপনি " . $pointForreferred . " পয়েন্ট রিওয়ার্ড পেয়েছেন এবং আপনার বর্তমান রিওয়ার্ড ব্যালেন্স " . $referredMembership->points . " । রিওয়ার্ড ব্যালেন্স দিয়ে কেনাকাটা করতে ভিজিট করুন ShojonSL.com";

                        $res = SmsModule::sendSms_greenweb($referredUser, $msg);
                        Log::info('SMS Response = ' . $res);
                    }

                    //? Send notification to referred user about points earned
                    $rtoken = $referredUser->cm_firebase_token;
                    if ($rtoken) {
                        try {

                            $rtitle = "Bonus points earned!";
                            $rmessage = 'You earned ' . $pointForreferred . ' bonus points for your referre user.';

                            $notificationManager->sendNotification($rtitle, $rmessage, $rtoken);
                            Log::info('Notification sent successfully to user ' . $referredUser->id);

                        } catch (\Exception $e) {
                            Log::error('Failed to send notification to user ' . $referredUser->id . ': ' . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

}


