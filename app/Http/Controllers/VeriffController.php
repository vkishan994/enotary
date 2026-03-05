<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\VeriffData;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VeriffService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VeriffController extends Controller
{
    public function callback(Request $request, $order_id)
    {
        $exists = VeriffData::where('order_id', decrypt($order_id))
            ->where('user_id', Auth::id())
            ->where('status', 'created')
            ->latest()
            ->first();
        if ($exists) {
            $exists->status = 'user_cancelled';
            $exists->save();
        }
        return redirect()->route('user.verification.page', [
            'order_id' => $order_id
        ]);
    }

    public function verificationPage(Request $request, $order_id)
    {
        $user = auth()->user();
        $VeriffData = VeriffData::where('order_id', decrypt($order_id))->first();
        return view('front.user.verification_page', compact('user', 'order_id', 'VeriffData'));
    }

    public function startVerification(Request $request, $order_id)
    {
        $user = auth()->user();
        $orderid = decrypt($order_id);

        $veriffService = new VeriffService();

        try {
            //  Create verification session
            $endUserId = (string) Str::uuid();

            $callbackBaseUrl = !empty(env('NGROKURL'))
                ? rtrim(env('NGROKURL'), '/')
                : rtrim(config('app.url'), '/');

            $callbackUrl = $callbackBaseUrl . '/veriff/callback/' . $order_id;

            $response = $veriffService->createSession([
                'callback'   =>  $callbackUrl,
                'vendorData' => (string) $orderid,         // original order ID
                'endUserId'  => $endUserId,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
            ]);

            // dd($response);

            // Validate response
            if (
                empty($response['verification']['id']) ||
                empty($response['verification']['url'])
            ) {
                throw new Exception('Invalid Veriff response');
            }

            DB::transaction(function () use ($response, $user, $orderid) {

                $existing = VeriffData::where('order_id', $orderid)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existing) {

                    // Update existing record
                    $existing->update([
                        'session_id'  => $response['verification']['id'],
                        'vendor_data' => $response['verification']['vendorData'] ?? null,
                        'status'      => $response['verification']['status'] ?? 'created',
                        'payload'     => $response,
                    ]);
                } else {

                    // Create new record
                    VeriffData::create([
                        'user_id'     => $user->id,
                        'order_id'    => $orderid,
                        'session_id'  => $response['verification']['id'],
                        'vendor_data' => $response['verification']['vendorData'] ?? null,
                        'status'      => $response['verification']['status'] ?? 'created',
                        'payload'     => $response,
                    ]);
                }
            });

            // Redirect user to Veriff hosted page
            return redirect()->away(
                $response['verification']['url']
            );
        } catch (Exception $e) {
            // dd($e->getMessage());
            report($e);

            return redirect()->back()->withErrors([
                'verification' => 'Unable to start identity verification. Please try again.',
            ]);
        }
    }
}
