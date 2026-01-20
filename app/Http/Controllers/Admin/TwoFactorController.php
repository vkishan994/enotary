<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\TwoFactorRecoveryMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class TwoFactorController extends Controller
{

    public function showRecoveryForm()
    {
        $routeName = Route::currentRouteName();

        if (str_starts_with($routeName, 'admin.')) {
            return view('auth.admin.recover_2fa');
        }

        if (str_starts_with($routeName, 'user.')) {
            return view('auth.user_recover_two_factor');
        }

        abort(404);
        // return view('auth.admin.recover_2fa');
    }

    // public function sendRecoveryLink(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:admins,email',
    //     ]);

    //     $admin = Admin::where('email', $request->email)->first();

    //     $token = Str::random(64);

    //     $admin->update([
    //         'two_factor_recovery_token' => hash('sha256', $token),
    //         'two_factor_recovery_expires_at' => now()->addMinutes(10),
    //     ]);

    //     Mail::to($admin->email)->send(
    //         new TwoFactorRecoveryMail($token)
    //     );

    //     return back()->with('success', 'Recovery link sent to your email.');
    // }

    public function sendRecoveryLink(Request $request)
    {
        $routeName = Route::currentRouteName();

        // Detect context
        if (str_starts_with($routeName, 'admin.')) {
            $model = Admin::class;
            $emailTable = 'admins';
        } elseif (str_starts_with($routeName, 'user.')) {
            $model = User::class;
            $emailTable = 'users';
        } else {
            abort(404);
        }

        // Validate email against correct table
        $request->validate([
            'email' => "required|email|exists:$emailTable,email",
        ]);

        // Fetch user/admin
        $account = $model::where('email', $request->email)->first();

        // Generate token
        $token = Str::random(64);

        // Store hashed token with expiry
        $account->update([
            'two_factor_recovery_token' => hash('sha256', $token),
            'two_factor_recovery_expires_at' => now()->addMinutes(10),
        ]);

        // Send recovery email
        Mail::to($account->email)->send(
            new TwoFactorRecoveryMail($token, $routeName)
        );

        return back()->with('success', 'Recovery link sent to your email.');
    }


    // public function resetTwoFactor(string $token)
    // {
    //     $admin = Admin::where('two_factor_recovery_token', hash('sha256', $token))
    //         ->where('two_factor_recovery_expires_at', '>', now())
    //         ->first();

    //     if (!$admin) {
    //         return redirect()->route('admin.2fa.recover')
    //             ->withErrors(['email' => 'Invalid or expired recovery link.']);
    //     }

    //     $admin->update([
    //         'google2fa_secret' => null,
    //         'google2fa_status' => 0,
    //         'two_factor_recovery_token' => null,
    //         'two_factor_recovery_expires_at' => null,
    //     ]);

    //     Auth::guard('admin')->login($admin);

    //     return redirect()->route('admin.edit.profile')
    //         ->with('success', 'Please set up two-factor authentication again.');
    // }

    public function resetTwoFactor(string $token)
    {
        $routeName = Route::currentRouteName();

        // Detect context
        if (str_starts_with($routeName, 'admin.')) {
            $model = Admin::class;
            $guard = 'admin';
            $recoverRoute = 'admin.2fa.recover';
            $redirectRoute = 'admin.edit.profile';
        } elseif (str_starts_with($routeName, 'user.')) {
            $model = User::class;
            $guard = 'web';
            $recoverRoute = 'user.2fa.recover';
            $redirectRoute = 'user.update-profile.user-form';
        } else {
            abort(404);
        }

        // Find account with valid token
        $account = $model::where(
            'two_factor_recovery_token',
            hash('sha256', $token)
        )
            ->where('two_factor_recovery_expires_at', '>', now())
            ->first();

        // Invalid or expired token
        if (!$account) {
            return redirect()->route($recoverRoute)
                ->withErrors(['email' => 'Invalid or expired recovery link.']);
        }

        // Reset 2FA
        $account->update([
            'google2fa_secret' => null,
            'google2fa_status' => 0,
            'two_factor_recovery_token' => null,
            'two_factor_recovery_expires_at' => null,
        ]);

        // Login user/admin
        Auth::guard($guard)->login($account);

        return redirect()->route($redirectRoute)
            ->with('success', 'Please set up two-factor authentication again.');
    }
}
