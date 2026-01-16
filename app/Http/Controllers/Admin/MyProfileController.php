<?php

namespace App\Http\Controllers\Admin;

use Stripe\Stripe;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\StripeClass;
use App\Rules\MatchOldPassword;
use App\Models\NotaryServiceType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class MyProfileController extends Controller
{
    protected $stripe;

    public function __construct(StripeClass $stripe)
    {
        $this->stripe = $stripe;
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();
        return view('admin.profile.form', compact('user'));
    }


    public function updateProfile(Request $request)
    {
        // dd($request->all());
        // $user = Auth::user();
        $user = Auth::guard('admin')->user();

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            // Validation for the name and email
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,

            // Validation for password change
            'password' => ['required_with:new_password', 'min:8', new MatchOldPassword],
            'new_password' => 'required_with:password|min:8|same:confirm_password',
            'confirm_password' => 'required_with:new_password|min:8',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->route('admin.edit.profile')
                ->withErrors($validator)
                ->withInput();
        }

        // Update the basic info (name, email)
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // Check and update password if fields are provided
        if ($request->has('password') && $request->has('new_password')) {
            // Ensure the current password matches
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->withErrors(['password' => 'Current password is incorrect.']);
            }

            // Update the password
            $user->password = Hash::make($request->new_password);
        }

        $google2faStatus = $request->boolean('google2fa_status'); // true / false

        if ($google2faStatus) {

            $google2fa = app('pragmarx.google2fa');

            // Generate secret only if not already present
            if (empty($user->google2fa_secret)) {
                $user->google2fa_secret = $google2fa->generateSecretKey();
            }

            $qrImage = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            session()->flash('2fa_secret', $user->google2fa_secret);
            session()->flash('2fa_qr', $qrImage);

            $user->google2fa_status = 1;
        } else {
            // Properly disable 2FA
            $user->google2fa_status = 0;
            $user->google2fa_secret = null;
            $user->save();
        }

        // Save the user
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }



    public function accountDashboard(Request $request)
    {
        $user = Auth::user();
        return view('front.dashboard', compact('user'));
    }


    public function notariseDocuments(Request $request)
    {
        $user = Auth::user();
        $notaryServiceTypes = NotaryServiceType::all();
        return view('front.notarise-documents', compact('user', 'notaryServiceTypes'));
    }

    public function getDocuments(Request $request)
    {
        $serviceTypeId = $request->service_type_id;
        $documents = Document::whereHas('notaryServiceTypes', function ($query) use ($serviceTypeId) {
            $query->where('notary_service_types.id', $serviceTypeId);
        })->get();

        return response()->json($documents);
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'service_type_id' => 'required|exists:notary_service_types,id',
        ]);

        Stripe::setApiKey(getValuesByKey('stripe_secret_key'));

        $document = Document::findOrFail($request->document_id);
        $user = Auth::user();

        // Create Payment Intent using our StripeClass service
        $payment = $this->stripe->createPayment($document->price, 'gbp', [
            'user_id' => $user->id,
            'document_id' => $document->id,
            'service_type_id' => $request->service_type_id
        ]);

        if ($payment['success']) {
            // Create pending order
            $order = Order::create([
                'user_id' => $user->id,
                'document_id' => $document->id,
                'notary_service_type_id' => $request->service_type_id,
                'amount' => $document->price,
                'stripe_payment_intent_id' => $payment['payment_intent_id'],
                'payment_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $payment['client_secret'],
                'order_id' => $order->id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $payment['message']
        ], 400);
    }

    public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');

        if ($paymentIntentId) {
            $order = Order::where('stripe_payment_intent_id', $paymentIntentId)->first();
            // if ($order) {
            //     $order->update(['payment_status' => 'completed']);
            // }
        }

        return view('front.payment-success');
    }

    public function updateUserProfileForm(Request $request)
    {
        return view('front.user.update-profile');
    }

    public function updateUserProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->update($request->only([
            'first_name',
            'last_name',
        ]));

        // Handle 2FA Toggle
        if ($request->has('enable_2fa')) {

            if (!$user->google2fa_status) {
                $google2fa = new Google2FA();
                $user->google2fa_secret = $google2fa->generateSecretKey();
                $user->google2fa_status = 1;
            }
        } else {
            // Disable 2FA
            $user->google2fa_status = 0;
            $user->google2fa_secret = null;
        }


        if ($request->filled('password')) {

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.'
                ]);
            }

            // Update password
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
