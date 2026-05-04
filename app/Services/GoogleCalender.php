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
    public static function createMeeting(
        string $refreshToken,
        array $eventData,
        string $calendarId = 'primary'
    ): array {
        // Get fresh access token
        $accessToken = self::getGoogleAccessToken($refreshToken);

        $http = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);

        if (App::environment('local')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->post(
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events?conferenceDataVersion=1",
            [
                'summary'     => $eventData['title'],
                'description' => $eventData['description'] ?? null,
                'start'       => [
                    'dateTime' => $eventData['start'], // ISO 8601
                    'timeZone' => config('app.timezone') ?? 'UTC',
                ],
                'end'         => [
                    'dateTime' => $eventData['end'], // ISO 8601
                    'timeZone' => config('app.timezone') ?? 'UTC',
                ],
                'attendees'   => collect($eventData['attendees'] ?? [])
                    ->map(fn($email) => ['email' => $email])
                    ->values()
                    ->toArray(),

                // Google Meet link
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => uniqid(),
                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet',
                        ],
                    ],
                ],
            ]
        );

        if (! $response->successful()) {
            throw new \Exception(
                'Failed to create Google Calendar event: ' . $response->body()
            );
        }

        return $response->json();
    }

    public static function deleteMeeting(
        string $refreshToken,
        string $eventId,
        string $calendarId = 'primary'
    ): bool {
        $accessToken = self::getGoogleAccessToken($refreshToken);
        $calendarId = urlencode($calendarId);
        $eventId = rawurlencode($eventId);

        $http = Http::withToken($accessToken);
        if (App::environment('local')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->delete(
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}"
        );

        if (! $response->successful()) {
            logger()->warning('Failed to delete Google Calendar event', [
                'status' => $response->status(),
                'body' => $response->body(),
                'event_id' => $eventId,
                'calendar_id' => $calendarId,
            ]);

            return false;
        }

        return true;
    }

    public static function getCalendarEvents(
        string $refreshToken,
        string $calendarId = 'primary',
        ?string $timeMin = null,
        ?string $timeMax = null
    ): array {
        $accessToken = self::getGoogleAccessToken($refreshToken);

        //  IMPORTANT: URL encode calendar ID
        $calendarId = urlencode($calendarId);

        $http = Http::withToken($accessToken);

        if (App::environment('local')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->get(
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events",
            [
                'singleEvents' => 'true',
                'orderBy'      => 'startTime',

                // RFC3339 with Z timezone
                'timeMin' => $timeMin
                    ? Carbon::parse($timeMin)->utc()->toRfc3339String()
                    : now()->subMonth()->utc()->toRfc3339String(),

                'timeMax' => $timeMax
                    ? Carbon::parse($timeMax)->utc()->toRfc3339String()
                    : now()->addMonths(3)->utc()->toRfc3339String(),

                'maxResults' => 2500,
            ]
        );

        // DEBUG (temporarily)
        if (! $response->successful()) {
            logger()->error('Google Calendar API Error', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return [];
        }

        return collect($response->json('items', []))
            ->map(function ($event) {
                return [
                    'id'            => $event['id'],
                    'title'         => $event['summary'] ?? 'Meeting',
                    'start'         => $event['start']['dateTime'] ?? $event['start']['date'],
                    'end'           => $event['end']['dateTime'] ?? $event['end']['date'],
                    'description'   => $event['description'] ?? '',
                    'attendees'     => $event['attendees'] ?? [],
                    'attendeesCount' => count($event['attendees'] ?? []),
                    'attendeesWaiting' => collect($event['attendees'] ?? [])
                        ->filter(fn($a) => ($a['responseStatus'] ?? '') === 'needsAction')
                        ->count(),
                    'organizer'     => $event['organizer']['email'] ?? '',
                    'organizerDisplayName' => $event['organizer']['displayName'] ?? '',
                    'conference'    => $event['conferenceData']['entryPoints'][0]['uri'] ?? null,
                    'htmlLink'      => $event['htmlLink'] ?? null,
                ];
            })
            ->values()
            ->toArray();
    }
}
