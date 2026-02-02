<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\VeriffData;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VeriffService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VeriffController extends Controller
{
    public function callback(Request $request)
    {
        $data = $request->all();
        dd($data);

        // Log callback for debugging
        Log::info('Veriff callback received', $data);

        try {

            if (empty($data)) {
                return redirect()->route('user.account-dashboard')
                    ->with('error', 'Verification was canceled or not completed.');
            }
            // Check if verification data exists
            $sessionId = $data['session']['id'] ?? null;

            // If no session ID is sent, it might be a canceled or incomplete callback
            if (!$sessionId) {
                Log::warning('Veriff callback received with no session ID, user may have canceled.');
                return redirect()->route('account-dashboard')
                    ->with('error', 'Verification was canceled or not completed.');
            }

            // Find the verification record
            $veriffData = VeriffData::where('session_id', $sessionId)->first();

            if (!$veriffData) {
                Log::error("No VeriffData record found for session: $sessionId");
                return redirect()->route('dashboard')
                    ->with('error', 'Unable to process verification.');
            }

            // Extract status and decision
            $veriffStatus = $data['verification']['status'] ?? 'canceled'; // default to canceled if missing
            $veriffDecision = $data['verification']['decision'] ?? null;

            // Update the veriff_data record
            $veriffData->update([
                'status'             => $veriffStatus,
                'veriff_decision'    => $veriffDecision,
                'veriff_reason'      => $data['verification']['reason'] ?? null,
                'veriff_verified_at' => $veriffStatus === 'completed' ? now() : null,
                'payload'            => $data,
            ]);

            // Optional: update user reference
            $veriffData->user->update([
                'veriff_status' => $veriffStatus,
            ]);

            // Redirect user based on status
            if ($veriffStatus === 'completed' && $veriffDecision === 'approved') {
                return redirect()->route('dashboard')->with('success', 'Identity verification completed successfully!');
            }

            if ($veriffStatus === 'canceled' || $veriffStatus === 'expired') {
                return redirect()->route('dashboard')->with('error', 'Verification was canceled or not completed.');
            }

            return redirect()->route('dashboard')->with('info', 'Verification status: ' . $veriffStatus);
        } catch (\Exception $e) {
            Log::error('Veriff callback error: ' . $e->getMessage(), $request->all());
            return redirect()->route('dashboard')
                ->with('error', 'Unable to process verification callback. Please try again.');
        }
    }

    public function verificationPage(Request $request, $order_id)
    {
        $user = auth()->user();
        return view('front.user.verification_page', compact('user', 'order_id'));
    }

    public function startVerification(Request $request, $order_id)
    {

        $user = auth()->user();
        $orderid = decrypt($order_id);

        $veriffService = new VeriffService();

        try {
            //  Create verification session
            $endUserId = (string) Str::uuid();

            $response = $veriffService->createSession([
                'callback'   => env('NGROKURL'),
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

            DB::transaction(function () use ($response, $user) {

                //  Store in veriff_data table
                VeriffData::create([
                    'user_id'             => $user->id,
                    'session_id'   => $response['verification']['id'],
                    'end_user_id'  => $response['verification']['endUserId'] ?? null,
                    'vendor_data'  => $response['verification']['vendorData'] ?? null,
                    'status'       => $response['verification']['status'] ?? null,
                    'veriff_decision'     => null,
                    'payload'      => $response, // store full API response
                ]);

                // (optional) keep reference on users table
                // $user->update([
                //     'veriff_status' => 'started',
                // ]);
            });

            // 3️⃣ Redirect user to Veriff hosted page
            return redirect()->away(
                $response['verification']['url']
            );
        } catch (Exception $e) {
            dd($e->getMessage());
            report($e);

            return redirect()->back()->withErrors([
                'verification' => 'Unable to start identity verification. Please try again.',
            ]);
        }
    }
}
