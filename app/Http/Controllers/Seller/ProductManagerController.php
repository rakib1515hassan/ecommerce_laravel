<?php

namespace App\Http\Controllers\Seller;

use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\ProductManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductManagerController extends Controller
{
    public function index()
    {
        return view('seller-views.product_manager.index');
    }

    public function list(Request $request)
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

        $product_managers = $product_managers->latest()->where(['seller_id' => auth('seller')->id()])->paginate(25)->appends($query_param);
        return view('seller-views.product_manager.list', compact('product_managers', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $product_managers = ProductManager::where(['seller_id' => auth('seller')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('seller-views.product_manager.partials._table', compact('product_managers'))->render()
        ]);
    }

    public function preview($id)
    {
        $dm = ProductManager::with(['reviews'])->where(['id' => $id])->first();
        return view('seller-views.product_manager.view', compact('dm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required',
            'phone' => 'required|unique:product_managers',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $product_manager = ProductManager::where(['email' => $request['email'], 'seller_id' => auth('seller')->id()])->first();
        $product_manager_phone = ProductManager::where(['phone' => $request['phone'], 'seller_id' => auth('seller')->id()])->first();

        if (isset($product_manager)) {
            $request->validate([
                'email' => 'required|unique:product_managers',
            ]);
        }

        if (isset($product_manager_phone)) {
            $request->validate([
                'phone' => 'required|unique:product_managers',
            ]);
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, ImageManager::upload('product_manager/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new ProductManager();
        $dm->seller_id = auth('seller')->id();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->image = ImageManager::upload('product_manager/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();

        Toastr::success('Product Manager added successfully!');
        return redirect('seller/product_manager/list');
    }

    public function edit($id)
    {
        $product_manager = ProductManager::where(['seller_id' => auth('seller')->id(), 'id' => $id])->first();
        return view('seller-views.product_manager.edit', compact('product_manager'));
    }

    public function status(Request $request)
    {
        $product_manager = ProductManager::find($request->id);
        $product_manager->is_active = $request->status;
        $product_manager->save();
        return response()->json([], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $product_manager = ProductManager::where(['id' => $id, 'seller_id' => auth('seller')->id()])->first();

        if (isset($product_manager) && $request['email'] != $product_manager['email']) {
            $request->validate([
                'email' => 'required|unique:product_managers',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($product_manager['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('product_manager/' . $img)) {
                    Storage::disk('public')->delete('product_manager/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                array_push($img_keeper, ImageManager::upload('product_manager/', 'png', $img));
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $product_manager['identity_image'];
        }
        $product_manager->seller_id = auth('seller')->id();
        $product_manager->f_name = $request->f_name;
        $product_manager->l_name = $request->l_name;
        $product_manager->email = $request->email;
        $product_manager->phone = $request->phone;
        $product_manager->identity_number = $request->identity_number;
        $product_manager->identity_type = $request->identity_type;
        $product_manager->image = $request->has('image') ? ImageManager::update('product_manager/', $product_manager->image, 'png', $request->file('image')) : $product_manager->image;
        $product_manager->password = strlen($request->password) > 1 ? bcrypt($request->password) : $product_manager['password'];
        $product_manager->save();

        Toastr::success('Product Manager updated successfully!');
        return redirect('seller/product_manager/list');
    }

    public function delete(Request $request,$id)
    {

        $product_manager = ProductManager::where(['seller_id' => auth('seller')->id(), 'id' => $id])->first();


        if (Storage::disk('public')->exists('product_manager/' . $product_manager['image'])) {
            Storage::disk('public')->delete('product_manager/' . $product_manager['image']);
        }


        $product_manager->delete();
        Toastr::success(translate('product Manager removed!'));
        return back();
    }
}
