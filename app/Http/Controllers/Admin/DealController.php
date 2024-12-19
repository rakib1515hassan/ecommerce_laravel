<?php

namespace App\Http\Controllers\Admin;

use App\Services\BackEndHelper;
use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\DealOfTheDay;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DealController extends Controller
{
    public function flash_index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $flash_deal = FlashDeal::where('deal_type', 'flash_deal')
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('title', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $flash_deal = FlashDeal::where('deal_type', 'flash_deal');
        }
        $flash_deal = $flash_deal->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);

        return view('admin-views.deal.flash-index', compact('flash_deal', 'search'));
    }

    public function flash_submit(Request $request)
    {
        $flash_deal_id = DB::table('flash_deals')->insertGetId([
            'title' => $request['title'][array_search('en', $request->lang)],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'background_color' => $request['background_color'],
            'text_color' => $request['text_color'],
            'banner' => $request->has('image') ? ImageManager::upload('deal/', 'png', $request->file('image')) : 'def.png',
            'slug' => Str::slug($request['title'][array_search('en', $request->lang)]),
            'featured' => $request['featured'] == 1 ? 1 : 0,
            'deal_type' => $request['deal_type'] == 'flash_deal' ? 'flash_deal' : 'feature_deal',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($flash_deal_id) {
            foreach ($request->lang as $index => $key) {
                if ($request->title[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Models\FlashDeal',
                            'translationable_id' => $flash_deal_id,
                            'locale' => $key,
                            'key' => 'title'],
                        ['value' => $request->title[$index]]
                    );
                }
            }
        }

        Toastr::success('Deal added successfully!');
        return back();
    }

    public function edit($deal_id)
    {
        $deal = FlashDeal::withoutGlobalScope('translate')->find($deal_id);
        return view('admin-views.deal.flash-update', compact('deal'));
    }

    public function feature_edit($deal_id)
    {
        $deal = FlashDeal::withoutGlobalScope('translate')->find($deal_id);
        return view('admin-views.deal.feature-update', compact('deal'));
    }

    public function update(Request $request, $deal_id)
    {
        $deal = FlashDeal::find($deal_id);
        if ($request->image) {
            $deal['banner'] = ImageManager::update('deal/', $deal['banner'], 'png', $request->file('image'));
        }

        DB::table('flash_deals')->where(['id' => $deal_id])->update([
            'title' => $request['title'][array_search('en', $request->lang)],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'background_color' => $request['background_color'],
            'text_color' => $request['text_color'],
            'banner' => $deal['banner'],
            'slug' => Str::slug($request['title'][array_search('en', $request->lang)]),
            'featured' => $request['featured'] == 'on' ? 1 : 0,
            'deal_type' => $request['deal_type'] == 'flash_deal' ? 'flash_deal' : 'feature_deal',
            // 'deal_type'        => $request['feature_deal'] == 2 ? 2 : 0,
            'status' => $deal['status'],
            'updated_at' => now(),
        ]);

        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\FlashDeal',
                        'translationable_id' => $deal_id,
                        'locale' => $key,
                        'key' => 'title'],
                    ['value' => $request->title[$index]]
                );
            }
        }

        Toastr::success('Deal updated successfully!');
        return back();
    }

    public function status_update(Request $request)
    {
        //FlashDeal::where(['status' => 1])->where(['deal_type' => 'flash_deal'])->update(['status' => 0]);
        FlashDeal::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function feature_status(Request $request)
    {

        FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->update(['status' => 0]);
        FlashDeal::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function featured_update(Request $request)
    {
        // FlashDeal::where(['featured' => 1])->update(['featured' => 0]);
        FlashDeal::where(['id' => $request['id']])->update([
            'featured' => $request['featured'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }


    // Feature Deal
    public function feature_index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $flash_deals = FlashDeal::where('deal_type', 'feature_deal')
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('title', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $flash_deals = FlashDeal::where('deal_type', 'feature_deal');
        }
        $flash_deals = $flash_deals->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.deal.feature-index', compact('flash_deals', 'search'));
    }

    public function add_product($deal_id)
    {
        $flash_deal_products = FlashDealProduct::with('product')->where('flash_deal_id', $deal_id)->paginate(AdditionalServices::pagination_limit());

//        $products = Product::whereIn('id', $flash_deal_products->pluck('product_id'))->paginate(helpers::pagination_limit());


        $deal = FlashDeal::with(['products.product'])->where('id', $deal_id)->first();

        return view('admin-views.deal.add-product', [
            'flash_deal_products' => $flash_deal_products,
            'deal' => $deal,
        ]);
    }

    public function add_product_submit(Request $request, $deal_id)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        if ($request->has('discount') || $request->has('discount_type')) {
            $request->validate([
                'discount' => 'required|numeric',
                'discount_type' => 'required|in:flat,percent',
            ]);
        }

        if ($request->discount_type == 'percent') {
            $request->validate([
                'discount' => 'required|numeric|max:100',
            ]);
        }

        $product = Product::find($request->product_id);

        $discount = $product->discount;
        $discount_type = $product->discount_type;

        if ($request->discount) {
            $discount = $request->discount;
            $discount_type = $request->discount_type;
        }

        $flash_deal_product = FlashDealProduct::where('flash_deal_id', $deal_id)->where('product_id', $request->product_id)->first();

        if ($flash_deal_product) {
            $flash_deal_product->update([
                'discount' => $discount ?? 0,
                'discount_type' => $discount_type ?? 'percent',
            ]);
        } else {
            FlashDealProduct::create([
                'flash_deal_id' => $deal_id,
                'product_id' => $request->product_id,
                'discount' => $discount ?? 0,
                'discount_type' => $discount_type ?? 'percent',
                'seller_is' => 'admin',
                'status' => 1
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => 1,
            ], 200);
        }

        return back();
    }

    public function delete_product($deal_product_id)
    {
        FlashDealProduct::where(['product_id' => $deal_product_id])->delete();
        return back();
    }

    public function deal_of_day(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $deals = DealOfTheDay::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $deals = new DealOfTheDay();
        }
        $deals = $deals->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.deal.day-index', compact('deals', 'search'));
    }

    public function deal_of_day_submit(Request $request)
    {
        $product = Product::find($request['product_id']);
        $deal_id = DB::table('deal_of_the_days')->insertGetId([
            'title' => $request['title'][array_search('en', $request->lang)],
            'discount' => $product['discount_type'] == 'amount' ? BackEndHelper::currency_to_usd($product['discount']) : $product['discount'],
            'discount_type' => $product['discount_type'],
            'product_id' => $request['product_id'],
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($deal_id) {
            foreach ($request->lang as $index => $key) {
                if ($request->title[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Models\DealOfTheDay',
                            'translationable_id' => $deal_id,
                            'locale' => $key,
                            'key' => 'title'],
                        ['value' => $request->title[$index]]
                    );
                }
            }
        }

        Toastr::success('Deal added successfully!');
        return back();
    }

    public function day_status_update(Request $request)
    {
        DealOfTheDay::where(['status' => 1])->update(['status' => 0]);
        DealOfTheDay::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function day_edit($deal_id)
    {
        $deal = DealOfTheDay::withoutGlobalScope('translate')->find($deal_id);
        return view('admin-views.deal.day-update', compact('deal'));
    }

    public function day_update(Request $request, $deal_id): \Illuminate\Http\RedirectResponse
    {
        $product = Product::find($request['product_id']);
        DB::table('deal_of_the_days')->where(['id' => $deal_id])->update([
            'title' => $request['title'][array_search('en', $request->lang)],
            'discount' => $product['discount_type'] == 'amount' ? BackEndHelper::currency_to_usd($product['discount']) : $product['discount'],
            'discount_type' => $product['discount_type'],
            'product_id' => $request['product_id'],
            'status' => 0,
            'updated_at' => now(),
        ]);

        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\DealOfTheDay',
                        'translationable_id' => $deal_id,
                        'locale' => $key,
                        'key' => 'title'],
                    ['value' => $request->title[$index]]
                );
            }
        }

        Toastr::success('Deal updated successfully!');
        return redirect()->route('admin.deal.day');
    }

    public function day_delete($id)
    {
        DealOfTheDay::destroy($id);
        Toastr::success('Deal removed successfully!');
        return back();
    }

    public function product_status_update(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'product_id' => 'required',
            'flash_deal_id' => 'required'
        ]);


        FlashDealProduct::where([
            'product_id' => $request->product_id,
            'flash_deal_id' => $request->flash_deal_id
        ])->update([
            'status' => (bool)$request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }
}
