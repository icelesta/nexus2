<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\WithPagination;

class Notifications extends Page
{
    use WithPagination;

    protected static ?string $navigationLabel = 'Notifications';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Notifications';

    protected string $view = 'filament.pages.notifications';

    protected static ?string $slug = 'notifications';

    public string $filter = 'all';

    public string $search = '';

    public int|string $perPage = 10;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    public static function getNavigationLabel(): string
    {
        return 'Notifications';
    }

    public static function getNavigationBadge(): ?string
    {
        if (! auth()->check()) {
            return null;
        }

        $count = auth()
            ->user()
            ->unreadNotifications()
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Counts
    |--------------------------------------------------------------------------
    */

    public function getAllCount(): int
    {
        return auth()
            ->user()
            ->notifications()
            ->count();
    }

    public function getUnreadCount(): int
    {
        return auth()
            ->user()
            ->unreadNotifications()
            ->count();
    }

    public function getApprovalCount(): int
    {
        return auth()
            ->user()
            ->notifications()
            ->where(
                'data->event',
                'APPROVAL_REQUIRED'
            )
            ->count();
    }

    public function getTransactionCount(): int
    {
        return auth()
            ->user()
            ->notifications()
            ->whereIn(
                'data->event',
                [
                    'LEVEL_APPROVED',
                    'FINAL_APPROVED',
                    'REJECTED',
                ]
            )
            ->count();
    }

    public function getCalendarCount(): int
    {
        return auth()
            ->user()
            ->notifications()
            ->whereIn(
                'data->event',
                [
                    'CALENDAR_INVITATION',
                    'CALENDAR_REMINDER',
                ]
            )
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public function setFilter(string $filter): void
    {
        $allowed = [
            'all',
            'unread',
            'approval',
            'transaction',
            'calendar',
        ];

        $this->filter = in_array(
            $filter,
            $allowed,
            true
        )
            ? $filter
            : 'all';

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Per Page
    |--------------------------------------------------------------------------
    */

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications Query
    |--------------------------------------------------------------------------
    */

    public function getNotifications(): LengthAwarePaginator
    {
        $query = auth()
            ->user()
            ->notifications()
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        match ($this->filter) {
            'unread' => $query->whereNull('read_at'),

            'approval' => $query->where(
                'data->event',
                'APPROVAL_REQUIRED'
            ),

            'transaction' => $query->whereIn(
                'data->event',
                [
                    'LEVEL_APPROVED',
                    'FINAL_APPROVED',
                    'REJECTED',
                ]
            ),

            'calendar' => $query->whereIn(
                'data->event',
                [
                    'CALENDAR_INVITATION',
                    'CALENDAR_REMINDER',
                ]
            ),

            default => null,
        };

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim($this->search);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query
                    ->where(
                        'data->document_no',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'data->title',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'data->body',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'data->document_type',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        if ($this->perPage === 'all') {
            return $query->paginate(
                max(
                    1,
                    (int) $query->count()
                )
            );
        }

        return $query->paginate(
            max(
                1,
                (int) $this->perPage
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mark Single Notification As Read
    |--------------------------------------------------------------------------
    */

    public function markAsRead(
        string $notificationId
    ): void {
        $notification = auth()
            ->user()
            ->notifications()
            ->whereKey($notificationId)
            ->first();

        if (! $notification) {
            return;
        }

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Mark All Notifications As Read
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead(): void
    {
        auth()
            ->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        $this->resetPage();
    }

    public function getCalendarResponseStatus($notification): ?string
    {
        if (
            ($notification->data['event'] ?? null)
            !== 'CALENDAR_INVITATION'
        ) {
            return null;
        }

        $eventId = $notification->data['document_id'] ?? null;

        if (! $eventId) {
            return null;
        }

        return \App\Models\CalendarEventParticipant::query()
            ->where('calendar_event_id', $eventId)
            ->where('user_id', auth()->id())
            ->value('response_status');
    }

    /*
    |--------------------------------------------------------------------------
    | Notification Helpers
    |--------------------------------------------------------------------------
    */

    public function isUnread(
        DatabaseNotification $notification
    ): bool {
        return $notification->read_at === null;
    }

    public function getEventLabel(
        DatabaseNotification $notification
    ): string {
        return match (
            $notification->data['event'] ?? null
        ) {
            'APPROVAL_REQUIRED' =>
                'Approval Required',

            'LEVEL_APPROVED' =>
                'Approval Completed',

            'FINAL_APPROVED' =>
                'Approved',

            'REJECTED' =>
                'Rejected',

            'CALENDAR_INVITATION' =>
                'Calendar Invitation',

            'CALENDAR_REMINDER' =>
                'Calendar Reminder',

            default =>
                'Notification',
        };
    }

    public function getEventColor(
        DatabaseNotification $notification
    ): string {
        return match (
            $notification->data['event'] ?? null
        ) {
            'APPROVAL_REQUIRED' => 'warning',
            'LEVEL_APPROVED' => 'success',
            'FINAL_APPROVED' => 'success',
            'REJECTED' => 'danger',

            'CALENDAR_INVITATION' => 'info',
            'CALENDAR_REMINDER' => 'warning',

            default => 'gray',
        };
    }

    public function getEventIcon(
        DatabaseNotification $notification
    ): string {
        return match (
            $notification->data['event'] ?? null
        ) {
            'APPROVAL_REQUIRED' =>
                'heroicon-o-clock',

            'LEVEL_APPROVED' =>
                'heroicon-o-check-circle',

            'FINAL_APPROVED' =>
                'heroicon-o-check-badge',

            'REJECTED' =>
                'heroicon-o-x-circle',

            'CALENDAR_INVITATION' =>
                'heroicon-o-calendar-days',

            'CALENDAR_REMINDER' =>
                'heroicon-o-bell-alert',

            default =>
                'heroicon-o-bell',
        };
    }

    public function getDocumentTypeLabel(
        DatabaseNotification $notification
    ): string {
        return match (
            $notification->data['document_type'] ?? null
        ) {
            'MATERIAL_REQUISITION' =>
                'Material Requisition',

            'PURCHASE_ORDER' =>
                'Purchase Order',

            default =>
                $notification->data['document_type']
                    ?? 'Transaction',
        };
    }

    public function getNotificationUrl(
        DatabaseNotification $notification
    ): ?string {
        $url = $notification->data['url'] ?? null;

        return is_string($url) && $url !== ''
            ? $url
            : null;
    }

    public function respondToCalendarInvitation(
        string $notificationId,
        string $response
    ): void {
        $response = strtoupper(trim($response));

        if (! in_array($response, [
            'ACCEPTED',
            'DECLINED',
            'TENTATIVE',
        ], true)) {
            return;
        }

        $notification = auth()
            ->user()
            ->notifications()
            ->whereKey($notificationId)
            ->first();

        if (! $notification) {
            return;
        }

        if (
            ($notification->data['event'] ?? null)
            !== 'CALENDAR_INVITATION'
        ) {
            return;
        }

        $eventId = $notification->data['document_id'] ?? null;

        if (! $eventId) {
            return;
        }

        $participant = \App\Models\CalendarEventParticipant::query()
            ->where('calendar_event_id', $eventId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $participant) {
            return;
        }

        $event = \App\Models\CalendarEvent::query()
            ->find($eventId);

        if (! $event || $event->status === 'CANCELLED') {
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(
            function () use ($participant, $response, $notification): void {
                $participant->update([
                    'response_status' => $response,
                    'responded_at' => now(),
                ]);

                if (! $notification->read_at) {
                    $notification->markAsRead();
                }
            }
        );
    }

}