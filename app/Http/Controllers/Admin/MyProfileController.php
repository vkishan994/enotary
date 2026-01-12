<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\NotaryServiceType;
use App\Models\Document;
use App\Models\Order;
use App\Services\StripeClass;

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
        $user = Auth::user();

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
            if ($order) {
                $order->update(['payment_status' => 'completed']);
            }
        }

        return view('front.payment-success');
    }
}
