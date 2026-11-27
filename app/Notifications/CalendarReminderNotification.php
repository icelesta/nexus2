<?php

namespace App\Notifications;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CalendarReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public CalendarEvent $event,
        public int $minutesBefore,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'CALENDAR_REMINDER',

            'document_type' => 'Calendar Event',

            'document_id' => $this->event->id,

            'document_no' => 'CAL-' . str_pad(
                (string) $this->event->id,
                6,
                '0',
                STR_PAD_LEFT
            ),

            'approval_level' => null,

            'role_name' => null,

            'actor_name' => $this->event->creator?->name,

            'remarks' => $this->event->description,

            'title' => 'Calendar Reminder',

            'body' => sprintf(
                '%s starts in %d minutes at %s.',
                $this->event->title,
                $this->minutesBefore,
                $this->event->start_at->format('H:i')
            ),

            'url' => route('filament.admin.pages.calendar'),

            'icon' => 'heroicon-o-bell-alert',

            'icon_color' => 'warning',
        ];
    }
}