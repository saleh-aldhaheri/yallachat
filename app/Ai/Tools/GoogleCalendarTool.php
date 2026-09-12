<?php

namespace App\Ai\Tools;

use App\Models\Service;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GoogleCalendarTool implements Tool
{
    public function __construct(protected User $user, protected Service $service) {}

    public function description(): Stringable|string
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d (l)');
        $startOfWeek = $now->copy()->startOfWeek()->format('Y-m-d (l)');
        $endOfWeek = $now->copy()->endOfWeek()->format('Y-m-d (l)');
        $nextWeekStart = $now->copy()->addWeek()->startOfWeek()->format('Y-m-d (l)');
        $nextWeekEnd = $now->copy()->addWeek()->endOfWeek()->format('Y-m-d (l)');
        $nextMonthStart = $now->copy()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d');
        $nextMonthEnd = $now->copy()->addMonthNoOverflow()->endOfMonth()->format('Y-m-d');
        $oneWeekCutoff = $now->copy()->addWeek()->format('Y-m-d');

        return <<<DESC
                NAME: Google Calendar Lookup

                PURPOSE:
                Fetch {$this->user->name}'s calendar events for a given date/time range, to answer questions about their availability, schedule, or what they're doing at a given time.

                CURRENT CAPABILITIES:
                - Retrieves calendar events only for a specific start and end date/time.
                - Returns all events that fall within the requested time range.
                - Can be used to infer whether {$this->user->name} appears to be busy or free during that period based on the returned events.
                - Does not create, update, delete, or manage calendar events.
                - Does not search outside the requested time range.
                - Does not know the current date or time unless the reference dates below are used.

                WHEN TO USE THIS TOOL:
                - The sender is asking where {$this->user->name} is, what they're doing, whether they're busy/free, or when they'll be free — for today or any future date within the next 7 days.
                - Do NOT use this tool for questions unrelated to schedule/availability.

                TEMPORAL REFERENCE ANCHORS (ACCURACY MANDATORY — you do not know the current date on your own, always use these):
                - Today: {$today}
                - This week: {$startOfWeek} to {$endOfWeek}
                - Next week: {$nextWeekStart} to {$nextWeekEnd}
                - Next month: {$nextMonthStart} to {$nextMonthEnd}
                - 1-week lookahead cutoff: {$oneWeekCutoff}

                RULES FOR USING RESULTS:
                1. PAST DATES: If the sender asks about a date/time before now, do not call this tool for it — state you do not have information about past schedules.
                2. BEYOND 1 WEEK: If the sender asks about availability more than 7 days from today, do not call this tool for it — state you're not sure that far ahead, but you'll pass the message to {$this->user->name} to confirm later.
                3. IF BUSY: State what {$this->user->name} is doing using the event summary (e.g. "in a meeting", "at a doctor's appointment") — do not read out raw calendar data verbatim.
                4. IF FREE / NO EVENT FOUND: State that {$this->user->name} appears to be free/available at that time, but you will pass along the message so they can confirm.
                5. Never mention that you queried a calendar, ran a tool, or accessed an API — phrase everything as knowledge about {$this->user->name}'s day.
                DESC;
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $startTime = ! empty($request['start_time'])
                ? Carbon::parse($request['start_time'])
                : Carbon::now()->startOfMonth();

            $endTime = ! empty($request['end_time'])
                ? Carbon::parse($request['end_time'])
                : Carbon::now()->endOfMonth();

            $calendarService = GoogleCalendarService::makeGoogleCalendar($this->service);
            $events = $calendarService->getEvents($startTime, $endTime);

            if (is_string($events)) {
                return $events;
            }

            if ($events->isEmpty()) {
                return "No calendar events found between {$startTime->toDateString()} and {$endTime->toDateString()}.";
            }

            return json_encode([
                'queried_range' => [
                    'start' => $startTime->toIso8601String(),
                    'end' => $endTime->toIso8601String(),
                ],
                'events' => $events->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('GoogleCalendarTool Error: '.$e->getMessage());

            return 'Unable to fetch events: '.$e->getMessage();
        }
    }

    public function schema(JsonSchema $schema): array
    {
        $now = now();

        return [
            'start_time' => $schema->string()
                ->description("ISO 8601 start timestamp. Calculate relative to today ({$now->toDateString()}). Leave empty to query entire current month."),
            'end_time' => $schema->string()
                ->description("ISO 8601 end timestamp. Calculate relative to today ({$now->toDateString()}). Leave empty to query entire current month."),
        ];
    }
}
