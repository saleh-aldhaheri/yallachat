<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledMessagesJob;
use App\Models\ChatScheduledMessage;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-scheduled-messages-command')]
#[Description('Send scheduled messages that are due to be broadcast now.')]
class SendScheduledMessagesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        ChatScheduledMessage::where('is_paused', false)
            ->whereHas('scheduledMessage', function ($query) use ($now) {
                $query->where('is_active', true)
                    // expiration filter
                    ->where(function ($query) use ($now) {
                        $query->where('ends_type', 'never')
                            ->orWhere(function ($query) use ($now) {
                                $query->where('ends_type', 'on_date')
                                    ->where('ends_on', '>=', $now);
                            });
                    })
                    // scheduled_type filter
                    ->where(function ($query) use ($now) {
                        $query->where(function ($q) use ($now) {
                            $q->where('schedule_type', 'recurring')
                                ->whereDate('start_date', '<=', $now)
                                ->where(function ($q) use ($now) {
                                    $q->where('frequency', 'daily')
                                        ->orWhere(function ($q) use ($now) {
                                            $q->where('frequency', 'weekly')
                                                ->whereRaw(
                                                    'DAYOFWEEK(start_date) = ?',
                                                    [$now->dayOfWeek + 1]
                                                );
                                        })
                                        ->orWhere(function ($q) use ($now) {
                                            $q->where('frequency', 'monthly')
                                                ->whereRaw(
                                                    'LEAST(DAY(start_date), DAY(LAST_DAY(?))) = ?',
                                                    [$now->format('Y-m-d'), $now->day]
                                                );
                                        })
                                        ->orWhere(function ($q) use ($now) {
                                            $q->where('frequency', 'yearly')
                                                ->whereRaw(
                                                    'MONTH(start_date) = ?',
                                                    [$now->month]
                                                )
                                                ->whereRaw(
                                                    'DAY(start_date) = ?',
                                                    [$now->day]
                                                );
                                        });
                                });
                        })
                            ->orWhere(function ($q) use ($now) {
                                $q->where('schedule_type', 'specific_dates')
                                    ->whereJsonContains(
                                        'run_dates',
                                        $now->format('Y-m-d')
                                    );
                            });
                    })
                    // time filter
                    ->whereBetween('run_at', [
                        $now->copy()->second(0)->format('H:i:s'),
                        $now->copy()->second(59)->format('H:i:s'),
                    ]);
            })->chunkById(100, function ($messages) {
                foreach ($messages as $message) {
                    SendScheduledMessagesJob::dispatch($message->id);
                }
            });
    }
}
