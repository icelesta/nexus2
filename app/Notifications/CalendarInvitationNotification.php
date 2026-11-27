<?php

namespace App\Notifications;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CalendarInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public CalendarEvent $event,
        public string $actorName,
    ) {
    }

    /**
     * Notification channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Database notification payload.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'CALENDAR_INVITATION',

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

            'actor_name' => $this->actorName,

            'remarks' => $this->event->description,

            'title' => 'Calendar Invitation',

            'body' => sprintf(
                '%s invited you to %s on %s at %s.',
                $this->actorName,
                $this->event->title,
                $this->event->start_at->format('d M Y'),
                $this->event->start_at->format('H:i')
            ),

            'url' => route('filament.admin.pages.calendar'),

            'icon' => 'heroicon-o-calendar-days',

            'icon_color' => 'blue',
        ];
    }
}