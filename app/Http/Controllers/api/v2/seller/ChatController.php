<?php

namespace App\Http\Controllers\api\v2\seller;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Chatting;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ChatController extends Controller
{
    public function messages(Request $request)
    {
        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        try {
            $messages = Chatting::with(['seller_info', 'customer', 'order'])->where('seller_id', $seller['id'])->latest()
            ->get();
            return response()->json($messages, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function send_message(Request $request)
    {
        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        if ($request->message == '') {
            return response()->json(translate('type something!'), 200);
        } else {
            $shop_id = Shop::where('seller_id', $seller['id'])->first()->id;
            $message = $request->message;
            $time = now();

            DB::table('chattings')->insert([
                'user_id' => $request->user_id, //user_id == seller_id
                'shop_id' => $shop_id,
                'seller_id' => $seller['id'],
                'message' => $request->message,
                'sent_by_seller' => 1,
                'seen_by_seller' => 0,
                'created_at' => now(),
            ]);
            return response()->json(['message' => $message, 'time' => $time]);
        }
    }
}
