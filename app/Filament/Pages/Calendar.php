<?php

declare(strict_types=1);


namespace App\Filament\Pages;

use App\Models\User;
use App\Models\CalendarEvent;
use App\Models\CalendarTask;
use App\Models\CalendarEventParticipant;
use App\Models\CalendarEventReminder;
use App\Notifications\CalendarInvitationNotification;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class Calendar extends Page
{
    protected static ?string $navigationLabel = 'Calendar';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Calendar';

    protected static ?string $slug = 'calendar';

    protected string $view = 'filament.pages.calendar';

    public string $viewMode = 'week';

    public string $calendarScope = 'my';

    public string $currentDate;

    public bool $showEventModal = false;

    public bool $showTaskModal = false;

    public string $eventTitle = '';

    public string $eventType = 'MEETING';

    public string $eventVisibility = 'PERSONAL';

    public string $eventDate = '';

    public string $eventStartTime = '09:00';

    public string $eventEndTime = '10:00';

    public string $eventLocation = '';

    public string $eventDescription = '';

    public array $participantUserIds = [];

    public string $participantSearch = '';

    public int $reminderMinutes = 15;

    public string $taskTitle = '';

    public string $taskDueAt = '';

    public string $taskPriority = 'MEDIUM';

    public function mount(): void
    {
        $this->currentDate = now()->format('Y-m-d');

        $this->eventDate = now()->format('Y-m-d');

        $this->taskDueAt = now()
            ->addDay()
            ->setTime(17, 0)
            ->format('Y-m-d\TH:i');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public function previousPeriod(): void
    {
        $date = Carbon::parse($this->currentDate);

        $this->currentDate = match ($this->viewMode) {
            'day' => $date->subDay()->format('Y-m-d'),
            'month' => $date->subMonth()->format('Y-m-d'),
            default => $date->subWeek()->format('Y-m-d'),
        };
    }

    public function nextPeriod(): void
    {
        $date = Carbon::parse($this->currentDate);

        $this->currentDate = match ($this->viewMode) {
            'day' => $date->addDay()->format('Y-m-d'),
            'month' => $date->addMonth()->format('Y-m-d'),
            default => $date->addWeek()->format('Y-m-d'),
        };
    }

    public function goToday(): void
    {
        $this->currentDate = now()->format('Y-m-d');
    }

    public function setViewMode(string $mode): void
    {
        if (! in_array($mode, [
            'day',
            'week',
            'month',
            'list',
        ], true)) {
            return;
        }

        $this->viewMode = $mode;
    }

    public function setScope(string $scope): void
    {
        if (! in_array($scope, [
            'my',
            'shared',
            'company',
        ], true)) {
            return;
        }

        $this->calendarScope = $scope;
    }

    public function getWeekDays(): array
    {
        $date = Carbon::parse($this->currentDate);

        $start = $date
            ->copy()
            ->startOfWeek();

        return collect(range(0, 6))
            ->map(
                fn (int $offset) =>
                    $start->copy()->addDays($offset)
            )
            ->all();
    }

    public function getEvents(): Collection
    {
        $userId = auth()->id();

        $query = CalendarEvent::query()
            ->with([
                'creator',
                'participants.user',
            ])
            ->where('status', '!=', 'CANCELLED');

        if ($this->calendarScope === 'my') {
            $query->where(function ($query) use ($userId): void {
                $query
                    ->where(function ($query) use ($userId): void {
                        $query
                            ->where('created_by', $userId)
                            ->where('visibility', 'PERSONAL');
                    })
                    ->orWhere(function ($query) use ($userId): void {
                        $query
                            ->where('visibility', 'USERS')
                            ->whereHas(
                                'participants',
                                fn ($query) =>
                                    $query->where('user_id', $userId)
                            );
                    });
            });
        }

        if ($this->calendarScope === 'shared') {
            $query->whereHas(
                'participants',
                fn ($query) =>
                    $query->where('user_id', $userId)
            );
        }

        if ($this->calendarScope === 'company') {
            $query->where('visibility', 'COMPANY');
        }

        if ($this->viewMode === 'day') {
            $start = Carbon::parse($this->currentDate)
                ->startOfDay();

            $end = Carbon::parse($this->currentDate)
                ->endOfDay();
        } else {
            $start = Carbon::parse($this->currentDate)
                ->startOfWeek();

            $end = Carbon::parse($this->currentDate)
                ->endOfWeek();
        }

        return $query
            ->where(function ($query) use ($start, $end): void {
                $query
                    ->whereBetween('start_at', [
                        $start,
                        $end,
                    ])
                    ->orWhere(function ($query) use ($start, $end): void {
                        $query
                            ->where('start_at', '<', $start)
                            ->where('end_at', '>=', $start);
                    });
            })
            ->orderBy('start_at')
            ->get();
    }

    public function getTasks(): Collection
    {
        return CalendarTask::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', [
                'OPEN',
                'IN_PROGRESS',
            ])
            ->orderByRaw(
                "FIELD(priority, 'URGENT', 'HIGH', 'MEDIUM', 'LOW')"
            )
            ->orderBy('due_at')
            ->limit(8)
            ->get();
    }

    public function getReminders(): Collection
    {
        return CalendarEvent::query()
            ->where(function ($query): void {
                $query
                    ->where('created_by', auth()->id())
                    ->orWhereHas(
                        'participants',
                        fn ($query) =>
                            $query->where(
                                'user_id',
                                auth()->id()
                            )
                    );
            })
            ->where('status', 'SCHEDULED')
            ->whereBetween('start_at', [
                now(),
                now()->addDays(7),
            ])
            ->orderBy('start_at')
            ->limit(5)
            ->get();
    }

    public function respondToInvitation(
        int $eventId,
        string $response
    ): void {
        $response = strtoupper(trim($response));

        if (! in_array($response, [
            'ACCEPTED',
            'DECLINED',
            'TENTATIVE',
        ], true)) {
            $this->addError(
                'invitationResponse',
                'Invalid invitation response.'
            );

            return;
        }

        $userId = (int) auth()->id();

        $participant = \App\Models\CalendarEventParticipant::query()
            ->where('calendar_event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if (! $participant) {
            $this->addError(
                'invitationResponse',
                'You are not a participant of this calendar event.'
            );

            return;
        }

        $event = \App\Models\CalendarEvent::query()
            ->find($eventId);

        if (! $event) {
            $this->addError(
                'invitationResponse',
                'Calendar event not found.'
            );

            return;
        }

        if ($event->status === 'CANCELLED') {
            $this->addError(
                'invitationResponse',
                'This calendar event has been cancelled.'
            );

            return;
        }

        \Illuminate\Support\Facades\DB::transaction(
            function () use ($participant, $response, $userId, $eventId): void {
                $participant->update([
                    'response_status' => $response,
                    'responded_at' => now(),
                ]);

                \App\Models\Notification::query()
                    ->where('notifiable_type', \App\Models\User::class)
                    ->where('notifiable_id', $userId)
                    ->where('data->event', 'CALENDAR_INVITATION')
                    ->where('data->document_id', $eventId)
                    ->whereNull('read_at')
                    ->get()
                    ->each(
                        fn ($notification) =>
                            $notification->markAsRead()
                    );
            }
        );

        $this->dispatch('calendar-invitation-responded');
    }

    public function createEvent(): void
    {
        $this->validate([
            'eventTitle' => ['required', 'string', 'max:255'],
            'eventType' => ['required', 'in:MEETING,REMINDER,EVENT,DEADLINE'],
            'eventVisibility' => ['required', 'in:PERSONAL,USERS,COMPANY'],
            'eventDate' => ['required', 'date'],
            'eventStartTime' => ['required'],
            'eventEndTime' => ['required'],
            'eventLocation' => ['nullable', 'string', 'max:255'],
            'eventDescription' => ['nullable', 'string'],
            'participantUserIds' => ['array'],
            'participantUserIds.*' => ['integer', 'exists:users,id'],
            'reminderMinutes' => ['required', 'integer', 'in:5,15,30,60'],
        ]);

        $startAt = \Carbon\Carbon::parse(
            $this->eventDate . ' ' . $this->eventStartTime
        );

        $endAt = \Carbon\Carbon::parse(
            $this->eventDate . ' ' . $this->eventEndTime
        );

        if ($endAt->lessThanOrEqualTo($startAt)) {
            $this->addError(
                'eventEndTime',
                'End time must be later than start time.'
            );

            return;
        }

        if (
            $this->eventVisibility === 'USERS'
            && empty($this->participantUserIds)
        ) {
            $this->addError(
                'participantUserIds',
                'Please select at least one participant.'
            );

            return;
        }

        $event = CalendarEvent::create([
            'created_by' => auth()->id(),
            'title' => $this->eventTitle,
            'description' => $this->eventDescription ?: null,
            'event_type' => $this->eventType,
            'visibility' => $this->eventVisibility,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'all_day' => false,
            'location' => $this->eventLocation ?: null,
            'priority' => 'MEDIUM',
            'status' => 'SCHEDULED',
        ]);

        /*
         * Creator is always an accepted participant.
         */
        CalendarEventParticipant::create([
            'calendar_event_id' => $event->id,
            'user_id' => auth()->id(),
            'response_status' => 'ACCEPTED',
            'responded_at' => now(),
        ]);

        /*
         * Specific users.
         */
        if ($this->eventVisibility === 'USERS') {

            $participantIds = collect($this->participantUserIds)
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id !== (int) auth()->id())
                ->unique()
                ->values();

            foreach ($participantIds as $userId) {

                CalendarEventParticipant::create([
                    'calendar_event_id' => $event->id,
                    'user_id' => $userId,
                    'response_status' => 'PENDING',
                ]);

                $participant = User::find($userId);

                if ($participant) {

                    $participant->notify(
                        new CalendarInvitationNotification(
                            $event,
                            auth()->user()->name
                        )
                    );
                }
            }
        }

        /*
         * Company event:
         *
         * We intentionally DO NOT create participant rows for
         * every employee. COMPANY visibility is resolved dynamically.
         */
        CalendarEventReminder::create([
            'calendar_event_id' => $event->id,
            'user_id' => auth()->id(),
            'minutes_before' => $this->reminderMinutes,
        ]);

        /*
         * For specific-user events, create a reminder for each participant.
         */
        if ($this->eventVisibility === 'USERS') {

            foreach (
                collect($this->participantUserIds)
                    ->filter(fn ($id) => (int) $id !== (int) auth()->id())
                    ->unique()
                as $userId
            ) {
                CalendarEventReminder::create([
                    'calendar_event_id' => $event->id,
                    'user_id' => $userId,
                    'minutes_before' => $this->reminderMinutes,
                ]);
            }
        }

        $this->resetEventForm();

        $this->showEventModal = false;

        $this->dispatch(
            'calendar-event-created'
        );
    }

    public function updateEvent(
        int $eventId,
        string $title,
        string $eventType,
        string $eventVisibility,
        string $eventDate,
        string $eventStartTime,
        string $eventEndTime,
        string $eventLocation = '',
        string $eventDescription = ''
    ): void {
        $event = CalendarEvent::query()
            ->whereKey($eventId)
            ->where('created_by', auth()->id())
            ->first();

        if (! $event) {
            $this->addError(
                'calendarEvent',
                'Calendar event not found or you are not authorized to edit this event.'
            );

            return;
        }

        if ($event->status === 'CANCELLED') {
            $this->addError(
                'calendarEvent',
                'Cancelled calendar events cannot be edited.'
            );

            return;
        }

        $title = trim($title);
        $eventType = strtoupper(trim($eventType));
        $eventVisibility = strtoupper(trim($eventVisibility));
        $eventDate = trim($eventDate);
        $eventStartTime = trim($eventStartTime);
        $eventEndTime = trim($eventEndTime);
        $eventLocation = trim($eventLocation);
        $eventDescription = trim($eventDescription);

        if (
            $title === ''
            || ! in_array(
                $eventType,
                ['MEETING', 'REMINDER', 'EVENT', 'DEADLINE'],
                true
            )
            || ! in_array(
                $eventVisibility,
                ['PERSONAL', 'USERS', 'COMPANY'],
                true
            )
        ) {
            $this->addError(
                'calendarEvent',
                'Invalid calendar event data.'
            );

            return;
        }

        try {
            $startAt = Carbon::parse(
                $eventDate . ' ' . $eventStartTime
            );

            $endAt = Carbon::parse(
                $eventDate . ' ' . $eventEndTime
            );
        } catch (\Throwable) {
            $this->addError(
                'calendarEvent',
                'Invalid event date or time.'
            );

            return;
        }

        if ($endAt->lessThanOrEqualTo($startAt)) {
            $this->addError(
                'calendarEvent',
                'End time must be later than start time.'
            );

            return;
        }

        DB::transaction(function () use (
            $event,
            $title,
            $eventType,
            $eventVisibility,
            $startAt,
            $endAt,
            $eventLocation,
            $eventDescription
        ): void {
            $event->update([
                'title' => $title,
                'event_type' => $eventType,
                'visibility' => $eventVisibility,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'location' => $eventLocation !== ''
                    ? $eventLocation
                    : null,
                'description' => $eventDescription !== ''
                    ? $eventDescription
                    : null,
            ]);

            /*
             * The event schedule has changed.
             * Existing unsent reminders must be recalculated
             * against the new start_at.
             */
            CalendarEventReminder::query()
                ->where('calendar_event_id', $event->id)
                ->update([
                    'sent_at' => null,
                ]);
        });

        $this->currentDate = $startAt->format('Y-m-d');

        $this->dispatch('calendar-event-updated');
    }

    public function moveEvent(
        int $eventId,
        string $newStartAt
    ): void {
        $event = CalendarEvent::query()
            ->whereKey($eventId)
            ->where('created_by', auth()->id())
            ->first();

        if (! $event) {
            $this->addError(
                'calendarEvent',
                'Calendar event not found or you are not authorized to move this event.'
            );

            return;
        }

        if ($event->status === 'CANCELLED') {
            $this->addError(
                'calendarEvent',
                'Cancelled calendar events cannot be moved.'
            );

            return;
        }

        try {
            $newStart = Carbon::parse($newStartAt);
        } catch (\Throwable) {
            $this->addError(
                'calendarEvent',
                'Invalid event start time.'
            );

            return;
        }

        /*
         * Calendar drag uses 15-minute increments.
         */
        $minutes = $newStart->minute;

        $snappedMinutes = (int) (
            round($minutes / 15) * 15
        );

        if ($snappedMinutes >= 60) {
            $newStart
                ->addHour()
                ->setMinute(0);
        } else {
            $newStart->setMinute($snappedMinutes);
        }

        $newStart->second(0);

        /*
         * Preserve the original event duration.
         */
        $durationMinutes = $event->start_at->diffInMinutes(
            $event->end_at ?? $event->start_at->copy()->addHour()
        );

        $durationMinutes = max(
            1,
            $durationMinutes
        );

        $newEnd = $newStart->copy()->addMinutes(
            $durationMinutes
        );

        DB::transaction(function () use (
            $event,
            $newStart,
            $newEnd
        ): void {
            $event->update([
                'start_at' => $newStart,
                'end_at' => $newEnd,
            ]);

            /*
             * Recalculate reminders against the new schedule.
             */
            CalendarEventReminder::query()
                ->where('calendar_event_id', $event->id)
                ->update([
                    'sent_at' => null,
                ]);
        });

        $this->currentDate = $newStart->format('Y-m-d');

        $this->dispatch('calendar-event-moved');
    }


    public function createTask(): void
    {
        $this->validate([
            'taskTitle' => ['required', 'string', 'max:255'],
            'taskPriority' => ['required'],
        ]);

        CalendarTask::create([
            'user_id' => auth()->id(),
            'title' => $this->taskTitle,
            'due_at' => $this->taskDueAt ?: null,
            'priority' => $this->taskPriority,
            'status' => 'OPEN',
        ]);

        $this->taskTitle = '';

        $this->taskDueAt = now()
            ->addDay()
            ->setTime(17, 0)
            ->format('Y-m-d\TH:i');

        $this->taskPriority = 'MEDIUM';

        $this->showTaskModal = false;
    }

    public function completeTask(int $taskId): void
    {
        CalendarTask::query()
            ->where('id', $taskId)
            ->where('user_id', auth()->id())
            ->update([
                'status' => 'COMPLETED',
            ]);
    }

    public function resetEventForm(): void
    {
        $this->eventTitle = '';
        $this->eventType = 'MEETING';
        $this->eventVisibility = 'PERSONAL';
        $this->eventDate = $this->currentDate;
        $this->eventStartTime = '09:00';
        $this->eventEndTime = '10:00';
        $this->eventLocation = '';
        $this->eventDescription = '';

        $this->participantUserIds = [];
        $this->participantSearch = '';

        $this->reminderMinutes = 15;
    }

    public function getAvailableUsers()
    {
        return User::query()
            ->active()
            ->whereKeyNot(auth()->id())
            ->when(
                filled($this->participantSearch),
                function ($query) {
                    $search = trim($this->participantSearch);

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            )
            ->orderBy('name')
            ->limit(100)
            ->get();
    }

}