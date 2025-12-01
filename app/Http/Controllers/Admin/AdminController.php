<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function Adminlogin(Request $request)
    {
        return view('auth.admin.login');
    }

    public function VerifyAdminlogin(Request $request)
    {
        // dd($request->all());
        $rules = [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'Email address is required',
            'email.email' => 'Valid email is required',
            'password.required' => 'Password is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('adminLogin')
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password');
        }
    }
}
