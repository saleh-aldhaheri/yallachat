<?php

namespace App\Livewire\Component;

use App\Models\ScheduledMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class HandleScheduledMessage extends Component
{
    public const int MAX_LANGUAGES = 3;

    public const array FREQUENCIES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
    ];

    public const array WEEKDAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public ?ScheduledMessage $scheduledMessage = null;

    public string $scheduledType = 'recurring';

    public string $topic = '';

    public string $description = '';

    public bool $isActive = true;

    public array $languages = ['English'];

    public string $startDate = '';

    public string $frequency = 'daily';

    public string $runAt = '';

    public string $endsType = 'never';

    public $endsOn = null;

    public array $runDates = [];

    public array $selectedChats = [];

    public bool $isUserHaveActiveModel = false;

    public bool $isUserHaveChats = false;

    public bool $isMultiLanguageEnabled = false;

    public function mount(): void
    {
        $model = auth()->user()->aiModels()->first();

        $this->isUserHaveActiveModel = ! is_null($model);
        $this->isUserHaveChats = auth()->user()->joinedChats()->exists();
        $this->isMultiLanguageEnabled = $model?->is_multi_language ?? false;

        if ($this->scheduledMessage) {
            $this->scheduledType = $this->scheduledMessage->schedule_type;
            $this->topic = $this->scheduledMessage->topic;
            $this->description = $this->scheduledMessage->description;
            $this->isActive = $this->scheduledMessage->is_active;
            $this->languages = $this->scheduledMessage->languages ?? ['English'];
            $this->startDate = $this->scheduledMessage->start_date?->format('Y-m-d') ?? '';
            $this->frequency = $this->scheduledMessage->frequency ?? 'daily';
            $this->runAt = substr((string) $this->scheduledMessage->run_at, 0, 5);
            $this->endsType = $this->scheduledMessage->ends_type ?? 'never';
            $this->endsOn = $this->scheduledMessage->ends_on?->format('Y-m-d') ?? null;
            $this->runDates = $this->scheduledMessage->run_dates ?? [];
            $this->selectedChats = $this->scheduledMessage->chats()->pluck('chats.id')->toArray();
        }
    }

    public function render()
    {
        return view('livewire.components.handle-scheduled-message', [
            'availableLanguages' => $this->languagesList(),
            'maxLanguages' => self::MAX_LANGUAGES,
            'frequencies' => self::FREQUENCIES,
            'chats' => $this->userChats(),
        ]);
    }

    public function addRunDate(): void
    {
        $this->runDates[] = now()->format('Y-m-d');
    }

    public function removeRunDate(int $index): void
    {
        unset($this->runDates[$index]);
        $this->runDates = array_values($this->runDates);
    }

    public function startMonthlyLabel(): string
    {
        if (! $this->startDate) {
            return '—';
        }

        $date = Carbon::parse($this->startDate);

        return $this->nthLabel(intdiv($date->day - 1, 7) + 1).' '.$this->weekdayName($date->dayOfWeek);
    }

    public function weekdayName(int $weekday): string
    {
        return self::WEEKDAYS[$weekday] ?? '';
    }

    public function nthLabel(int $nth): string
    {
        return match ($nth) {
            -1 => 'last',
            1 => 'first',
            2 => 'second',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
            default => '',
        };
    }

    public function startWeekdayLabel(): string
    {
        return $this->startDate ? Carbon::parse($this->startDate)->format('l') : '—';
    }

    public function startYearlyLabel(): string
    {
        return $this->startDate ? Carbon::parse($this->startDate)->format('F jS') : '—';
    }

    public function updatedEndsType(): void
    {
        if ($this->endsType !== 'on_date') {
            $this->endsOn = null;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'scheduledType' => 'required|in:recurring,specific_dates',
            'topic' => 'required|string|max:255',
            'description' => 'required|string',
            'runAt' => 'required|date_format:H:i',
            'languages' => 'array|min:1|max:3',
            'selectedChats' => 'required|array|min:1',
        ]);

        $aiModel = auth()->user()->aiModels()->useable()->first();

        if(!$aiModel)
        {
            Toaster::error("No enabled and connected AI model was found. Please check your model configuration.");
            return;
        }

        if ($this->scheduledType === 'recurring') {
            $this->validate([
                'startDate' => 'required|date|after_or_equal:today',
                'frequency' => 'required|in:daily,weekly,monthly,yearly',
                'endsType' => 'required|in:never,on_date',
            ]);

            if ($this->endsType === 'on_date') {
                $this->validate([
                    'endsOn' => 'required|date|after:startDate',
                ]);
            }
        }

        if ($this->scheduledType === 'specific_dates') {
            $this->validate([
                'runDates' => 'required|array|min:1',
                'runDates.*' => ['date', 'after_or_equal:today'],
            ]);

            $minAllowed = Carbon::now()->addMinutes(5);

            foreach ($this->runDates as $date) {
                $scheduledAt = Carbon::parse($date.' '.$this->runAt);

                if ($scheduledAt->lt($minAllowed)) {
                    $this->addError('runDates', 'Each run date must be at least 5 minutes from now.');

                    return;
                }
            }
        }

        $data = [
            'topic' => $this->topic,
            'user_id' => auth()->id(),
            'description' => $this->description,
            'is_active' => $this->isActive,
            'languages' => $this->isMultiLanguageEnabled ? $this->languages : null,
            'schedule_type' => $this->scheduledType,
            'start_date' => $this->scheduledType === 'recurring' ? $this->startDate : null,
            'frequency' => $this->scheduledType === 'recurring' ? $this->frequency : null,
            'run_at' => $this->runAt,
            'run_dates' => $this->scheduledType === 'specific_dates' ? $this->runDates : null,
            'ends_type' => $this->scheduledType === 'recurring' ? $this->endsType : 'never',
            'ends_on' => $this->scheduledType === 'recurring' && $this->endsType === 'on_date' ? $this->endsOn : null,
        ];

        try {
            $isUpdate = (bool) $this->scheduledMessage;

            DB::transaction(function () use ($data) {

                $scheduledMessage = $this->scheduledMessage
                    ? tap($this->scheduledMessage)->update($data)
                    : ScheduledMessage::create($data);
                $scheduledMessage->chats()->sync($this->selectedChats);

                $this->reset(['topic', 'description', 'languages', 'runDates', 'selectedChats']);
                $this->dispatch('set-page', 'list_scheduled_messages');
            });

            Toaster::success($isUpdate ? 'Scheduled message updated.' : 'Scheduled message created.');
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            Toaster::error('Something went wrong while saving the scheduled message.');
        }
    }

    private function languagesList(): array
    {
        return json_decode(
            file_get_contents(resource_path('data/languages.json')),
            true
        ) ?? [];
    }

    private function userChats(): Collection
    {
        return auth()->user()->joinedChats()->get()->map(function ($chat) {
            $name = $chat->name;

            if ($chat->type === 'private') {
                $name = $chat->participants()
                    ->whereNot('participant_id', auth()->id())
                    ->first()?->name ?? $chat->name;
            }

            return [
                'id' => $chat->id,
                'name' => $name,
            ];
        })->values();
    }
}
