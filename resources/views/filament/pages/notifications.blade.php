<x-filament-panels::page>

    <div class="space-y-5">

        @php
            $notifications = $this->getNotifications();
        @endphp


        {{-- =========================================================
             PAGE HEADER
        ========================================================== --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-4">

                {{-- HEADER ICON --}}

                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-warning-50 dark:bg-warning-500/10">

                    <x-filament::icon
                        icon="heroicon-o-bell"
                        class="h-7 w-7 text-warning-600 dark:text-warning-400"
                    />

                </div>


                {{-- TITLE --}}

                <div>

                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Notifications
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Stay updated with your transaction activities and approvals.
                    </p>

                </div>

            </div>


            {{-- MARK ALL --}}

            @if ($this->getUnreadCount() > 0)

                <button
                    type="button"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled"
                    wire:target="markAllAsRead"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                >

                    <x-filament::icon
                        icon="heroicon-o-check"
                        class="h-4 w-4"
                    />

                    <span wire:loading.remove wire:target="markAllAsRead">
                        Mark all as read
                    </span>

                    <span wire:loading wire:target="markAllAsRead">
                        Processing...
                    </span>

                </button>

            @endif

        </div>


        {{-- =========================================================
             MAIN NOTIFICATION CARD
        ========================================================== --}}

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">


            {{-- =====================================================
                 TOOLBAR
            ====================================================== --}}

            <div class="border-b border-gray-200 p-3 dark:border-gray-700">

                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">


                    {{-- FILTER TABS --}}

                    <div class="flex flex-wrap items-center gap-1">

                        {{-- ALL --}}

                        <button
                            type="button"
                            wire:click="setFilter('all')"
                            class="inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-medium transition
                            {{ $filter === 'all'
                                ? 'bg-warning-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'
                            }}"
                        >

                            All

                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $filter === 'all'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'
                                }}"
                            >
                                {{ $this->getAllCount() }}
                            </span>

                        </button>


                        {{-- UNREAD --}}

                        <button
                            type="button"
                            wire:click="setFilter('unread')"
                            class="inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-medium transition
                            {{ $filter === 'unread'
                                ? 'bg-warning-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'
                            }}"
                        >

                            Unread

                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $filter === 'unread'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400'
                                }}"
                            >
                                {{ $this->getUnreadCount() }}
                            </span>

                        </button>


                        {{-- APPROVAL --}}

                        <button
                            type="button"
                            wire:click="setFilter('approval')"
                            class="inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-medium transition
                            {{ $filter === 'approval'
                                ? 'bg-warning-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'
                            }}"
                        >

                            Approval

                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $filter === 'approval'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400'
                                }}"
                            >
                                {{ $this->getApprovalCount() }}
                            </span>

                        </button>


                        {{-- TRANSACTION --}}

                        <button
                            type="button"
                            wire:click="setFilter('transaction')"
                            class="inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-medium transition
                            {{ $filter === 'transaction'
                                ? 'bg-warning-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'
                            }}"
                        >

                            Transaction

                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $filter === 'transaction'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'
                                }}"
                            >
                                {{ $this->getTransactionCount() }}
                            </span>

                        </button>

                        {{-- CALENDAR --}}

                        <button
                            type="button"
                            wire:click="setFilter('calendar')"
                            class="inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-medium transition
                            {{ $filter === 'calendar'
                                ? 'bg-warning-500 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'
                            }}"
                        >
                            Calendar

                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $filter === 'calendar'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'
                                }}"
                            >
                                {{ $this->getCalendarCount() }}
                            </span>
                        </button>

                    </div>


                    {{-- SEARCH + SHOW --}}

                    <div class="flex w-full flex-col gap-3 sm:flex-row xl:w-auto">


                        {{-- SHOW --}}

                        <div class="flex items-center gap-2">

                            <span class="whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Show
                            </span>

                            <select
                                wire:model.live="perPage"
                                class="h-10 rounded-lg border-gray-200 bg-white py-2 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-warning-500 focus:ring-warning-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            >
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="all">All</option>
                            </select>

                        </div>


                        {{-- SEARCH --}}

                        <div class="relative w-full sm:w-80">

                            <x-filament::icon
                                icon="heroicon-o-magnifying-glass"
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                            />

                            <input
                                type="search"
                                wire:model.live.debounce.400ms="search"
                                placeholder="Search notifications..."
                                class="h-10 block w-full rounded-lg border-gray-200 bg-white pl-10 pr-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-warning-500 focus:ring-warning-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            />

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 NOTIFICATION LIST
            ====================================================== --}}

            <div class="divide-y divide-gray-100 dark:divide-gray-800">

                @forelse ($notifications as $notification)

                    @php
                        $event = $notification->data['event'] ?? null;
                        $isUnread = ! $notification->read_at;

                        $icon = match ($event) {
                            'APPROVAL_REQUIRED' => 'heroicon-o-clock',
                            'LEVEL_APPROVED' => 'heroicon-o-check-circle',
                            'FINAL_APPROVED' => 'heroicon-o-check-badge',
                            'REJECTED' => 'heroicon-o-x-circle',

                            'CALENDAR_INVITATION' => 'heroicon-o-calendar-days',
                            'CALENDAR_REMINDER' => 'heroicon-o-bell-alert',

                            default => 'heroicon-o-bell',
                        };

                        $iconStyle = match ($event) {
                            'APPROVAL_REQUIRED' =>
                                'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',

                            'LEVEL_APPROVED',
                            'FINAL_APPROVED' =>
                                'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400',

                            'REJECTED' =>
                                'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400',

                            'CALENDAR_INVITATION' =>
                                'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-400',

                            'CALENDAR_REMINDER' =>
                                'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',

                            default =>
                                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                        };

                        $badgeStyle = match ($event) {
                            'APPROVAL_REQUIRED' =>
                                'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',

                            'LEVEL_APPROVED',
                            'FINAL_APPROVED' =>
                                'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',

                            'REJECTED' =>
                                'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400',

                            'CALENDAR_INVITATION' =>
                                'bg-info-50 text-info-700 dark:bg-info-500/10 dark:text-info-400',

                            'CALENDAR_REMINDER' =>
                                'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',

                            default =>
                                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                        };

                        $eventLabel = match ($event) {
                            'APPROVAL_REQUIRED' => 'Approval Required',
                            'LEVEL_APPROVED' => 'Approval Completed',
                            'FINAL_APPROVED' => 'Approved',
                            'REJECTED' => 'Rejected',

                            'CALENDAR_INVITATION' => 'Calendar Invitation',
                            'CALENDAR_REMINDER' => 'Calendar Reminder',

                            default => 'Notification',
                        };

                        $calendarResponse = $this->getCalendarResponseStatus($notification);
                    @endphp


                    {{-- NOTIFICATION ROW --}}

                    <div
                        wire:key="notification-{{ $notification->id }}"
                        class="relative transition
                        {{ $isUnread
                            ? 'bg-warning-50/30 hover:bg-warning-50/50 dark:bg-warning-500/[0.03] dark:hover:bg-warning-500/[0.06]'
                            : 'bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800/40'
                        }}"
                    >

                        {{-- UNREAD INDICATOR --}}

                        @if ($isUnread)

                            <div class="absolute inset-y-0 left-0 w-1 bg-warning-500"></div>

                        @endif


                        <div class="flex flex-col gap-5 px-5 py-5 lg:flex-row lg:items-center lg:px-6">


                            {{-- ICON --}}

                            <div class="flex shrink-0 items-start">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $iconStyle }}">

                                    <x-filament::icon
                                        icon="{{ $icon }}"
                                        class="h-6 w-6"
                                    />

                                </div>

                            </div>


                            {{-- MAIN CONTENT --}}

                            <div class="min-w-0 flex-1">

                                {{-- TITLE --}}

                                <div class="flex flex-wrap items-center gap-2">

                                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>


                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $badgeStyle }}">
                                        {{ $eventLabel }}
                                    </span>

                                </div>


                                {{-- DOCUMENT NUMBER --}}

                                @if (! empty($notification->data['document_no']))

                                    <div class="mt-1 text-sm font-semibold text-warning-600 dark:text-warning-400">
                                        {{ $notification->data['document_no'] }}
                                    </div>

                                @endif


                                {{-- BODY --}}

                                @if (! empty($notification->data['body']))

                                    <p class="mt-1.5 max-w-4xl text-sm leading-6 text-gray-600 dark:text-gray-400">
                                        {{ $notification->data['body'] }}
                                    </p>

                                @endif


                                {{-- META --}}

                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500 dark:text-gray-400">

                                    {{-- DOCUMENT TYPE --}}

                                    <span class="inline-flex items-center gap-1.5">

                                        <x-filament::icon
                                            icon="heroicon-o-document-text"
                                            class="h-3.5 w-3.5"
                                        />

                                        {{ $this->getDocumentTypeLabel($notification) }}

                                    </span>


                                    {{-- LEVEL --}}

                                    @if (! empty($notification->data['approval_level']))

                                        <span class="inline-flex items-center gap-1.5">

                                            <x-filament::icon
                                                icon="heroicon-o-bars-3-bottom-left"
                                                class="h-3.5 w-3.5"
                                            />

                                            Level {{ $notification->data['approval_level'] }}

                                        </span>

                                    @endif


                                    {{-- ROLE --}}

                                    @if (! empty($notification->data['role_name']))

                                        <span class="inline-flex items-center gap-1.5">

                                            <x-filament::icon
                                                icon="heroicon-o-user-group"
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ $notification->data['role_name'] }}

                                        </span>

                                    @endif

                                </div>

                            </div>


                            {{-- RIGHT SIDE --}}

                            <div class="flex shrink-0 flex-col gap-3 lg:w-72 lg:items-end">

                                {{-- TIME --}}

                                <div class="text-right">

                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at?->diffForHumans() }}
                                    </div>

                                    <div class="mt-0.5 text-[11px] text-gray-400 dark:text-gray-500">
                                        {{ $notification->created_at?->format('d M Y H:i') }}
                                    </div>

                                </div>


                                {{-- ACTIONS --}}

                                <div class="flex flex-wrap items-center justify-end gap-2">

                                    @if ($event === 'CALENDAR_INVITATION')

                                        @if ($calendarResponse === 'ACCEPTED')

                                            <span
                                                class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-success-200 bg-success-50 px-3.5 text-xs font-semibold text-success-700 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-400"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-check-circle"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Accepted

                                            </span>

                                        @elseif ($calendarResponse === 'DECLINED')

                                            <span
                                                class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-danger-200 bg-danger-50 px-3.5 text-xs font-semibold text-danger-700 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-400"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-x-circle"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Declined

                                            </span>

                                        @elseif ($calendarResponse === 'TENTATIVE')

                                            <span
                                                class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-warning-200 bg-warning-50 px-3.5 text-xs font-semibold text-warning-700 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-400"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-question-mark-circle"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Tentative

                                            </span>

                                        @else

                                            <button
                                                type="button"
                                                wire:click="respondToCalendarInvitation('{{ $notification->id }}', 'ACCEPTED')"
                                                wire:loading.attr="disabled"
                                                class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-success-200 bg-success-50 px-3.5 text-xs font-semibold text-success-700 shadow-sm transition hover:bg-success-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-400 dark:hover:bg-success-500/20"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-check"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Accept

                                            </button>


                                            <button
                                                type="button"
                                                wire:click="respondToCalendarInvitation('{{ $notification->id }}', 'TENTATIVE')"
                                                wire:loading.attr="disabled"
                                                class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-warning-200 bg-warning-50 px-3.5 text-xs font-semibold text-warning-700 shadow-sm transition hover:bg-warning-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-400 dark:hover:bg-warning-500/20"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-question-mark-circle"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Tentative

                                            </button>


                                            <button
                                                type="button"
                                                wire:click="respondToCalendarInvitation('{{ $notification->id }}', 'DECLINED')"
                                                wire:loading.attr="disabled"
                                                class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-danger-200 bg-danger-50 px-3.5 text-xs font-semibold text-danger-700 shadow-sm transition hover:bg-danger-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-400 dark:hover:bg-danger-500/20"
                                            >

                                                <x-filament::icon
                                                    icon="heroicon-o-x-mark"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Decline

                                            </button>

                                        @endif

                                    @endif

                                    @if (! empty($notification->data['url']))

                                        <a
                                            href="{{ route('notifications.open', ['notification' => $notification->id]) }}"
                                            class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-warning-200 bg-white px-3.5 text-xs font-semibold text-warning-700 shadow-sm transition hover:bg-warning-50 dark:border-warning-500/30 dark:bg-gray-900 dark:text-warning-400 dark:hover:bg-warning-500/10"
                                        >

                                            <x-filament::icon
                                                icon="heroicon-o-arrow-up-right"
                                                class="h-3.5 w-3.5"
                                            />

                                            Open Transaction

                                        </a>

                                    @endif


                                    @if ($isUnread)

                                        <button
                                            type="button"
                                            wire:click="markAsRead('{{ $notification->id }}')"
                                            wire:loading.attr="disabled"
                                            class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                                        >

                                            <x-filament::icon
                                                icon="heroicon-o-check"
                                                class="h-3.5 w-3.5"
                                            />

                                            Mark as read

                                        </button>

                                    @else

                                        <span class="inline-flex h-9 items-center gap-1.5 rounded-lg px-3 text-xs font-medium text-gray-400 dark:text-gray-500">

                                            <x-filament::icon
                                                icon="heroicon-o-check-circle"
                                                class="h-3.5 w-3.5"
                                            />

                                            Read

                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                @empty


                    {{-- =================================================
                         EMPTY STATE
                    ================================================== --}}

                    <div class="flex flex-col items-center justify-center px-6 py-20">

                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">

                            <x-filament::icon
                                icon="heroicon-o-bell-slash"
                                class="h-8 w-8 text-gray-400"
                            />

                        </div>

                        <h3 class="mt-5 text-base font-semibold text-gray-950 dark:text-white">
                            No notifications
                        </h3>

                        <p class="mt-1 max-w-md text-center text-sm text-gray-500 dark:text-gray-400">
                            There are no notifications matching your current filter or search criteria.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- =====================================================
                 PAGINATION
            ====================================================== --}}

            @if ($notifications->hasPages())

                <div class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6 lg:flex-row lg:items-center lg:justify-between">


                    {{-- PAGINATION INFO --}}

                    <div class="text-sm text-gray-500 dark:text-gray-400">

                        Showing

                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $notifications->firstItem() ?? 0 }}
                        </span>

                        to

                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $notifications->lastItem() ?? 0 }}
                        </span>

                        of

                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $notifications->total() }}
                        </span>

                        notifications

                    </div>


                    {{-- CONTROLS --}}

                    <div class="flex items-center gap-1 overflow-x-auto pb-1">


                        {{-- PREVIOUS --}}

                        @if ($notifications->onFirstPage())

                            <button
                                type="button"
                                disabled
                                class="inline-flex h-9 shrink-0 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500"
                            >
                                &laquo; Previous
                            </button>

                        @else

                            <button
                                type="button"
                                wire:click="previousPage"
                                class="inline-flex h-9 shrink-0 items-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                &laquo; Previous
                            </button>

                        @endif


                        {{-- PAGE NUMBERS --}}

                        <div class="hidden items-center gap-1 md:flex">

                            @php
                                $currentPage = $notifications->currentPage();
                                $lastPage = $notifications->lastPage();

                                $startPage = max(
                                    1,
                                    $currentPage - 2
                                );

                                $endPage = min(
                                    $lastPage,
                                    $currentPage + 2
                                );
                            @endphp


                            {{-- FIRST --}}

                            @if ($startPage > 1)

                                <button
                                    type="button"
                                    wire:click="gotoPage(1)"
                                    class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    1
                                </button>

                                @if ($startPage > 2)

                                    <span class="px-1 text-xs text-gray-400">
                                        ...
                                    </span>

                                @endif

                            @endif


                            {{-- VISIBLE --}}

                            @for ($page = $startPage; $page <= $endPage; $page++)

                                @if ($page === $currentPage)

                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-warning-500 px-3 text-xs font-semibold text-white shadow-sm"
                                    >
                                        {{ $page }}
                                    </button>

                                @else

                                    <button
                                        type="button"
                                        wire:click="gotoPage({{ $page }})"
                                        class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm transition hover:border-warning-300 hover:bg-warning-50 hover:text-warning-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-warning-500/10"
                                    >
                                        {{ $page }}
                                    </button>

                                @endif

                            @endfor


                            {{-- LAST --}}

                            @if ($endPage < $lastPage)

                                @if ($endPage < $lastPage - 1)

                                    <span class="px-1 text-xs text-gray-400">
                                        ...
                                    </span>

                                @endif

                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $lastPage }})"
                                    class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    {{ $lastPage }}
                                </button>

                            @endif

                        </div>


                        {{-- NEXT --}}

                        @if ($notifications->hasMorePages())

                            <button
                                type="button"
                                wire:click="nextPage"
                                class="inline-flex h-9 shrink-0 items-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                Next &raquo;
                            </button>

                        @else

                            <button
                                type="button"
                                disabled
                                class="inline-flex h-9 shrink-0 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-xs font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500"
                            >
                                Next &raquo;
                            </button>

                        @endif

                    </div>

                </div>

            @endif

        </div>

    </div>

</x-filament-panels::page>