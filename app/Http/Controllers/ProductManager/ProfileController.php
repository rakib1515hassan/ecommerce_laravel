<?php

namespace App\Http\Controllers\ProductManager;

use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\ProductManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    public function view()
    {
        $data = ProductManager::where('id', auth('product_manager')->id())->first();
        return view('product_manager-views.profile.view', compact('data'));
    }

    public function edit($id)
    {
        if (auth('product_manager')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_profile'));
            return back();
        }
        $data = ProductManager::where('id', auth('product_manager')->id())->first();
        return view('product_manager-views.profile.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $product_manager = ProductManager::find(auth('product_manager')->id());
        $product_manager->f_name = $request->f_name;
        $product_manager->l_name = $request->l_name;
        $product_manager->phone = $request->phone;
        if ($request->image) {
            $product_manager->image = ImageManager::update('product_manager/', $product_manager->image, 'png', $request->file('image'));
        }
        $product_manager->save();

        Toastr::info('Profile updated successfully!');
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $product_manager = ProductManager::find(auth('product_manager')->id());
        $product_manager->password = bcrypt($request['password']);
        $product_manager->save();
        Toastr::success('product_manager password updated successfully!');
        return back();
    }

    public function bank_update(Request $request, $id)
    {
        $bank = ProductManager::find(auth('product_manager')->id());
        $bank->bank_name = $request->bank_name;
        $bank->branch = $request->branch;
        $bank->holder_name = $request->holder_name;
        $bank->account_no = $request->account_no;
        $bank->save();
        Toastr::success('Bank Info updated');
        return redirect()->route('product_manager.profile.view');
    }

    public function bank_edit($id)
    {
        if (auth('product_manager')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_info'));
            return back();
        }
        $data = ProductManager::where('id', auth('product_manager')->id())->first();
        return view('product_manager-views.profile.bankEdit', compact('data'));
    }

}
