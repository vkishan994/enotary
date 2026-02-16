<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\VeriffData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VeriffWebhookController extends Controller
{
    public function handle(Request $request)
    {

        $rawPayload = $request->getContent();
        $receivedSignature = strtolower($request->header('x-hmac-signature'));
        $sharedSecret = getValuesByKey('veriff_secret_key');

        // Generate signature (EXACTLY like Veriff docs)
        $calculatedSignature = strtolower(
            hash_hmac('sha256', $rawPayload, $sharedSecret)
        );

        // Log everything (even invalid attempts)
        Log::channel('veriff')->info('Webhook incoming', [
            'raw_payload' => $rawPayload,
        ]);

        // HMAC verification
        if (!hash_equals($calculatedSignature, $receivedSignature)) {
            Log::channel('veriff')->warning('Invalid HMAC signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Decode payload AFTER verification
        $payload = json_decode($rawPayload, true);

        if (!isset($payload['id'])) {
            Log::channel('veriff')->warning('Missing session id', $payload);
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $sessionId = $payload['id'];

        /**
         * EVENT WEBHOOK (started / submitted / completed)
         */
        if (isset($payload['action'])) {

            VeriffData::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'end_user_id' => $payload['endUserId'] ?? null,
                    'vendor_data' => $payload['vendorData'] ?? null,
                    'status' => $payload['action'],
                    'payload' => $payload,
                ]
            );
        }

        /**
         * DECISION WEBHOOK (approved / declined)
         */
        // if (isset($payload['decision'])) {
        //     VeriffData::where('session_id', $sessionId)->update([
        //         'veriff_decision' => $payload['decision'],
        //         'veriff_reason' => $payload['reason'] ?? null,
        //         'veriff_verified_at' => isset($payload['verification']['decisionTime'])
        //             ? Carbon::parse($payload['verification']['decisionTime'])
        //             : now(),
        //         'status' => 'finished',
        //         'payload' => $payload,
        //     ]);
        // }


        if (isset($payload['decision'])) {

            DB::transaction(function () use ($payload, $sessionId) {

                $veriff = VeriffData::where('session_id', $sessionId)->first();

                if (!$veriff) {
                    return;
                }

                // Determine decision status
                $decision = strtolower($payload['decision']);

                // Update Veriff table
                $veriff->update([
                    'veriff_decision'    => $payload['decision'],
                    'veriff_reason'      => $payload['reason'] ?? null,
                    'veriff_verified_at' => isset($payload['verification']['decisionTime'])
                        ? Carbon::parse($payload['verification']['decisionTime'])
                        : now(),
                    'status'             => 'finished',
                    'payload'            => $payload,
                ]);


                Order::where('id', $veriff->order_id)
                    ->update([
                        'kyc_status' => $payload['decision'], // change if needed
                    ]);
            });
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
