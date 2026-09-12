<?php

namespace App\Services;

use App\Enums\ServicesEnum;
use App\Models\Service;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GoogleCalendarService
{
    private function __construct(private Service $service, private Calendar $calendar) {}

    private static function check(Service $service)
    {

        return $service->is_available && $service->is_active;
    }

    public static function makeGoogleCalendar(Service $service): self
    {
        if (! self::check($service)) {
            throw new \Exception('Google Calendar integration is not connected for this user.');
        }

        $client = new Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));

        $client->setAccessToken([
            'access_token' => $service->access_token,
            'refresh_token' => $service->refresh_token,
        ]);

        if ($client->isAccessTokenExpired() && $service->refresh_token) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($service->refresh_token);

            if (isset($newToken['access_token'])) {
                $service->update([
                    'access_token' => $newToken['access_token'],
                ]);
            }
        }

        return new self($service, new Calendar($client));
    }

    /**
     * Fetch events between two Carbon timestamps.
     *
     * @throws Exception
     */
    public function getEvents(Carbon $startDateTime, Carbon $endDateTime): string|Collection
    {
        try {
            $key = sprintf(
                'user_%s_%s_%s_%s',
                $this->service->user_id,
                ServicesEnum::GOOGLE_CALENDAR->value,
                $startDateTime->timestamp,
                $startDateTime->diffInSeconds($endDateTime)
            );

            return collect(Cache::remember($key, now()->addHour(), function () use ($startDateTime, $endDateTime) {
                $optParams = [
                    'timeMin' => $startDateTime->toRfc3339String(),
                    'timeMax' => $endDateTime->toRfc3339String(),
                    'singleEvents' => true,
                    'orderBy' => 'startTime',
                ];

                $results = $this->calendar->events->listEvents('primary', $optParams);
                $events = [];

                foreach ($results->getItems() as $event) {
                    $events[] = [
                        'id' => $event->getId(),
                        'summary' => $event->getSummary(),
                        'description' => $event->getDescription(),
                        'start' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                        'end' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                    ];
                }

                return $events;
            }));

        } catch (\Throwable $e) {

            if ($e->getCode() === 401 || $e->getCode() === 403) {
                $this->service->update(['is_available' => false]);
                throw new \Exception('Google Calendar integration is not connected for this user.', 401, $e);
            }

            if ($e->getCode() === 500) {
                return 'Google Calendar Side Error: Please try again later.';
            }

            throw new \Exception('Failed to retrieve calendar events: '.$e->getMessage(), 500, $e);
        }
    }
}
