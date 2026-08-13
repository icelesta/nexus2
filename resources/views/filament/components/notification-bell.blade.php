{{-- =========================================================
 | NEXUS 2.0
 | TOPBAR NOTIFICATION CONTROL
 | AM-5.6.3-F / UI FINAL
 |
 | Position:
 | GLOBAL_SEARCH_BEFORE
 |
 | Layout:
 | Notifications + Bell + Badge
 |
 | IMPORTANT:
 | This component is the ONLY custom notification control.
 ========================================================= --}}

@php

    $user = auth()->user();

    $notifications = $user
        ? $user->notifications()
            ->latest()
            ->take(5)
            ->get()
        : collect();

    $unreadCount = $user
        ? $user->unreadNotifications()->count()
        : 0;

@endphp

<div
    class="relative flex items-center"
    x-data="{ open: false }"
>

    {{-- =====================================================
     | NOTIFICATION CONTROL
     ====================================================== --}}

    <button
        type="button"
        @click="open = !open"
        class="
            relative
            flex
            h-9
            items-center
            gap-2
            rounded-lg
            px-3
            text-sm
            font-semibold
            text-amber-600
            transition
            duration-150
            hover:bg-gray-100
            hover:text-amber-700
            focus:outline-none
            dark:text-amber-400
            dark:hover:bg-gray-800
            dark:hover:text-amber-300
        "
        aria-label="Notifications"
        :aria-expanded="open.toString()"
    >

        {{-- =================================================
         | LABEL
         ================================================== --}}

        <span class="whitespace-nowrap">
            Notifications
        </span>


        {{-- =================================================
         | BELL
         ================================================== --}}

        <span class="relative flex items-center">

            <x-heroicon-o-bell
                class="h-[18px] w-[18px]"
            />

            {{-- =================================================
             | UNREAD BADGE
             ================================================== --}}

            @if ($unreadCount > 0)

                <span
                    class="
                        absolute
                        -right-2
                        -top-2
                        flex
                        min-h-[15px]
                        min-w-[15px]
                        items-center
                        justify-center
                        rounded-full
                        bg-danger-600
                        px-1
                        text-[9px]
                        font-bold
                        leading-none
                        text-white
                        ring-2
                        ring-white
                        dark:ring-gray-900
                    "
                >
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>

            @endif

        </span>

    </button>


    {{-- =====================================================
     | DROPDOWN
     ====================================================== --}}

    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"

        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"

        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"

        class="
            absolute
            right-0
            top-full
            z-[9999]
            mt-2
            w-[370px]
            overflow-hidden
            rounded-xl
            border
            border-gray-200
            bg-white
            shadow-xl
            dark:border-gray-700
            dark:bg-gray-900
        "
    >

        {{-- =================================================
         | HEADER
         ================================================== --}}

        <div
            class="
                flex
                items-center
                justify-between
                border-b
                border-gray-200
                px-4
                py-3
                dark:border-gray-700
            "
        >

            <div>

                <div
                    class="
                        text-sm
                        font-semibold
                        text-gray-900
                        dark:text-white
                    "
                >
                    Notifications
                </div>

                <div
                    class="
                        mt-0.5
                        text-xs
                        text-gray-500
                        dark:text-gray-400
                    "
                >
                    {{ $unreadCount }}
                    unread notification{{ $unreadCount === 1 ? '' : 's' }}
                </div>

            </div>


            @if ($unreadCount > 0)

                <form
                    method="POST"
                    action="{{ route('notifications.read-all') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="
                            text-xs
                            font-medium
                            text-primary-600
                            transition
                            hover:text-primary-700
                            dark:text-primary-400
                            dark:hover:text-primary-300
                        "
                    >
                        Mark all as read
                    </button>

                </form>

            @endif

        </div>


        {{-- =================================================
         | NOTIFICATION LIST
         ================================================== --}}

        <div class="max-h-[420px] overflow-y-auto">

            @forelse ($notifications as $notification)

                @php

                    $data = $notification->data ?? [];

                    $title =
                        $data['title']
                        ?? 'Notification';

                    $documentNo =
                        $data['document_no']
                        ?? null;

                    $body =
                        $data['body']
                        ?? null;

                    $icon =
                        $data['icon']
                        ?? 'heroicon-o-bell';

                @endphp


                <a
                    href="{{ route('notifications.open', $notification->id) }}"
                    class="
                        block
                        border-b
                        border-gray-100
                        px-4
                        py-3
                        transition
                        hover:bg-gray-50
                        dark:border-gray-800
                        dark:hover:bg-gray-800/60
                    "
                >

                    <div class="flex gap-3">

                        {{-- ICON --}}

                        <div
                            class="
                                flex
                                h-9
                                w-9
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                bg-gray-100
                                dark:bg-gray-800
                            "
                        >

                            <x-dynamic-component
                                :component="$icon"
                                class="h-4 w-4 text-gray-500"
                            />

                        </div>


                        {{-- CONTENT --}}

                        <div class="min-w-0 flex-1">

                            <div
                                class="
                                    flex
                                    items-start
                                    justify-between
                                    gap-2
                                "
                            >

                                <div
                                    class="
                                        truncate
                                        text-sm
                                        font-semibold
                                        text-gray-900
                                        dark:text-white
                                    "
                                >
                                    {{ $title }}
                                </div>

                                @if (! $notification->read_at)

                                    <span
                                        class="
                                            mt-1
                                            h-2
                                            w-2
                                            shrink-0
                                            rounded-full
                                            bg-danger-500
                                        "
                                    ></span>

                                @endif

                            </div>


                            @if ($documentNo)

                                <div
                                    class="
                                        mt-0.5
                                        text-xs
                                        font-medium
                                        text-primary-600
                                        dark:text-primary-400
                                    "
                                >
                                    {{ $documentNo }}
                                </div>

                            @endif


                            @if ($body)

                                <div
                                    class="
                                        mt-1
                                        line-clamp-2
                                        text-xs
                                        leading-5
                                        text-gray-500
                                        dark:text-gray-400
                                    "
                                >
                                    {{ $body }}
                                </div>

                            @endif


                            <div
                                class="
                                    mt-1
                                    text-[10px]
                                    text-gray-400
                                "
                            >
                                {{ $notification->created_at?->diffForHumans() }}
                            </div>

                        </div>

                    </div>

                </a>

            @empty

                {{-- EMPTY STATE --}}

                <div
                    class="
                        px-6
                        py-10
                        text-center
                    "
                >

                    <x-heroicon-o-bell-slash
                        class="
                            mx-auto
                            h-8
                            w-8
                            text-gray-300
                        "
                    />

                    <div
                        class="
                            mt-2
                            text-sm
                            font-medium
                            text-gray-600
                            dark:text-gray-300
                        "
                    >
                        No notifications
                    </div>

                    <div
                        class="
                            mt-1
                            text-xs
                            text-gray-400
                        "
                    >
                        You're all caught up.
                    </div>

                </div>

            @endforelse

        </div>

    </div>

</div>