<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
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

            $user = Auth::guard('admin')->user();

            // If 2FA is enabled → redirect to OTP verification page
            if ($user->google2fa_status == 1 && !empty($user->google2fa_secret)) {

                session([
                    '2fa:admin:id' => $user->id,
                ]);

                Auth::guard('admin')->logout();

                return redirect()->route('admin.verify.two.factor');
            }
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password');
        }
    }

    public function verifyTwoFactorForm(Request $request)
    {

        $userId = session('2fa:admin:id');
        $user = Admin::find($userId);

        if (!$user || !$user->google2fa_secret) {
            return redirect()->route('adminLogin')
                ->withErrors(['msg' => '2FA setup required or user not found.']);
        }

        $google2fa = app('pragmarx.google2fa');
        $qrImage = $google2fa->getQRCodeInline(
            env('APP_NAME'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.verify-two-factor', [
            'qrCodeUrl' => $qrImage,
            'secretKey' => $user->google2fa_secret,
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required']);

        $userId = session('2fa:admin:id');
        $user = Admin::find($userId);

        if (!$user) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Session expired'])
                : redirect()->route('adminLogin')->withErrors(['Session expired']);
        }

        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Invalid 2FA code'])
                : back()->withErrors(['Invalid 2FA code']);
        }

        //  Clear temp 2FA session
        session()->forget('2fa:admin:id');

        // LOGIN WITH ADMIN GUARD
        Auth::guard('admin')->login($user);

        // Redirect to dashboard
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard');
    }
}
