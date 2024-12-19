<?php

namespace App\Http\Controllers\Reseller\Auth;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Models\Reseller;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Mail\RegistrationNotification;
use Illuminate\Support\Facades\Mail;


class RegisterController extends Controller
{
    public function create()
    {
        return view('reseller-views.auth.register');
    }


    // public function store(Request $request,$sellerId, $productManagerId)
    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|unique:resellers',
            'phone' => 'required|unique:resellers',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

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
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->image = ImageManager::upload('reseller/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();


        Toastr::success('Registration Apply successfully!! Please, wait for the seller approval.');
        return redirect()->route('reseller.auth.login');
    }
}
