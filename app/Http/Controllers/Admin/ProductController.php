<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Color;
use App\Models\DealOfTheDay;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\ProductManager;
use App\Models\Review;
use App\Models\Translation;
use App\Models\Wishlist;
use App\Services\AdditionalServices;
use App\Services\BackEndHelper;
use App\Services\ImageManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductController extends BaseController
{
    public function add_new(): Factory|View|Application
    {
        $cat = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $product_managers = ProductManager::where('is_active', 1)->get();

        return view('admin-views.product.add-new', compact('cat', 'br', 'product_managers'));
    }

    public function featured_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->featured = ($product['featured'] == 0 || $product['featured'] == null) ? 1 : 0;
        $product->save();
        $data = $request->status;
        return response()->json($data);
    }

    public function approve_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = ($product['request_status'] == 0) ? 1 : 0;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => $product['request_status']]);
    }

    public function deny(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = 2;
        $product->denied_note = $request->denied_note;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => 2]);
    }

    public function view($id)
    {
        $product = Product::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->paginate(AdditionalServices::pagination_limit());
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit' => 'required',
            'images' => 'required',
            'image' => 'required',
            'tax' => 'required|min:0',
            'unit_price' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:1',
            'policy' => 'required',
            'sku' => 'nullable|unique:products,sku',
        ], [
            'images.required' => 'Product images is required!',
            'image.required' => 'Product thumbnail is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
            'unit.required' => 'Unit  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['unit_price'] <= $dis) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'unit_price',
                    'Discount can not be more or equal to the price!'
                );
            });
        }


        $p = new Product();
        $p->user_id = auth('admin')->id();
        $p->added_by = "admin";
        $p->name = $request->name[array_search('en', $request->lang)];
        $p->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);
        $p->sku = $request->sku;

        $category = [];

        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $p->category_ids = json_encode($category);
        $p->brand_id = $request->brand_id;
        $p->unit = $request->unit;
        $p->details = $request->description[array_search('en', $request->lang)];

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $p->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $p->colors = json_encode($colors);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = array_map(function ($value) {
                    return trim($value);
                }, explode(',', implode('|', $request[$str])));
                $choice_options[] = $item;
            }
        }
        $p->choice_options = json_encode($choice_options);

        //combinations start
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            $options[] = $request->colors;
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', trim($my_str));
            }
        }
        //Generates the combinations of customer choice options

        $combinations = AdditionalServices::combinations($options);

        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (int) $request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        //combinations end
        $p->variation = json_encode($variations);
        $p->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $p->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $p->tax = $request->tax_type == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        $p->tax_type = $request->tax_type;
        $p->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->attributes = json_encode($request->choice_attributes);
        $p->current_stock = abs($stock_count);

        $p->video_provider = 'youtube';
        $p->video_url = $request->video_link;
        $p->request_status = 1;
        $p->weight = $request->weight;
        $p->policy = $request->policy;

        $p->product_manager_id = $request->has('product_manager_id') ? $request->product_manager_id : null;
        $p->product_manager_amount = $request->has('product_manager_amount') ? $request->product_manager_amount : 0;
        $p->is_admin_manage = $request->has('is_admin_manage') ? $request->is_admin_manage : 0;
        $p->seller_amount = $request->has('seller_amount') ? $request->seller_amount : 0;
        $p->reseller_amount = $request->has('reseller_amount') ? $request->reseller_amount : 0;

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('product/', 'png', $request->image);

            $p->meta_title = $request->meta_title;
            $p->meta_description = $request->meta_description;
            $p->meta_image = ImageManager::upload('product/meta/', 'png', $request->meta_image);

            $p->save();

            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    $data[] = array(
                        'translationable_type' => 'App\Models\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    $data[] = array(
                        'translationable_type' => 'App\Models\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    );
                }
            }
            Translation::insert($data);

            Toastr::success(translate('Product added successfully!'));
            return redirect()->route('admin.product.list', ['in_house']);
        }
    }

    function list(Request $request, $type)
    {
        $query_param = [];
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin']);
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];
        $pro = $pro->orderBy('id', 'DESC')->paginate(AdditionalServices::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.list', compact('pro', 'search', 'request_status'));
    }

    public function stock_limit_list(Request $request, $type)
    {
        $stock_limit = AdditionalServices::get_business_settings('stock_limit');
        $sort_oqrderQty = $request['sort_oqrderQty'];
        $query_param = $request->all();
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin']);
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];

        $pro = $pro->withCount('order_details')->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
            return $q->orderBy('current_stock', 'asc');
        })
            ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                return $q->orderBy('current_stock', 'desc');
            })
            ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'asc');
            })
            ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'desc');
            })
            ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                return $q->orderBy('id');
            })->where('current_stock', '<', $stock_limit);

        $pro = $pro->orderBy('id', 'DESC')->paginate(AdditionalServices::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.stock-limit-list', compact('pro', 'search', 'request_status', 'sort_oqrderQty'));
    }

    public function update_quantity(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $product = Product::find($request['product_id']);
        if ($stock_count >= 0) {
            $product->current_stock = $stock_count;
            $product->variation = json_encode($variations);
            $product->save();
            Toastr::success(translate('product_quantity_updated_successfully!'));
            return back();
        } else {
            Toastr::warning(translate('product_quantity_can_not_be_less_than_0_!'));
            return back();
        }
    }

    // Seller Product Status
    public function status_update(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        $product->status = !$product->status;
        $product->save();

        return response()->json(['success' => true]);
    }

    // public function status_update(Request $request)
    // {
    //     $product = Product::where(['id' => $request['id']])->first();
    //     $success = 1;
    //     if ($request['status'] == 1) {
    //         if ($product->added_by == 'seller' && $product->request_status == 0) {
    //             $success = 0;
    //         } else {
    //             $product->status = $request['status'];
    //         }
    //     } else {
    //         $product->status = $request['status'];
    //     }
    //     $product->save();
    //     return response()->json([
    //         'success' => $success,
    //     ], 200);
    // }

    
    
    
    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }

    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', trim($my_str));
            }
        }

        $combinations = AdditionalServices::combinations($options);
        return response()->json([
            'view' => view('admin-views.product.partials._sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'))->render(),
        ]);
    }

    public function get_variations(Request $request)
    {
        $product = Product::find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function edit($id)
    {
        $product = Product::withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();

        $seller = null;
        if ($product->added_by == 'seller') {
            $seller = DB::table('sellers')->where('id', $product->user_id)->first();
        }

        if ($seller) {
            $product_managers = ProductManager::where('is_active', 1)->where('seller_id', $seller->id)->get();
        } else {
            $product_managers = ProductManager::where('is_active', 1)->get();
        }

        return view('admin-views.product.edit', compact('categories', 'br', 'product', 'product_category', 'seller', 'product_managers'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit' => 'required',
            'tax' => 'required|min:0',
            'unit_price' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:1',
            'sku' => 'nullable|unique:products,sku,' . $id,
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
            'unit.required' => 'Unit  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['unit_price'] <= $dis) {
            $validator->after(function ($validator) {
                $validator->errors()->add('unit_price', 'Discount can not be more or equal to the price!');
            });
        }

        $product = Product::find($id);
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->sku = $request->sku;
        $product->category_ids = json_encode($category);
        $product->brand_id = $request->brand_id;
        $product->unit = $request->unit;
        $product->details = $request->description[array_search('en', $request->lang)];
        $product_images = json_decode($product->images);

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $product->colors = json_encode($colors);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = array_map(function ($value) {
                    return trim($value);
                }, explode(',', implode('|', $request[$str])));
                $choice_options[] = $item;
            }
        }
        $product->choice_options = json_encode($choice_options);
        $variations = [];
        //combinations start
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', trim($my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = AdditionalServices::combinations($options);
        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (int) $request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->withInput();
        }

        //combinations end
        $product->variation = json_encode($variations);
        $product->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $product->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $product->tax = $request->tax == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $product->attributes = json_encode($request->choice_attributes);
        $product->discount_type = $request->discount_type;
        $product->current_stock = abs($stock_count);
        $product->weight = $request->weight;
        $product->policy = $request->policy;

        $product->video_provider = 'youtube';
        $product->video_url = $request->video_link;
        if ($product->added_by == 'seller' && $product->request_status == 2) {
            $product->request_status = 1;
        }

        $product->product_manager_id = $request->has('product_manager_id') ? $request->product_manager_id : $product->product_manager_id;
        $product->product_manager_amount = $request->has('product_manager_amount') ? $request->product_manager_amount : $product->product_manager_amount;
        $product->is_admin_manage = $request->has('is_admin_manage') ? $request->is_admin_manage : $product->is_admin_manage;
        $product->seller_amount = $request->has('seller_amount') ? $request->seller_amount : $product->seller_amount;
        $product->reseller_amount = $request->has('reseller_amount') ? $request->reseller_amount : $product->reseller_amount;


        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            }

            if ($request->file('image')) {
                $product->thumbnail = ImageManager::update('product/', $product->thumbnail, 'png', $request->file('image'));
            }

            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            if ($request->file('meta_image')) {
                $product->meta_image = ImageManager::update('product/meta/', $product->meta_image, 'png', $request->file('meta_image'));
            }

            $product->save();

            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $request->name[$index]]
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $request->description[$index]]
                    );
                }
            }
            Toastr::success('Product updated successfully.');
            return back();
        }
    }

    public function remove_image(Request $request)
    {
        ImageManager::delete('/product/' . $request['image']);
        $product = Product::find($request['id']);
        $array = [];
        if (count(json_decode($product['images'])) < 2) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }
        foreach (json_decode($product['images']) as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success('Product image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Product::find($id);

        $translation = Translation::where('translationable_type', 'App\Models\Product')
            ->where('translationable_id', $id);
        $translation->delete();

        Cart::where('product_id', $product->id)->delete();
        Wishlist::where('product_id', $product->id)->delete();

        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/product/' . $image);
        }
        ImageManager::delete('/product/' . $product['thumbnail']);
        $product->delete();

        FlashDealProduct::where(['product_id' => $id])->delete();
        DealOfTheDay::where(['product_id' => $id])->delete();

        Toastr::success('Product removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    // public function bulk_import_data(Request $request)
    // {
    //     try {
    //         $collections = (new FastExcel)->import($request->file('products_file'));
    //     } catch (\Exception $exception) {
    //         Toastr::error('You have uploaded a wrong format file, please upload the right file.');
    //         return back();
    //     }

    //     $data = [];
    //     $skip = ['youtube_video_url', 'details'];
    //     foreach ($collections as $collection) {
    //         foreach ($collection as $key => $value) {
    //             if ($value === "" && !in_array($key, $skip)) {
    //                 Toastr::error('Please fill ' . $key . ' fields');
    //                 return back();
    //             }
    //         }

    //         $thumbnail = explode('/', $collection['thumbnail']);

    //         $data[] = [
    //             'name' => $collection['name'],
    //             'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
    //             'category_ids' => json_encode([['id' => $collection['category_id'], 'position' => 0], ['id' => $collection['sub_category_id'], 'position' => 1], ['id' => $collection['sub_sub_category_id'], 'position' => 2]]),
    //             'brand_id' => $collection['brand_id'],
    //             'unit' => $collection['unit'],
    //             'min_qty' => $collection['min_qty'],
    //             'refundable' => $collection['refundable'],
    //             'unit_price' => $collection['unit_price'],
    //             'purchase_price' => $collection['purchase_price'],
    //             'tax' => $collection['tax'],
    //             'discount' => $collection['discount'],
    //             'discount_type' => $collection['discount_type'],
    //             'current_stock' => $collection['current_stock'],
    //             'details' => $collection['details'],
    //             'video_provider' => 'youtube',
    //             'video_url' => $collection['youtube_video_url'],
    //             'images' => json_encode(['def.png']),
    //             'thumbnail' => $thumbnail[1],
    //             'status' => 1,
    //             'request_status' => 1,
    //             'colors' => json_encode([]),
    //             'attributes' => json_encode([]),
    //             'choice_options' => json_encode([]),
    //             'variation' => json_encode([]),
    //             'featured_status' => 1,
    //             'added_by' => 'admin',
    //             'user_id' => auth('admin')->id(),
    //         ];
    //     }
    //     DB::table('products')->insert($data);
    //     Toastr::success(count($data) . ' - Products imported successfully!');
    //     return back();
    // }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        $data = [];
        $skip = ['youtube_video_url', 'details'];
        foreach ($collections as $collection) {
            foreach ($collection as $key => $value) {
                if ($value === "" && !in_array($key, $skip)) {
                    Toastr::error('Please fill ' . $key . ' fields');
                    return back();
                }
            }

            $thumbnail_path = $collection['thumbnail'] ?? 'none_image.jpeg';

            // $thumbnail = explode('/', $collection['thumbnail'] ?? 'none_image.jpeg');
            // $thumbnail_path = count($thumbnail) > 1 ? $thumbnail[1] : 'none_image.jpeg';

            $data[] = [
                'name' => $collection['name'],
                'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
                'category_ids' => json_encode([['id' => $collection['category_id'], 'position' => 0], ['id' => $collection['sub_category_id'], 'position' => 1], ['id' => $collection['sub_sub_category_id'], 'position' => 2]]),
                'brand_id' => $collection['brand_id'],
                'unit' => $collection['unit'],
                'min_qty' => $collection['min_qty'],
                'refundable' => $collection['refundable'],
                'unit_price' => $collection['unit_price'],
                'purchase_price' => $collection['purchase_price'],
                'tax' => $collection['tax'],
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'current_stock' => $collection['current_stock'],
                'details' => $collection['details'],
                'video_provider' => 'youtube',
                'video_url' => $collection['youtube_video_url'],
                'images' => $collection['images'],
                'thumbnail' => $thumbnail_path,
                'status' => 1,
                'request_status' => 1,
                'colors' => json_encode([]),
                'attributes' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'featured_status' => 1,
                'added_by' => 'admin',
                'user_id' => auth('admin')->id(),
            ];
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . ' - Products imported successfully!');
        return back();
    }


    public function bulk_export_data()
    {
        $products = Product::where(['added_by' => 'admin'])->get();
        //export from product
        $storage = [];
        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            $sub_sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } else if ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                } else if ($category['position'] == 3) {
                    $sub_sub_category_id = $category['id'];
                }
            }
            $storage[] = [
                'name' => $item->name,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'sub_sub_category_id' => $sub_sub_category_id,
                'brand_id' => $item->brand_id,
                'unit' => $item->unit,
                'min_qty' => $item->min_qty,
                'refundable' => $item->refundable,
                'youtube_video_url' => $item->video_url,
                'unit_price' => $item->unit_price,
                'purchase_price' => $item->purchase_price,
                'tax' => $item->tax,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'current_stock' => $item->current_stock,
                'details' => $item->details,
                'thumbnail' => 'thumbnail/' . $item->thumbnail,
            ];
        }
        return (new FastExcel($storage))->download('inhouse_products.xlsx');
    }

    public function product_manager(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $product_managers = ProductManager::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $product_managers = new ProductManager();
        }

        $product_managers = $product_managers->latest()->paginate(25)->appends($query_param);
        // dd($product_managers);
        return view('admin-views.product.product-manager-list', compact('product_managers', 'search'));
    }

    public function product_manager_status(Request $request)
    {
        $product_manager = ProductManager::find($request->id);
        $product_manager->is_active = $request->status;
        $product_manager->save();
        $data = $request->status;
        return response()->json($data);
    }
}
