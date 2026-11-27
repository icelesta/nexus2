<x-filament-panels::page>

    <div class="space-y-5">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <x-heroicon-o-calendar-days class="h-6 w-6" />
                    </div>

                    <div>
                        <h1 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                            Calendar
                        </h1>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Manage your schedule, meetings, reminders and tasks.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-750"
                    title="Calendar Settings"
                >
                    <x-heroicon-o-cog-6-tooth class="h-4 w-4" />
                    Settings
                </button>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-750"
                >
                    <x-heroicon-o-arrow-path class="h-4 w-4" />
                    Sync Calendar
                </button>

                <button
                    type="button"
                    wire:click="$set('showEventModal', true)"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    <x-heroicon-o-plus class="h-4 w-4" />
                    New Event
                </button>

            </div>
        </div>


        {{-- =========================================================
            MAIN CALENDAR CARD
        ========================================================== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

            <div class="grid min-h-[720px] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)]">


                {{-- =================================================
                    LEFT SIDEBAR
                ================================================== --}}
                <aside class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-700 dark:bg-gray-900 lg:border-b-0 lg:border-r">

                    <div class="space-y-6 p-5">

                        {{-- Calendar selector --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Calendar
                            </label>

                            <button
                                type="button"
                                class="flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                                    All Calendars
                                </span>

                                <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                            </button>
                        </div>


                        {{-- Mini calendar --}}
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <button
                                    type="button"
                                    wire:click="previousPeriod"
                                    class="rounded-md p-1.5 text-gray-500 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                >
                                    <x-heroicon-o-chevron-left class="h-4 w-4" />
                                </button>

                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($currentDate)->format('F Y') }}
                                </span>

                                <button
                                    type="button"
                                    wire:click="nextPeriod"
                                    class="rounded-md p-1.5 text-gray-500 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                >
                                    <x-heroicon-o-chevron-right class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-7 gap-1 text-center">

                                @foreach (['S','M','T','W','T','F','S'] as $day)
                                    <div class="py-1 text-[10px] font-semibold text-gray-400">
                                        {{ $day }}
                                    </div>
                                @endforeach

                                @php
                                    $miniMonth = \Carbon\Carbon::parse($currentDate)->startOfMonth();
                                    $start = $miniMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                                    $end = $miniMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
                                    $miniDate = $start->copy();
                                @endphp

                                @while ($miniDate <= $end)

                                    @php
                                        $isCurrentMonth = $miniDate->month === $miniMonth->month;
                                        $isToday = $miniDate->isToday();
                                        $isSelected = $miniDate->format('Y-m-d') === \Carbon\Carbon::parse($currentDate)->format('Y-m-d');
                                    @endphp

                                    <button
                                        type="button"
                                        wire:click="$set('currentDate', '{{ $miniDate->format('Y-m-d') }}')"
                                        class="
                                            flex h-8 w-8 items-center justify-center rounded-full text-xs transition
                                            {{ ! $isCurrentMonth ? 'text-gray-300 dark:text-gray-700' : 'text-gray-600 dark:text-gray-300' }}
                                            {{ $isSelected ? 'bg-blue-600 font-semibold text-white' : '' }}
                                            {{ $isToday && ! $isSelected ? 'font-bold text-blue-600 ring-1 ring-blue-500' : '' }}
                                            hover:bg-blue-50 dark:hover:bg-gray-800
                                        "
                                    >
                                        {{ $miniDate->day }}
                                    </button>

                                    @php
                                        $miniDate->addDay();
                                    @endphp

                                @endwhile

                            </div>
                        </div>


                        {{-- Event types --}}
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Event Type
                                </h3>
                            </div>

                            <div class="space-y-2">

                                @foreach([
                                    ['label' => 'Meeting', 'color' => 'bg-blue-500'],
                                    ['label' => 'Reminder', 'color' => 'bg-amber-500'],
                                    ['label' => 'Event', 'color' => 'bg-emerald-500'],
                                    ['label' => 'Deadline', 'color' => 'bg-red-500'],
                                    ['label' => 'Personal', 'color' => 'bg-violet-500'],
                                    ['label' => 'Shared', 'color' => 'bg-cyan-500'],
                                    ['label' => 'Company', 'color' => 'bg-gray-500'],
                                ] as $type)

                                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                                        <input
                                            type="checkbox"
                                            checked
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                        >

                                        <span class="h-2.5 w-2.5 rounded-full {{ $type['color'] }}"></span>

                                        <span>
                                            {{ $type['label'] }}
                                        </span>
                                    </label>

                                @endforeach

                            </div>
                        </div>


                        {{-- To Do List --}}
                        <div>

                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    To Do List
                                </h3>

                                <button
                                    type="button"
                                    wire:click="$set('showTaskModal', true)"
                                    class="rounded-md p-1 text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-800"
                                    title="Add Task"
                                >
                                    <x-heroicon-o-plus class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="space-y-3">

                                @forelse($this->getTasks() as $task)

                                    <div class="flex items-start gap-2">

                                        <button
                                            type="button"
                                            wire:click="completeTask({{ $task->id }})"
                                            class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border border-gray-300 dark:border-gray-600"
                                        >
                                        </button>

                                        <div class="min-w-0 flex-1">

                                            <p class="truncate text-sm font-medium text-gray-700 dark:text-gray-200">
                                                {{ $task->title }}
                                            </p>

                                            @if($task->due_at)
                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    {{ $task->due_at->format('d M, H:i') }}
                                                </p>
                                            @endif

                                        </div>

                                        @php
                                            $priorityClass = match ($task->priority) {
                                                'URGENT' => 'bg-red-100 text-red-700',
                                                'HIGH' => 'bg-orange-100 text-orange-700',
                                                'MEDIUM' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="rounded px-1.5 py-0.5 text-[9px] font-bold {{ $priorityClass }}">
                                            {{ $task->priority }}
                                        </span>

                                    </div>

                                @empty

                                    <div class="rounded-lg border border-dashed border-gray-200 px-3 py-4 text-center text-xs text-gray-400 dark:border-gray-700">
                                        No open tasks.
                                    </div>

                                @endforelse

                            </div>

                        </div>


                        {{-- Reminders --}}
                        <div>

                            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Reminders
                            </h3>

                            <div class="space-y-2">

                                @forelse($this->getReminders() as $reminder)

                                    <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">

                                        <p class="truncate text-xs font-semibold text-gray-700 dark:text-gray-200">
                                            {{ $reminder->title }}
                                        </p>

                                        <p class="mt-1 text-[11px] text-gray-400">
                                            {{ $reminder->start_at->format('d M Y, H:i') }}
                                        </p>

                                    </div>

                                @empty

                                    <p class="text-xs text-gray-400">
                                        No upcoming reminders.
                                    </p>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </aside>


                {{-- =================================================
                    CALENDAR CONTENT
                ================================================== --}}
                <section class="min-w-0">

                    {{-- Calendar toolbar --}}
                    <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 lg:flex-row lg:items-center lg:justify-between">

                        <div class="flex items-center gap-2">

                            <button
                                type="button"
                                wire:click="goToday"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                            >
                                Today
                            </button>

                            <button
                                type="button"
                                wire:click="previousPeriod"
                                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <x-heroicon-o-chevron-left class="h-4 w-4" />
                            </button>

                            <button
                                type="button"
                                wire:click="nextPeriod"
                                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <x-heroicon-o-chevron-right class="h-4 w-4" />
                            </button>

                            <h2 class="ml-2 text-base font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($currentDate)->format('F Y') }}
                            </h2>

                        </div>


                        <div class="flex items-center gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">

                            @foreach([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'list' => 'List',
                            ] as $mode => $label)

                                <button
                                    type="button"
                                    wire:click="setViewMode('{{ $mode }}')"
                                    class="
                                        rounded-md px-3 py-1.5 text-xs font-medium transition
                                        {{ $viewMode === $mode
                                            ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-700 dark:text-blue-400'
                                            : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'
                                        }}
                                    "
                                >
                                    {{ $label }}
                                </button>

                            @endforeach

                        </div>

                    </div>


                    {{-- Calendar scope --}}
                    <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-5 py-3 dark:border-gray-800">

                        @foreach([
                            'my' => 'My Calendar',
                            'shared' => 'Shared with Me',
                            'company' => 'Company Calendar',
                        ] as $scope => $label)

                            <button
                                type="button"
                                wire:click="setScope('{{ $scope }}')"
                                class="
                                    rounded-full px-3 py-1.5 text-xs font-semibold transition
                                    {{ $calendarScope === $scope
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300'
                                        : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'
                                    }}
                                "
                            >
                                {{ $label }}
                            </button>

                        @endforeach

                    </div>

                    {{-- =================================================
                        DAY VIEW
                    ================================================== --}}
                    @if($viewMode === 'day')

                        @php
                            $dayDate = \Carbon\Carbon::parse($currentDate);
                            $events = $this->getEvents();

                            /*
                             * IMPORTANT:
                             * Day View uses one fixed coordinate system:
                             *
                             * 1 hour = 64px
                             * 24 hours = 1536px
                             */
                            $hourHeight = 64;
                            $dayHeight = 24 * $hourHeight;
                        @endphp

                        <div class="overflow-x-auto">

                            <div class="min-w-[720px]">

                                {{-- Day Header --}}
                                <div class="grid grid-cols-[80px_minmax(0,1fr)] border-b border-gray-200 dark:border-gray-700">

                                    <div></div>

                                    <div class="border-l border-gray-100 px-4 py-4 dark:border-gray-800">

                                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                            {{ $dayDate->format('l') }}
                                        </div>

                                        <div
                                            class="mt-1 text-lg font-semibold
                                            {{ $dayDate->isToday()
                                                ? 'text-blue-600'
                                                : 'text-gray-900 dark:text-gray-100'
                                            }}"
                                        >
                                            {{ $dayDate->format('d F Y') }}
                                        </div>

                                    </div>

                                </div>


                                {{-- Day Timeline --}}
                                <div class="grid grid-cols-[80px_minmax(0,1fr)]">

                                    {{-- =================================================
                                        TIME AXIS
                                    ================================================== --}}
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900"
                                        style="height: {{ $dayHeight }}px;"
                                    >

                                        @for($hour = 0; $hour < 24; $hour++)

                                            <div
                                                class="box-border border-t border-gray-100 pr-3 pt-1 text-right text-[10px] text-gray-400 dark:border-gray-800"
                                                style="height: {{ $hourHeight }}px;"
                                            >
                                                {{ sprintf('%02d:00', $hour) }}
                                            </div>

                                        @endfor

                                    </div>


                                    {{-- =================================================
                                        SCHEDULE
                                    ================================================== --}}
                                    <div
                                        class="relative border-l border-gray-100 dark:border-gray-800
                                        {{ $dayDate->isToday()
                                            ? 'bg-blue-50/[0.18] dark:bg-blue-950/[0.08]'
                                            : ''
                                        }}"
                                        style="height: {{ $dayHeight }}px;"
                                    >

                                        {{-- =================================================
                                            HOUR GRID
                                        ================================================== --}}
                                        @for($hour = 0; $hour < 24; $hour++)

                                            <div
                                                class="absolute left-0 right-0 border-t border-gray-100 dark:border-gray-800"
                                                style="top: {{ $hour * $hourHeight }}px;"
                                            ></div>

                                        @endfor


                                        {{-- =================================================
                                            CURRENT TIME INDICATOR
                                        ================================================== --}}
                                        @if($dayDate->isToday())

                                            @php
                                                $now = now();

                                                $currentMinutes =
                                                    ($now->hour * 60) +
                                                    $now->minute;

                                                $currentTop =
                                                    ($currentMinutes / 60) * $hourHeight;
                                            @endphp

                                            <div
                                                class="absolute left-0 right-0 z-20"
                                                style="top: {{ $currentTop }}px;"
                                            >

                                                <div class="relative">

                                                    <span
                                                        class="absolute -left-1.5 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"
                                                    ></span>

                                                    <div class="border-t-2 border-red-400"></div>

                                                    <span
                                                        class="absolute right-2 -top-3 rounded bg-red-500 px-1.5 py-0.5 text-[9px] font-semibold text-white"
                                                    >
                                                        {{ $now->format('H:i') }}
                                                    </span>

                                                </div>

                                            </div>

                                        @endif


                                        {{-- =================================================
                                            EVENTS
                                        ================================================== --}}
                                        @foreach($events as $event)

                                            @php
                                                $eventStart = $event->start_at->copy();

                                                $eventEnd = $event->end_at
                                                    ? $event->end_at->copy()
                                                    : $eventStart->copy()->addHour();

                                                /*
                                                 * Clip events crossing midnight
                                                 * to the selected day's visible range.
                                                 */
                                                $visibleStart = $eventStart->lt(
                                                    $dayDate->copy()->startOfDay()
                                                )
                                                    ? $dayDate->copy()->startOfDay()
                                                    : $eventStart;

                                                $visibleEnd = $eventEnd->gt(
                                                    $dayDate->copy()->endOfDay()
                                                )
                                                    ? $dayDate->copy()->endOfDay()
                                                    : $eventEnd;

                                                $startMinutes =
                                                    ($visibleStart->hour * 60) +
                                                    $visibleStart->minute;

                                                $duration = max(
                                                    30,
                                                    $visibleStart->diffInMinutes($visibleEnd)
                                                );

                                                $top =
                                                    ($startMinutes / 60) * $hourHeight;

                                                $height = max(
                                                    44,
                                                    ($duration / 60) * $hourHeight
                                                );

                                                $eventClass = match ($event->event_type) {
                                                    'MEETING' =>
                                                        'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200',

                                                    'REMINDER' =>
                                                        'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200',

                                                    'EVENT' =>
                                                        'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200',

                                                    'DEADLINE' =>
                                                        'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200',

                                                    default =>
                                                        'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200',
                                                };
                                            @endphp

                                            <div
                                                class="absolute left-2 right-2 z-10 overflow-hidden rounded-lg border px-3 py-2 text-xs shadow-sm {{ $eventClass }}"
                                                style="
                                                    top: {{ $top }}px;
                                                    min-height: {{ $height }}px;
                                                "
                                            >

                                                <div class="flex items-start gap-2">

                                                    <x-heroicon-o-calendar-days
                                                        class="mt-0.5 h-4 w-4 shrink-0"
                                                    />

                                                    <div class="min-w-0 flex-1">

                                                        <div class="truncate font-semibold">
                                                            {{ $event->title }}
                                                        </div>

                                                        <div class="mt-0.5 text-[10px] opacity-75">
                                                            {{ $visibleStart->format('H:i') }}
                                                            –
                                                            {{ $visibleEnd->format('H:i') }}
                                                        </div>

                                                        @if($event->location)

                                                            <div class="mt-1 flex items-center gap-1 text-[10px] opacity-75">

                                                                <x-heroicon-o-map-pin
                                                                    class="h-3 w-3 shrink-0"
                                                                />

                                                                <span class="truncate">
                                                                    {{ $event->location }}
                                                                </span>

                                                            </div>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- =================================================
                        WEEK VIEW
                    ================================================== --}}
                    @if($viewMode === 'week')

                        @php
                            $days = $this->getWeekDays();
                            $events = $this->getEvents();
                        @endphp

                        <div class="overflow-x-auto">

                            <div class="min-w-[850px]">

                                {{-- Day headers --}}
                                <div class="grid grid-cols-[70px_repeat(7,minmax(110px,1fr))] border-b border-gray-200 dark:border-gray-700">

                                    <div></div>

                                    @foreach($days as $day)

                                        @php
                                            $date = \Carbon\Carbon::parse($day);
                                        @endphp

                                        <div class="border-l border-gray-100 px-2 py-3 text-center dark:border-gray-800">

                                            <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                                {{ $date->format('D') }}
                                            </div>

                                            <div class="mt-1 text-lg font-semibold {{ $date->isToday() ? 'text-blue-600' : 'text-gray-800 dark:text-gray-100' }}">
                                                {{ $date->format('d') }}
                                            </div>

                                        </div>

                                    @endforeach

                                </div>


                                {{-- Schedule --}}
                                <div class="grid grid-cols-[70px_repeat(7,minmax(110px,1fr))]">

                                    {{-- Time axis --}}
                                    <div class="bg-gray-50/50 dark:bg-gray-900">

                                        @for($hour = 0; $hour < 24; $hour++)

                                            <div class="h-16 border-b border-gray-100 pr-2 pt-1 text-right text-[10px] text-gray-400 dark:border-gray-800">
                                                {{ sprintf('%02d:00', $hour) }}
                                            </div>

                                        @endfor

                                    </div>


                                    {{-- Day columns --}}
                                    @foreach($days as $day)

                                        @php
                                            $dateKey = \Carbon\Carbon::parse($day)->format('Y-m-d');
                                            $dayEvents = $events->filter(
                                                fn ($event) => $event->start_at->format('Y-m-d') === $dateKey
                                            );
                                        @endphp

                                        <div class="relative border-l border-gray-100 dark:border-gray-800">

                                            @for($hour = 0; $hour < 24; $hour++)

                                                <div class="h-16 border-b border-gray-100 dark:border-gray-800"></div>

                                            @endfor


                                            {{-- Events --}}
                                            @foreach($dayEvents as $event)

                                                @php
                                                    $startMinutes = ($event->start_at->hour * 60) + $event->start_at->minute;
                                                    $duration = $event->end_at
                                                        ? max(30, $event->start_at->diffInMinutes($event->end_at))
                                                        : 60;

                                                    $top = ($startMinutes / 60) * 64;
                                                    $height = max(42, ($duration / 60) * 64);

                                                    $eventClass = match ($event->event_type) {
                                                        'MEETING' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200',
                                                        'REMINDER' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200',
                                                        'EVENT' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200',
                                                        'DEADLINE' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200',
                                                        default => 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200',
                                                    };
                                                @endphp

                                                <div
                                                    class="absolute left-1 right-1 z-10 overflow-hidden rounded-md border px-2 py-1.5 text-[11px] shadow-sm {{ $eventClass }}"
                                                    style="top: {{ $top }}px; min-height: {{ $height }}px;"
                                                >

                                                    <div class="truncate font-semibold">
                                                        {{ $event->title }}
                                                    </div>

                                                    <div class="mt-0.5 truncate opacity-75">
                                                        {{ $event->start_at->format('H:i') }}
                                                        @if($event->end_at)
                                                            - {{ $event->end_at->format('H:i') }}
                                                        @endif
                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>


                    {{-- =================================================
                        LIST VIEW
                    ================================================== --}}
                    @elseif($viewMode === 'list')

                        @php
                            $events = $this->getEvents();
                        @endphp

                        <div class="divide-y divide-gray-100 dark:divide-gray-800">

                            @forelse($events as $event)

                                <div class="flex items-center gap-4 px-5 py-4">

                                    <div class="w-16 text-center">
                                        <div class="text-xs uppercase text-gray-400">
                                            {{ $event->start_at->format('M') }}
                                        </div>

                                        <div class="text-xl font-bold text-gray-800 dark:text-white">
                                            {{ $event->start_at->format('d') }}
                                        </div>
                                    </div>

                                    <div class="h-10 w-1 rounded-full
                                        {{ match($event->event_type) {
                                            'MEETING' => 'bg-blue-500',
                                            'REMINDER' => 'bg-amber-500',
                                            'EVENT' => 'bg-emerald-500',
                                            'DEADLINE' => 'bg-red-500',
                                            default => 'bg-gray-500',
                                        } }}
                                    "></div>

                                    <div class="min-w-0 flex-1">

                                        <div class="truncate text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $event->title }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ $event->start_at->format('H:i') }}

                                            @if($event->end_at)
                                                - {{ $event->end_at->format('H:i') }}
                                            @endif

                                            @if($event->location)
                                                · {{ $event->location }}
                                            @endif
                                        </div>

                                    </div>

                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        {{ $event->event_type }}
                                    </span>

                                </div>

                            @empty

                                <div class="px-5 py-16 text-center text-sm text-gray-400">
                                    No events found.
                                </div>

                            @endforelse

                        </div>


                    {{-- =================================================
                        DAY / MONTH PLACEHOLDER
                    ================================================== --}}
                    @else

                        <div class="flex min-h-[600px] items-center justify-center px-5">

                            <div class="max-w-md text-center">

                                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950">
                                    <x-heroicon-o-calendar-days class="h-7 w-7" />
                                </div>

                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ ucfirst($viewMode) }} View
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-gray-400">
                                    The {{ $viewMode }} calendar view will use the same event engine
                                    and calendar data.
                                </p>

                            </div>

                        </div>

                    @endif

                </section>

            </div>

        </div>

    </div>


    {{-- =============================================================
        NEW EVENT MODAL
    ============================================================== --}}
    @if($showEventModal)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">

                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">

                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            New Event
                        </h2>

                        <p class="mt-0.5 text-xs text-gray-400">
                            Create a meeting, reminder, event or deadline.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showEventModal', false)"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800"
                    >
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>

                </div>


                <div class="grid gap-4 p-5 md:grid-cols-2">

                    <div class="md:col-span-2">

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Event Title
                        </label>

                        <input
                            type="text"
                            wire:model.defer="eventTitle"
                            placeholder="e.g. Purchasing Review Meeting"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Event Type
                        </label>

                        <select
                            wire:model.defer="eventType"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="MEETING">Meeting</option>
                            <option value="REMINDER">Reminder</option>
                            <option value="EVENT">Event</option>
                            <option value="DEADLINE">Deadline</option>
                        </select>

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Visibility
                        </label>

                        <select
                            wire:model.live="eventVisibility"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="PERSONAL">Personal</option>
                            <option value="USERS">Specific Users</option>
                            <option value="COMPANY">Everyone / Company</option>
                        </select>

                        @if($eventVisibility === 'USERS')

                            <div class="md:col-span-2">

                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50/60 dark:border-gray-700 dark:bg-gray-800/40">

                                    {{-- Header --}}
                                    <div class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">

                                        <div>
                                            <div class="flex items-center gap-2">

                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                                                    <x-heroicon-o-user-group class="h-4 w-4" />
                                                </div>

                                                <div>
                                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-100">
                                                        Participants
                                                    </div>

                                                    <div class="text-[10px] text-gray-400">
                                                        Select users who should receive this meeting invitation.
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        {{-- Selected counter --}}
                                        <div class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">

                                            {{ count($participantUserIds) }}
                                            {{ count($participantUserIds) === 1 ? 'user' : 'users' }}
                                            selected

                                        </div>

                                    </div>


                                    {{-- Search --}}
                                    <div class="border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">

                                        <div class="relative">

                                            <x-heroicon-o-magnifying-glass
                                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                            />

                                            <input
                                                type="text"
                                                wire:model.live.debounce.250ms="participantSearch"
                                                placeholder="Search user by name or email..."
                                                class="w-full rounded-lg border-gray-200 bg-gray-50 py-2.5 pl-9 pr-3 text-xs text-gray-700 placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:bg-gray-800"
                                            >

                                        </div>

                                    </div>


                                    {{-- User list --}}
                                    <div class="max-h-60 overflow-y-auto">

                                        @forelse($this->getAvailableUsers() as $user)

                                            @php
                                                $name = trim($user->name ?? 'User');

                                                $nameParts = preg_split('/\s+/', $name);

                                                $initials = collect($nameParts)
                                                    ->filter()
                                                    ->take(2)
                                                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                                    ->implode('');

                                                $isSelected = in_array(
                                                    (string) $user->id,
                                                    array_map('strval', $participantUserIds),
                                                    true
                                                );
                                            @endphp


                                            <label
                                                class="
                                                    group flex cursor-pointer items-center gap-3 border-b border-gray-100 px-4 py-3 transition
                                                    last:border-b-0
                                                    dark:border-gray-800
                                                    {{ $isSelected
                                                        ? 'bg-blue-50/70 dark:bg-blue-950/20'
                                                        : 'bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800'
                                                    }}
                                                "
                                            >

                                                {{-- Checkbox --}}
                                                <input
                                                    type="checkbox"
                                                    value="{{ $user->id }}"
                                                    wire:model="participantUserIds"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                                                >


                                                {{-- Avatar --}}
                                                <div
                                                    class="
                                                        flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                                        text-[11px] font-bold
                                                        {{ $isSelected
                                                            ? 'bg-blue-600 text-white'
                                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200'
                                                        }}
                                                    "
                                                >
                                                    {{ $initials ?: 'U' }}
                                                </div>


                                                {{-- User information --}}
                                                <div class="min-w-0 flex-1">

                                                    <div class="truncate text-xs font-semibold text-gray-800 dark:text-gray-100">
                                                        {{ $user->name }}
                                                    </div>

                                                    @if($user->email)
                                                        <div class="mt-0.5 truncate text-[10px] text-gray-400">
                                                            {{ $user->email }}
                                                        </div>
                                                    @endif

                                                </div>


                                                {{-- Selected indicator --}}
                                                @if($isSelected)

                                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white">

                                                        <x-heroicon-o-check class="h-3.5 w-3.5" />

                                                    </div>

                                                @else

                                                    <div class="h-6 w-6 shrink-0"></div>

                                                @endif

                                            </label>

                                        @empty

                                            <div class="px-4 py-8 text-center">

                                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">

                                                    <x-heroicon-o-user-minus class="h-5 w-5" />

                                                </div>

                                                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                    No users found
                                                </p>

                                                @if(filled($participantSearch))
                                                    <p class="mt-1 text-[10px] text-gray-400">
                                                        Try another name or email.
                                                    </p>
                                                @endif

                                            </div>

                                        @endforelse

                                    </div>


                                    {{-- Footer --}}
                                    <div class="border-t border-gray-200 bg-gray-50 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800/50">

                                        <div class="flex items-center gap-1.5 text-[10px] text-gray-400">

                                            <x-heroicon-o-information-circle class="h-3.5 w-3.5" />

                                            <span>
                                                Selected users will receive the meeting invitation.
                                            </span>

                                        </div>

                                    </div>

                                </div>


                                @error('participantUserIds')

                                    <p class="mt-1.5 text-xs font-medium text-red-600">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        @endif

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Date
                        </label>

                        <input
                            type="date"
                            wire:model.defer="eventDate"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                    </div>


                    <div class="grid grid-cols-2 gap-3">

                        <div>

                            <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                                Start
                            </label>

                            <input
                                type="time"
                                wire:model.defer="eventStartTime"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            >

                        </div>

                        <div>

                            <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                                End
                            </label>

                            <input
                                type="time"
                                wire:model.defer="eventEndTime"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            >

                        </div>

                    </div>


                    <div class="md:col-span-2">


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Reminder
                        </label>

                        <select
                            wire:model.defer="reminderMinutes"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="5">5 minutes before</option>
                            <option value="15">15 minutes before</option>
                            <option value="30">30 minutes before</option>
                            <option value="60">1 hour before</option>
                        </select>

                    </div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Location
                        </label>

                        <input
                            type="text"
                            wire:model.defer="eventLocation"
                            placeholder="Meeting room / location / online"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                    </div>


                    <div class="md:col-span-2">

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Description
                        </label>

                        <textarea
                            wire:model.defer="eventDescription"
                            rows="3"
                            placeholder="Additional information..."
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        ></textarea>

                    </div>

                </div>


                <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">

                    <button
                        type="button"
                        wire:click="$set('showEventModal', false)"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="createEvent"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Create Event
                    </button>

                </div>

            </div>

        </div>

    @endif


    {{-- =============================================================
        NEW TASK MODAL
    ============================================================== --}}
    @if($showTaskModal)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">

                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">

                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            New Task
                        </h2>

                        <p class="mt-0.5 text-xs text-gray-400">
                            Add a personal task to your To Do List.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showTaskModal', false)"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800"
                    >
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>

                </div>


                <div class="space-y-4 p-5">

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Task
                        </label>

                        <input
                            type="text"
                            wire:model.defer="taskTitle"
                            placeholder="e.g. Follow up supplier quotation"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Due Date & Time
                        </label>

                        <input
                            type="datetime-local"
                            wire:model.defer="taskDueAt"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Priority
                        </label>

                        <select
                            wire:model.defer="taskPriority"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="LOW">Low</option>
                            <option value="MEDIUM">Medium</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>

                    </div>

                </div>


                <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">

                    <button
                        type="button"
                        wire:click="$set('showTaskModal', false)"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="createTask"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Add Task
                    </button>

                </div>

            </div>

        </div>

    @endif

</x-filament-panels::page>