<?php

namespace App\Http\Controllers\Seller;

use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Reseller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ResellerController extends Controller
{
    public function index()
    {
        return view('seller-views.reseller.index');
    }

    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $resellers = Reseller::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $resellers = new Reseller();
        }

        $resellers = $resellers->latest()->where(['seller_id' => auth('seller')->id()])->paginate(25)->appends($query_param);
        return view('seller-views.reseller.list', compact('resellers', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $resellers = Reseller::where(['seller_id' => auth('seller')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('seller-views.reseller.partials._table', compact('resellers'))->render()
        ]);
    }

    public function preview($id)
    {
        $dm = Reseller::with(['reviews'])->where(['id' => $id])->first();
        return view('seller-views.reseller.view', compact('dm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required',
            'phone' => 'required|unique:resellers',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $reseller = Reseller::where(['email' => $request['email'], 'seller_id' => auth('seller')->id()])->first();
        $reseller_phone = Reseller::where(['phone' => $request['phone'], 'seller_id' => auth('seller')->id()])->first();

        if (isset($reseller)) {
            $request->validate([
                'email' => 'required|unique:resellers',
            ]);
        }

        if (isset($reseller_phone)) {
            $request->validate([
                'phone' => 'required|unique:resellers',
            ]);
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, ImageManager::upload('reseller/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new Reseller();
        $dm->seller_id = auth('seller')->id();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->image = ImageManager::upload('reseller/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();

        Toastr::success('Product Manager added successfully!');
        return redirect('seller/reseller/list');
    }

    public function edit($id)
    {
        $reseller = Reseller::where(['seller_id' => auth('seller')->id(), 'id' => $id])->first();
        return view('seller-views.reseller.edit', compact('reseller'));
    }

    public function status(Request $request)
    {
        $reseller = Reseller::find($request->id);
        $reseller->is_active = $request->status;
        $reseller->save();
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

        $reseller = Reseller::where(['id' => $id, 'seller_id' => auth('seller')->id()])->first();

        if (isset($reseller) && $request['email'] != $reseller['email']) {
            $request->validate([
                'email' => 'required|unique:resellers',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($reseller['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('reseller/' . $img)) {
                    Storage::disk('public')->delete('reseller/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                array_push($img_keeper, ImageManager::upload('reseller/', 'png', $img));
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $reseller['identity_image'];
        }
        $reseller->seller_id = auth('seller')->id();
        $reseller->f_name = $request->f_name;
        $reseller->l_name = $request->l_name;
        $reseller->email = $request->email;
        $reseller->phone = $request->phone;
        $reseller->identity_number = $request->identity_number;
        $reseller->identity_type = $request->identity_type;
        $reseller->image = $request->has('image') ? ImageManager::update('reseller/', $reseller->image, 'png', $request->file('image')) : $reseller->image;
        $reseller->password = strlen($request->password) > 1 ? bcrypt($request->password) : $reseller['password'];
        $reseller->save();

        Toastr::success('Product Manager updated successfully!');
        return redirect('seller/reseller/list');
    }

    public function delete(Request $request,$id)
    {

        $reseller = Reseller::where(['seller_id' => auth('seller')->id(), 'id' => $id])->first();


        if (Storage::disk('public')->exists('reseller/' . $reseller['image'])) {
            Storage::disk('public')->delete('reseller/' . $reseller['image']);
        }


        $reseller->delete();
        Toastr::success(translate('product Manager removed!'));
        return back();
    }
}
