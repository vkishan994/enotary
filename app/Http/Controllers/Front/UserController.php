<?php

namespace App\Http\Controllers\Front;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function verifyTwoFactorForm()
    {
        $userId = session('2fa:user:id');
        $user = User::find($userId);

        if (!$user || !$user->google2fa_secret) {
            return redirect()->route('adminLogin')
                ->withErrors(['msg' => '2FA session expired.']);
        }

        return view('auth.verify-two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'code' => 'required|digits:6',
            ]);

            $userId = session('2fa:user:id');

            // Session expired
            if (!$userId) {
                return redirect()
                    ->back()
                    ->withErrors(['code' => '2FA session expired. Please login again.'])
                    ->withInput();
            }

            $user = User::find($userId);

            // User or secret missing
            if (!$user || !$user->google2fa_secret) {
                return redirect()
                    ->back()
                    ->withErrors(['code' => '2FA setup not found. Please login again.'])
                    ->withInput();
            }

            $google2fa = app('pragmarx.google2fa');

            // Invalid code
            if (!$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
                return redirect()
                    ->back()
                    ->withErrors(['code' => 'Invalid authentication code.'])
                    ->withInput();
            }

            // Clear temp 2FA session
            session()->forget('2fa:admin:id');

            // Login admin
            Auth::guard('admin')->login($user);

            return redirect()->route('dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors (Laravel already formats these)
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            // Any unexpected error
            report($e);

            return redirect()
                ->back()
                ->withErrors([
                    'code' => 'Something went wrong. Please try again.'
                ])
                ->withInput();
        }
    }

    public function generate(Request $request)
    {
        $user = Auth::guard('web')->user(); // use web guard for users

        // DISABLE 2FA
        if ($request->action === 'disable') {
            $user->update([
                'google2fa_secret' => null,
                'google2fa_status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication has been disabled.'
            ]);
        }

        $google2fa = app('pragmarx.google2fa');

        // Generate secret
        $secret = $google2fa->generateSecretKey();

        // Store temporarily in session
        session(['2fa_secret' => $secret]);

        // Generate QR code inline
        $qr = $google2fa->getQRCodeInline(config('app.name'), $user->email, $secret);

        return response()->json([
            'qr' => $qr,
            'secret' => $secret,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = Auth::guard('web')->user(); // use web guard for users
        $google2fa = app('pragmarx.google2fa');

        $secret = session('2fa_secret');

        if (!$secret) {
            return response()->json([
                'success' => false,
                'message' => '2FA session expired. Please try again.',
            ]);
        }

        if ($google2fa->verifyKey($secret, $request->otp)) {
            $user->update([
                'google2fa_secret' => $secret,
                'google2fa_status' => 1,
            ]);

            session()->forget('2fa_secret');

            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP',
        ]);
    }
}
