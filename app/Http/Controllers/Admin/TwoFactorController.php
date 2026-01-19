<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\TwoFactorRecoveryMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{

    public function showRecoveryForm()
    {
        return view('auth.admin.recover_2fa');
    }

    public function sendRecoveryLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $token = Str::random(64);

        $admin->update([
            'two_factor_recovery_token' => hash('sha256', $token),
            'two_factor_recovery_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($admin->email)->send(
            new TwoFactorRecoveryMail($token)
        );

        return back()->with('success', 'Recovery link sent to your email.');
    }

    public function resetTwoFactor(string $token)
    {
        $admin = Admin::where('two_factor_recovery_token', hash('sha256', $token))
            ->where('two_factor_recovery_expires_at', '>', now())
            ->first();

        if (!$admin) {
            return redirect()->route('admin.2fa.recover')
                ->withErrors(['email' => 'Invalid or expired recovery link.']);
        }

        $admin->update([
            'google2fa_secret' => null,
            'google2fa_status' => 0,
            'two_factor_recovery_token' => null,
            'two_factor_recovery_expires_at' => null,
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.edit.profile')
            ->with('success', 'Please set up two-factor authentication again.');
    }
}
