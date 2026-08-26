@php

    $now = \App\Support\Timezone\UserTimezone::now();

    $hour = (int) $now->format('H');

    $greeting = match (true) {

        $hour < 12 =>
            'Selamat Pagi',

        $hour < 15 =>
            'Selamat Siang',

        $hour < 18 =>
            'Selamat Sore',

        default =>
            'Selamat Malam',
    };

    $userName =
        auth()->user()?->name
        ?? 'User';

@endphp


    {{-- =========================================================
         HERO GRID
         ========================================================= --}}

<div class="w-full max-w-none">

    <div
        class="
            grid
            grid-cols-1
            lg:grid-cols-[minmax(360px,2fr)_minmax(0,5fr)]
            gap-5
            lg:gap-6
            w-full
            max-w-none
        "
    >

        {{-- GREETING CARD --}}
        <div
            class="
                relative
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                shadow-sm
                min-h-[350px]
                flex
                flex-col
                justify-between
            "
        >

            {{-- ORANGE DOT PATTERN --}}

            <div
                class="
                    absolute
                    left-4
                    top-8
                    hidden
                    lg:block
                    opacity-70
                "
            >

                <div class="grid grid-cols-3 gap-2">

                    @for ($i = 0; $i < 8; $i++)

                        @for ($j = 0; $j < 3; $j++)

                            <div
                                class="
                                    h-1.5
                                    w-1.5
                                    rounded-full
                                    bg-orange-200
                                "
                            ></div>

                        @endfor

                    @endfor

                </div>

            </div>


            {{-- CONTENT --}}

            <div
                class="
                    relative
                    z-10
                    px-7
                    py-8
                    lg:px-10
                    lg:py-10
                "
            >

                {{-- EYEBROW --}}

                <p
                    class="
                        text-[10px]
                        lg:text-xs
                        font-bold
                        tracking-[0.32em]
                        text-orange-500
                    "
                >
                    NEXUS 2.0 | E R P
                </p>


                {{-- GREETING --}}

                <h1
                    class="
                        mt-5
                        text-3xl
                        lg:text-4xl
                        font-extrabold
                        leading-tight
                        tracking-tight
                        text-slate-900
                    "
                >

                    {{ $greeting }},

                    <br>

                    <span class="text-orange-500">
                        {{ $userName }}
                    </span>

                </h1>


                {{-- ACCENT LINE --}}

                <div class="mt-5 flex items-center gap-2">

                    <div
                        class="
                            h-1
                            w-10
                            rounded-full
                            bg-orange-500
                        "
                    ></div>

                    <div
                        class="
                            h-1
                            w-12
                            rounded-full
                            bg-slate-100
                        "
                    ></div>

                </div>


                {{-- DATE / TIME --}}

                <div
                    class="
                        mt-7
                        text-sm
                        font-medium
                        text-slate-600
                    "
                >

                    {{ $now->translatedFormat('l, d F Y') }}

                    <span class="mx-2 text-slate-300">
                        •
                    </span>

                    <span id="dashboard-clock">
                        {{ $now->format('H:i') }} WIB
                    </span>

                    <span class="mx-2 text-slate-300">
                        •
                    </span>

                    Jakarta

                </div>


                {{-- QUOTE --}}

                <div
                    class="
                        mt-7
                        flex
                        items-start
                        gap-4
                    "
                >

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            shrink-0
                            items-center
                            justify-center
                            rounded-full
                            bg-orange-50
                            text-xl
                            font-bold
                            text-orange-500
                        "
                    >
                        “
                    </div>


                    <div
                        class="
                            pt-1
                            text-sm
                            leading-6
                            text-slate-600
                        "
                    >

                        Kelola setiap proses dengan cerdas,

                        <br>

                        wujudkan operasi yang efisien dan terintegrasi.

                    </div>

                </div>

            </div>


            {{-- DECORATIVE BOTTOM LINE --}}

            <div
                class="
                    absolute
                    bottom-0
                    left-0
                    h-1
                    w-full
                    bg-gradient-to-r
                    from-orange-400
                    via-orange-200
                    to-transparent
                "
            ></div>

        </div>



        {{-- BMS HERO --}}
        <div
            class="
                relative
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-slate-900
                shadow-sm
                min-h-[350px]
                w-full
            "
        >
            <img
                src="{{ asset('images/dashboard-hero.png') }}"
                alt="PT. Besmindo Materi Sewatama - Oilfield Equipment"
                class="
                    absolute
                    inset-0
                    h-full
                    w-full
                    object-cover
                    object-center
                "
            />

            {{-- IMAGE OVERLAY --}}
            <div
                class="
                    absolute
                    inset-0
                    bg-gradient-to-r
                    from-slate-950/10
                    via-transparent
                    to-transparent
                "
            ></div>

            {{-- TOP ACCENT --}}
            <div
                class="
                    absolute
                    left-0
                    top-0
                    h-1
                    w-full
                    bg-gradient-to-r
                    from-blue-500
                    via-cyan-400
                    to-transparent
                "
            ></div>
        </div>

    </div>



    {{-- =========================================================
         LIVE CLOCK
         ========================================================= --}}

    <script>
        (() => {

            const updateDashboardClock = () => {

                const clock =
                    document.getElementById(
                        'dashboard-clock'
                    );

                if (! clock) {
                    return;
                }

                const now = new Date();

                const hours =
                    now
                        .getHours()
                        .toString()
                        .padStart(2, '0');

                const minutes =
                    now
                        .getMinutes()
                        .toString()
                        .padStart(2, '0');

                clock.textContent =
                    `${hours}:${minutes} WIB`;
            };


            updateDashboardClock();


            setInterval(
                updateDashboardClock,
                1000
            );

        })();
    </script>

</div>