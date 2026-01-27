<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Service\Calendar\EventDateTime;


class GoogleCalender
{

    public static function getGoogleAccessToken(string $refreshToken): string
    {
        $http = Http::asForm();

        // Disable SSL verification only in local environment
        if (App::environment('local')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->post('https://oauth2.googleapis.com/token', [
            'client_id'     => getValuesByKey('google_client_id'),
            'client_secret' => getValuesByKey('google_client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ]);

        if (! $response->successful()) {
            throw new \Exception(
                'Failed to refresh Google access token: ' . $response->body()
            );
        }

        return $response->json()['access_token'];
    }

    /**
     * Create Google Calendar Event using Refresh Token
     */
    public static function createEvent(
        string $summary,
        string $description,
        string $startDateTime,
        ?string $endDateTime = null,
        array $attendees = [],
        string $calendarId = 'primary'
    ): ?Event {
        try {
            $client = new Client();
            $client->setApplicationName(config('app.name'));
            $client->setScopes([Calendar::CALENDAR]);
            $client->setAccessType('offline');

            // 🔐 Load OAuth credentials JSON from DB
            $googleCredentials = getValuesByKey('google_client_secret');
            if (!$googleCredentials) {
                throw new \Exception('Google credentials not found');
            }

            $client->setAuthConfig(json_decode($googleCredentials, true));

            // 🔑 Load refresh token
            $refreshToken = getValuesByKey('google_refresh_token');
            if (!$refreshToken) {
                throw new \Exception('Google refresh token missing');
            }

            // ✅ THIS is the correct way
            $client->refreshToken($refreshToken);

            $service = new Calendar($client);

            // Default duration: 30 minutes
            if (!$endDateTime) {
                $endDateTime = Carbon::parse($startDateTime)
                    ->addMinutes(30)
                    ->toIso8601String();
            }

            $event = new Event([
                'summary'     => $summary,
                'description' => $description,
                'start'       => new EventDateTime([
                    'dateTime' => $startDateTime,
                    'timeZone' => config('app.timezone', 'UTC'),
                ]),
                'end'         => new EventDateTime([
                    'dateTime' => $endDateTime,
                    'timeZone' => config('app.timezone', 'UTC'),
                ]),
                'attendees'   => array_map(fn($email) => ['email' => $email], $attendees),

                // 🎥 Google Meet link
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => uniqid(),
                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet',
                        ],
                    ],
                ],
            ]);

            return $service->events->insert(
                $calendarId,
                $event,
                ['conferenceDataVersion' => 1]
            );
        } catch (\Exception $e) {
            // dd($e->getMessage());
            Log::error('Google Calendar Error', [
                'message' => $e->getMessage(),
                'summary' => $summary,
                'start'   => $startDateTime,
            ]);

            return null;
        }
    }
}
