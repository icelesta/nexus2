<?php

namespace App\Console\Commands;

use App\Models\CalendarEventReminder;
use App\Notifications\CalendarReminderNotification;
use Illuminate\Console\Command;

class ProcessCalendarReminders extends Command
{
    protected $signature = 'calendar:process-reminders';

    protected $description = 'Process pending calendar event reminders';

    public function handle(): int
    {
        $now = now();

        $reminders = CalendarEventReminder::query()
            ->with([
                'event.creator',
                'user',
            ])
            ->whereNull('sent_at')
            ->whereHas('event', function ($query) {
                $query
                    ->where('status', 'SCHEDULED')
                    ->where('start_at', '>', now());
            })
            ->get();

        $processed = 0;

        foreach ($reminders as $reminder) {

            $event = $reminder->event;

            $reminderAt = $event->start_at
                ->copy()
                ->subMinutes($reminder->minutes_before);

            if ($now->lt($reminderAt)) {
                continue;
            }

            if ($now->gte($event->start_at)) {
                continue;
            }

            $reminder->user->notify(
                new CalendarReminderNotification(
                    $event,
                    $reminder->minutes_before
                )
            );

            $reminder->update([
                'sent_at' => now(),
            ]);

            $processed++;

            $this->info(
                sprintf(
                    'Reminder sent: %s → %s',
                    $event->title,
                    $reminder->user->name
                )
            );
        }

        $this->info(
            "Processed {$processed} calendar reminder(s)."
        );

        return self::SUCCESS;
    }
}