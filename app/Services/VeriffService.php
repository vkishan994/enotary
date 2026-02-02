<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class VeriffService
{

    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = getValuesByKey('veriff_base_url');
        $this->apiKey = getValuesByKey('veriff_api_key');
    }

    /**
     * Create Veriff verification session
     */
    public function createSession(array $data): array
    {
        try {
            $payload = [
                'verification' => [
                    'callback'   => $data['callback'],
                    'vendorData' => (string) $data['vendorData'],
                    'endUserId'  => (string) $data['endUserId'],
                    'person' => array_filter([
                        'firstName' => $data['first_name'],
                        'lastName'  => $data['last_name'],
                        'email'     => $data['email'] ?? null,
                    ]),
                ],
            ];

            $response = Http::withHeaders([
                'x-auth-client' => $this->apiKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
                ->withoutVerifying() //disable SSL verification for local
                ->post($this->apiUrl . '/v1/sessions', $payload);

            if (!$response->successful()) {
                Log::error('Veriff API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                throw new Exception('Veriff session creation failed');
            }

            return $response->json();
        } catch (Throwable $e) {
            dd($e->getMessage());
            Log::critical('Veriff exception', [
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Unable to start verification');
        }
    }
}
