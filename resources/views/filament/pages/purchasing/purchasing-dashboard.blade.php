<x-filament-panels::page>

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h1 class="text-4xl font-bold tracking-tight text-gray-900">
                    Purchasing Dashboard
                </h1>

            </div>

            <div class="text-left lg:text-right">

                <div class="text-xs uppercase tracking-widest text-gray-500">
                    {{ now()->format('l') }}
                </div>

                <div class="text-2xl font-bold text-gray-900">
                    {{ now()->format('d F Y') }}
                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- WELCOME BANNER --}}
        {{-- ========================================================= --}}
        <div
            class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-8 shadow-lg">

            <div class="max-w-4xl">

                <h2 class="text-3xl font-bold text-white">
                    Welcome Back 👋
                </h2>

                <p class="mt-4 text-base leading-7 text-blue-100">

                    Manage Material Requisition, Assignment Material
                    Requisition, Purchase Orders, Goods Receipt,
                    Vendor Performance and Purchasing Analytics
                    from a single dashboard.

                </p>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- DASHBOARD FILTER --}}
        {{-- ========================================================= --}}
        <div
            class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
        >

            {{-- Subtle accent --}}
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            <div class="p-5">

                {{-- Filter Header --}}
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                        >
                            <x-filament::icon
                                icon="heroicon-o-funnel"
                                class="h-5 w-5 text-blue-600"
                            />
                        </div>

                        <div>

                            <div class="text-sm font-semibold text-gray-900">
                                Dashboard Filters
                            </div>

                            <div class="text-xs text-gray-500">
                                Refine purchasing analytics
                            </div>

                        </div>

                    </div>

                    {{-- Active Filter Indicator --}}
                    <div class="flex items-center gap-2">

                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        ></span>

                        <span class="text-xs font-medium text-gray-500">
                            Filters Active
                        </span>

                    </div>

                </div>


                {{-- Filter Controls --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Branch --}}
                    <div class="xl:col-span-3">

                        <label
                            class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >
                            <x-filament::icon
                                icon="heroicon-o-building-office-2"
                                class="h-4 w-4 text-gray-400"
                            />

                            Branch
                        </label>

                        <div class="relative">

                            <select
                                wire:model.live="branchFilter"
                                class="block w-full appearance-none rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            >

                                <option value="">
                                    All Branch
                                </option>

                                @foreach ($this->branchOptions as $id => $name)

                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-gray-400"
                                />

                            </div>

                        </div>

                    </div>


                    {{-- Department --}}
                    <div class="xl:col-span-3">

                        <label
                            class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >
                            <x-filament::icon
                                icon="heroicon-o-briefcase"
                                class="h-4 w-4 text-gray-400"
                            />

                            Department
                        </label>

                        <div class="relative">

                            <select
                                wire:model.live="departmentFilter"
                                class="block w-full appearance-none rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            >

                                <option value="">
                                    All Department
                                </option>

                                @foreach ($this->departmentOptions as $id => $name)

                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-gray-400"
                                />

                            </div>

                        </div>

                    </div>


                    {{-- Date From --}}
                    <div class="xl:col-span-2">

                        <label
                            class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >
                            <x-filament::icon
                                icon="heroicon-o-calendar"
                                class="h-4 w-4 text-gray-400"
                            />

                            From
                        </label>

                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    {{-- Date To --}}
                    <div class="xl:col-span-2">

                        <label
                            class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >
                            <x-filament::icon
                                icon="heroicon-o-calendar"
                                class="h-4 w-4 text-gray-400"
                            />

                            To
                        </label>

                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    {{-- Refresh --}}
                    <div class="flex items-end xl:col-span-2">

                        <button
                            type="button"
                            wire:click="$refresh"
                            class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
                        >

                            <x-filament::icon
                                icon="heroicon-o-arrow-path"
                                class="h-4 w-4 transition duration-300 group-hover:rotate-180"
                            />

                            Refresh Dashboard

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- KPI SECTION --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">

            {{-- ===================================================== --}}
            {{-- MR --}}
            {{-- ===================================================== --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Material Requisition
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($this->materialRequisitionCount) }}
                        </div>

                        <div class="mt-2 text-sm text-blue-600">
                            +12 This Month
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100">

                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="h-7 w-7 text-blue-600"/>

                    </div>

                </div>

            </div>

            {{-- Assignment --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Assignment
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($this->assignmentMaterialRequisitionCount) }}
                        </div>

                        <div class="mt-2 text-sm text-amber-600">
                            Purchasing PIC Assignment
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-100">

                        <x-filament::icon
                            icon="heroicon-o-user-plus"
                            class="h-7 w-7 text-amber-600"/>

                    </div>

                </div>

            </div>

            {{-- Purchase Order --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Purchase Order
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($this->purchaseOrderCount) }}
                        </div>

                        <div class="mt-2 text-sm text-emerald-600">
                            Generated PO
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100">

                        <x-filament::icon
                            icon="heroicon-o-clipboard-document-list"
                            class="h-7 w-7 text-emerald-600"/>

                    </div>

                </div>

            </div>

            {{-- Open PO --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Open (PO)
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format(
                                $this->openPurchaseOrderCount
                            ) }}
                        </div>

                        <div class="mt-2 text-sm text-red-600">
                            Waiting Receipt
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-red-100">

                        <x-filament::icon
                            icon="heroicon-o-clock"
                            class="h-7 w-7 text-red-600"/>

                    </div>

                </div>

            </div>

            {{-- Spend --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Total Spend
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            Rp {{ number_format(
                                    $this->purchaseOrderSpend,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                        </div>

                        <div class="mt-2 text-sm text-indigo-600">
                            Current Period
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-100">

                        <x-filament::icon
                            icon="heroicon-o-banknotes"
                            class="h-7 w-7 text-indigo-600"/>

                    </div>

                </div>

            </div>

            {{-- Approval --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pending Approval
                        </div>

                        <div class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($this->pendingApprovalCount) }}
                        </div>

                        <div class="mt-2 text-sm text-orange-600">
                            Documents
                        </div>

                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-orange-100">

                        <x-filament::icon
                            icon="heroicon-o-exclamation-circle"
                            class="h-7 w-7 text-orange-600"/>

                    </div>

                </div>

            </div>


        </div>

        {{-- ========================================================= --}}
        {{-- PURCHASING WORKFLOW PIPELINE --}}
        {{-- UI ONLY — DATA / QUERY UNCHANGED --}}
        {{-- ========================================================= --}}

        <div class="overflow-x-auto">

            <div class="flex w-full min-w-[1050px] items-center px-6 py-6">

                @php

                    $pipeline = [

                        [
                            'code' => 'DM',
                            'title' => 'Direct Market',
                            'count' => $this->directMarketCount,
                            'status' => 'Open',
                            'icon' => 'heroicon-o-document-text',

                            'iconBg' => 'bg-gradient-to-br from-blue-50 to-blue-200',
                            'iconColor' => 'text-blue-600',

                            'connectorColor' => '#3b82f6',
                        ],

                        [
                            'code' => 'ADM',
                            'title' => 'Assignment Direct Market',
                            'count' => $this->assignmentDirectMarketCount,
                            'status' => 'In Progress',
                            'icon' => 'heroicon-o-user-plus',

                            'iconBg' => 'bg-gradient-to-br from-amber-50 to-amber-200',
                            'iconColor' => 'text-amber-600',

                            'connectorColor' => '#f59e0b',
                        ],

                        [
                            'code' => 'RR',
                            'title' => 'Receiving Record',
                            'count' => $this->goodsReceiptCount,
                            'status' => 'Receiving',
                            'icon' => 'heroicon-o-archive-box',

                            'iconBg' => 'bg-gradient-to-br from-rose-50 to-rose-200',
                            'iconColor' => 'text-rose-600',

                            'connectorColor' => '#f43f5e',
                        ],

                    ];

                @endphp


                @foreach($pipeline as $stage)

                    {{-- ================================================= --}}
                    {{-- PIPELINE CARD --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0 flex-1">

                        <div
                            class="relative rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                        >

                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <div class="text-[10px] font-bold tracking-[0.25em] text-gray-500">
                                        {{ $stage['code'] }}
                                    </div>

                                    <div class="mt-1.5 truncate text-base font-semibold leading-5 text-gray-900">
                                        {{ $stage['title'] }}
                                    </div>

                                </div>


                                {{-- Icon --}}
                                <div
                                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gray-100"
                                >

                                    <x-filament::icon
                                        :icon="$stage['icon']"
                                        class="h-5 w-5 text-gray-600"
                                    />

                                </div>

                            </div>


                            {{-- Count --}}
                            <div class="mt-5">

                                <div class="text-4xl font-bold leading-none text-gray-900">
                                    {{ number_format($stage['count']) }}
                                </div>

                            </div>


                            {{-- Status --}}
                            <div class="mt-4 flex items-center gap-2">

                                <span
                                    class="h-2 w-2 flex-shrink-0 rounded-full bg-green-500"
                                ></span>

                                <span class="text-xs font-medium text-gray-600">
                                    {{ $stage['status'] }}
                                </span>

                            </div>


                            {{-- Decorative Dots --}}
                            <div
                                class="absolute right-3.5 top-1/2 flex -translate-y-1/2 flex-col gap-1.5"
                            >

                                @for($i = 0; $i < 5; $i++)

                                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>

                                @endfor

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- CONNECTOR --}}
                    {{-- ================================================= --}}

                    @if(!$loop->last)

                        <div
                            class="relative z-10 flex min-w-[110px] flex-1 items-center"
                        >

                            {{-- Line Before Arrow --}}
                            <div
                                class="h-[2px] flex-1"
                                style="
                                    background-color: {{ $stage['connectorColor'] }};
                                    opacity: 0.45;
                                "
                            ></div>


                            {{-- Arrow --}}
                            <div
                                class="relative z-20 mx-3 flex h-9 w-9 flex-shrink-0
                                       items-center justify-center
                                       rounded-full
                                       border border-gray-200
                                       bg-white
                                       shadow-sm"
                            >

                                <x-filament::icon
                                    icon="heroicon-m-chevron-right"
                                    class="h-4 w-4 text-gray-500"
                                />

                            </div>


                            {{-- Line After Arrow --}}
                            <div
                                class="h-[2px] flex-1"
                                style="
                                    background-color: {{ $stage['connectorColor'] }};
                                    opacity: 0.45;
                                "
                            ></div>

                        </div>

                    @endif

                @endforeach

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- PURCHASING ANALYTICS CHARTS --}}
        {{-- 2 ROW × 2 COLUMN ENTERPRISE LAYOUT --}}
        {{-- UI ONLY — DATA / QUERY / FUNCTION UNCHANGED --}}
        {{-- ============================================================= --}}

        <div class="space-y-6">


            {{-- ========================================================= --}}
            {{-- ROW 1 --}}
            {{-- PURCHASE VALUE TREND + TOP 5 CATEGORIES --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">


                {{-- ===================================================== --}}
                {{-- PURCHASE VALUE TREND --}}
                {{-- ===================================================== --}}
                {{-- ANNUAL CHART — UI ONLY --}}
                {{-- Branch / Department respected --}}
                {{-- From / To filter intentionally ignored --}}
                {{-- ===================================================== --}}

                <div class="min-w-0">

                    <div class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        {{-- HEADER --}}

                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

                            <div>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50"
                                    >

                                        <x-filament::icon
                                            icon="heroicon-o-arrow-trending-up"
                                            class="h-5 w-5 text-indigo-600"
                                        />

                                    </div>

                                    <div>

                                        <h3 class="text-lg font-semibold text-gray-900">

                                            Purchase Value Trend

                                            <span class="font-normal text-gray-500">
                                                (IDR)
                                            </span>

                                        </h3>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            Approved Purchase Orders by month
                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- PERIOD --}}

                            <div
                                class="flex items-center gap-2 rounded-xl border border-gray-200
                                       bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm"
                            >

                                <span>
                                    This Year
                                </span>

                                <span class="text-xs text-gray-400">
                                    {{ now()->year }}
                                </span>

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-gray-400"
                                />

                            </div>

                        </div>


                        {{-- CHART --}}

                        <div class="px-6 pb-6 pt-4">

                            @php

                                $trendData = $this->purchaseValueTrend;

                                $trendValues = collect($trendData)
                                    ->pluck('purchase_value')
                                    ->map(fn ($value) => (float) $value);

                                $maxTrendValue = (float) (
                                    $trendValues->max() ?? 0
                                );

                                /*
                                |--------------------------------------------------------------------------
                                | Dynamic Chart Scale
                                |--------------------------------------------------------------------------
                                */

                                if ($maxTrendValue <= 0) {

                                    $chartMax = 100000;

                                } else {

                                    $magnitude = pow(
                                        10,
                                        floor(log10($maxTrendValue))
                                    );

                                    $step = $magnitude / 2;

                                    $chartMax = ceil(
                                        $maxTrendValue / $step
                                    ) * $step;

                                }

                                $chartMax = max(
                                    $chartMax,
                                    100000
                                );

                                /*
                                |--------------------------------------------------------------------------
                                | SVG Coordinates
                                |--------------------------------------------------------------------------
                                */

                                $chartLeft   = 70;
                                $chartRight  = 970;
                                $chartTop    = 30;
                                $chartBottom = 275;

                                $chartWidth = $chartRight - $chartLeft;
                                $chartHeight = $chartBottom - $chartTop;

                                $pointCount = count($trendData);

                                $points = [];

                                foreach ($trendData as $index => $trend) {

                                    $x = $chartLeft
                                        + (
                                            $index
                                            * (
                                                $chartWidth
                                                / max($pointCount - 1, 1)
                                            )
                                        );

                                    $value = (float) $trend['purchase_value'];

                                    $y = $chartBottom
                                        - (
                                            ($value / $chartMax)
                                            * $chartHeight
                                        );

                                    $points[] = [
                                        'x' => $x,
                                        'y' => $y,
                                        'value' => $value,
                                        'label' => $trend['label'],
                                    ];

                                }

                                /*
                                |--------------------------------------------------------------------------
                                | SVG Line Path
                                |--------------------------------------------------------------------------
                                */

                                $linePath = collect($points)
                                    ->map(
                                        fn ($point, $index) =>
                                            ($index === 0 ? 'M' : 'L')
                                            . ' '
                                            . round($point['x'], 2)
                                            . ' '
                                            . round($point['y'], 2)
                                    )
                                    ->implode(' ');

                                /*
                                |--------------------------------------------------------------------------
                                | IDR Formatter
                                |--------------------------------------------------------------------------
                                */

                                $formatChartValue = function ($value) {

                                    $value = (float) $value;

                                    if ($value >= 1000000000) {

                                        return 'Rp '
                                            . rtrim(
                                                rtrim(
                                                    number_format(
                                                        $value / 1000000000,
                                                        1,
                                                        '.',
                                                        ''
                                                    ),
                                                    '0'
                                                ),
                                                '.'
                                            )
                                            . ' B';

                                    }

                                    if ($value >= 1000000) {

                                        return 'Rp '
                                            . rtrim(
                                                rtrim(
                                                    number_format(
                                                        $value / 1000000,
                                                        1,
                                                        '.',
                                                        ''
                                                    ),
                                                    '0'
                                                ),
                                                '.'
                                            )
                                            . ' Jt';

                                    }

                                    if ($value >= 1000) {

                                        return 'Rp '
                                            . rtrim(
                                                rtrim(
                                                    number_format(
                                                        $value / 1000,
                                                        1,
                                                        '.',
                                                        ''
                                                    ),
                                                    '0'
                                                ),
                                                '.'
                                            )
                                            . ' K';

                                    }

                                    return 'Rp '
                                        . number_format(
                                            $value,
                                            0,
                                            ',',
                                            '.'
                                        );

                                };

                            @endphp


                            {{-- LEGEND --}}

                            <div class="mb-3 flex items-center justify-between">

                                <div class="flex items-center gap-2">

                                    <span
                                        class="h-2.5 w-2.5 rounded-full bg-blue-600"
                                    ></span>

                                    <span class="text-xs font-medium text-gray-600">
                                        Actual
                                    </span>

                                </div>

                                <div class="text-xs text-gray-400">
                                    IDR
                                </div>

                            </div>


                            {{-- SVG CHART --}}

                            <div class="w-full overflow-hidden">

                                <svg
                                    viewBox="0 0 1000 340"
                                    class="h-auto w-full"
                                    preserveAspectRatio="none"
                                    role="img"
                                    aria-label="Purchase Value Trend"
                                >

                                    {{-- GRID LINES --}}

                                    @for($grid = 0; $grid <= 4; $grid++)

                                        @php

                                            $gridRatio = $grid / 4;

                                            $gridY = $chartTop
                                                + (
                                                    $gridRatio
                                                    * $chartHeight
                                                );

                                            $gridValue = $chartMax
                                                * (
                                                    1 - $gridRatio
                                                );

                                        @endphp

                                        <line
                                            x1="{{ $chartLeft }}"
                                            y1="{{ $gridY }}"
                                            x2="{{ $chartRight }}"
                                            y2="{{ $gridY }}"
                                            stroke="currentColor"
                                            stroke-width="1"
                                            stroke-dasharray="5 5"
                                            class="text-gray-200"
                                        />

                                        <text
                                            x="{{ $chartLeft - 12 }}"
                                            y="{{ $gridY + 4 }}"
                                            text-anchor="end"
                                            class="fill-gray-400 text-[12px]"
                                        >
                                            {{ $formatChartValue($gridValue) }}
                                        </text>

                                    @endfor


                                    {{-- X AXIS --}}

                                    <line
                                        x1="{{ $chartLeft }}"
                                        y1="{{ $chartBottom }}"
                                        x2="{{ $chartRight }}"
                                        y2="{{ $chartBottom }}"
                                        stroke="currentColor"
                                        stroke-width="1"
                                        class="text-gray-200"
                                    />


                                    {{-- TREND LINE --}}

                                    <path
                                        d="{{ $linePath }}"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-blue-600"
                                    />


                                    {{-- AREA UNDER LINE --}}

                                    @php

                                        $areaPath = $linePath
                                            . ' L '
                                            . $chartRight
                                            . ' '
                                            . $chartBottom
                                            . ' L '
                                            . $chartLeft
                                            . ' '
                                            . $chartBottom
                                            . ' Z';

                                    @endphp

                                    <path
                                        d="{{ $areaPath }}"
                                        fill="currentColor"
                                        class="text-blue-50"
                                        opacity="0.45"
                                    />


                                    {{-- DATA POINTS --}}

                                    @foreach($points as $point)

                                        <circle
                                            cx="{{ $point['x'] }}"
                                            cy="{{ $point['y'] }}"
                                            r="7"
                                            fill="white"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            class="text-blue-600"
                                        >

                                            <title>
                                                {{ $point['label'] }} :
                                                {{ $formatChartValue($point['value']) }}
                                            </title>

                                        </circle>

                                    @endforeach


                                    {{-- MONTH LABELS --}}

                                    @foreach($points as $point)

                                        <text
                                            x="{{ $point['x'] }}"
                                            y="{{ $chartBottom + 30 }}"
                                            text-anchor="middle"
                                            class="fill-gray-500 text-[12px] font-medium"
                                        >
                                            {{ $point['label'] }}
                                        </text>

                                    @endforeach

                                </svg>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- TOP 5 CATEGORIES --}}
                {{-- ===================================================== --}}

                <div class="min-w-0">

                    <div class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        {{-- HEADER --}}

                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50"
                                >

                                    <x-filament::icon
                                        icon="heroicon-o-chart-pie"
                                        class="h-5 w-5 text-indigo-600"
                                    />

                                </div>

                                <div>

                                    <h3 class="text-lg font-semibold text-gray-900">

                                        Top 5 Categories

                                        <span class="font-normal text-gray-500">
                                            (by Purchase Value)
                                        </span>

                                    </h3>

                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Approved Purchase Orders by category
                                    </p>

                                </div>

                            </div>


                            {{-- PERIOD --}}

                            <div
                                class="flex items-center gap-2 rounded-xl border border-gray-200
                                       bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm"
                            >

                                <span>
                                    This Month
                                </span>

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-gray-400"
                                />

                            </div>

                        </div>


                        {{-- CATEGORY DATA --}}

                        <div class="px-6 pb-6 pt-5">

                            @php

                                $categoryData = collect(
                                    $this->topPurchaseCategories
                                );

                                $categoryTotal = (float) $categoryData
                                    ->sum('purchase_value');

                                $categoryColors = [
                                    '#4F46E5',
                                    '#3B82F6',
                                    '#F59E0B',
                                    '#16A34A',
                                    '#8B5CF6',
                                    '#94A3B8',
                                ];

                                $donutRadius = 82;

                                $donutCircumference =
                                    2 * pi() * $donutRadius;

                                $categoryOffset = 0;

                            @endphp


                            @if($categoryData->isNotEmpty() && $categoryTotal > 0)

                                <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-[1fr_1.15fr]">

                                    {{-- DONUT --}}

                                    <div class="flex items-center justify-center">

                                        <div class="relative h-[250px] w-[250px]">

                                            <svg
                                                viewBox="0 0 220 220"
                                                class="h-full w-full"
                                                role="img"
                                                aria-label="Top 5 Categories by Purchase Value"
                                            >

                                                <circle
                                                    cx="110"
                                                    cy="110"
                                                    r="{{ $donutRadius }}"
                                                    fill="none"
                                                    stroke="#F1F5F9"
                                                    stroke-width="30"
                                                />

                                                @foreach($categoryData as $index => $category)

                                                    @php

                                                        $value =
                                                            (float) $category['purchase_value'];

                                                        $percentage =
                                                            $categoryTotal > 0
                                                                ? (
                                                                    $value
                                                                    / $categoryTotal
                                                                ) * 100
                                                                : 0;

                                                        $segmentLength =
                                                            (
                                                                $percentage
                                                                / 100
                                                            )
                                                            * $donutCircumference;

                                                        $dashOffset =
                                                            -(
                                                                $categoryOffset
                                                                / 100
                                                            )
                                                            * $donutCircumference;

                                                        $color =
                                                            $categoryColors[
                                                                $index
                                                                % count($categoryColors)
                                                            ];

                                                        $categoryOffset += $percentage;

                                                    @endphp

                                                    <circle
                                                        cx="110"
                                                        cy="110"
                                                        r="{{ $donutRadius }}"
                                                        fill="none"
                                                        stroke="{{ $color }}"
                                                        stroke-width="30"
                                                        stroke-dasharray="{{ $segmentLength }} {{ $donutCircumference }}"
                                                        stroke-dashoffset="{{ $dashOffset }}"
                                                        transform="rotate(-90 110 110)"
                                                    />

                                                @endforeach


                                                <circle
                                                    cx="110"
                                                    cy="110"
                                                    r="58"
                                                    fill="white"
                                                />

                                                <text
                                                    x="110"
                                                    y="105"
                                                    text-anchor="middle"
                                                    class="fill-gray-400 text-[10px] font-medium"
                                                >
                                                    TOTAL
                                                </text>

                                                <text
                                                    x="110"
                                                    y="123"
                                                    text-anchor="middle"
                                                    class="fill-gray-900 text-[14px] font-bold"
                                                >
                                                    {{ $formatChartValue($categoryTotal) }}
                                                </text>

                                            </svg>

                                        </div>

                                    </div>


                                    {{-- CATEGORY LEGEND --}}

                                    <div class="min-w-0">

                                        <div class="space-y-3">

                                            @foreach($categoryData as $index => $category)

                                                @php

                                                    $color =
                                                        $categoryColors[
                                                            $index
                                                            % count($categoryColors)
                                                        ];

                                                    $value =
                                                        (float) $category['purchase_value'];

                                                    $percentage =
                                                        $categoryTotal > 0
                                                            ? (
                                                                $value
                                                                / $categoryTotal
                                                            ) * 100
                                                            : 0;

                                                @endphp

                                                <div class="flex items-center gap-3">

                                                    <span
                                                        class="h-3 w-3 flex-shrink-0 rounded-full"
                                                        style="background-color: {{ $color }}"
                                                    ></span>

                                                    <div class="min-w-0 flex-1">

                                                        <div
                                                            class="truncate text-sm font-medium text-gray-700"
                                                        >
                                                            {{ $category['category'] }}
                                                        </div>

                                                    </div>

                                                    <div
                                                        class="whitespace-nowrap text-sm font-semibold text-gray-900"
                                                    >
                                                        {{ $formatChartValue($value) }}
                                                    </div>

                                                    <div
                                                        class="w-12 text-right text-sm font-semibold"
                                                        style="color: {{ $color }}"
                                                    >
                                                        {{ number_format($percentage, 0) }}%
                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>


                                        {{-- TOTAL --}}

                                        <div class="mt-5 border-t border-gray-200 pt-4">

                                            <div class="flex items-center justify-between">

                                                <span class="text-sm font-semibold text-gray-900">
                                                    Total
                                                </span>

                                                <div class="flex items-center gap-4">

                                                    <span class="text-sm font-bold text-gray-900">
                                                        {{ $formatChartValue($categoryTotal) }}
                                                    </span>

                                                    <span class="text-sm font-medium text-gray-400">
                                                        100%
                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @else

                                <div class="flex min-h-[320px] items-center justify-center">

                                    <div class="text-center">

                                        <div
                                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100"
                                        >

                                            <x-filament::icon
                                                icon="heroicon-o-chart-pie"
                                                class="h-6 w-6 text-gray-400"
                                            />

                                        </div>

                                        <div class="mt-3 text-sm font-medium text-gray-500">
                                            No category purchase data
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            No approved purchase orders found for the selected filters.
                                        </div>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>
            {{-- END ROW 1 --}}



            {{-- ========================================================= --}}
            {{-- ROW 2 --}}
            {{-- TOP 5 SUPPLIERS + SPEND BY DEPARTMENT --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">


                {{-- ===================================================== --}}
                {{-- TOP 5 SUPPLIERS --}}
                {{-- SAME WIDTH AS PURCHASE VALUE TREND --}}
                {{-- ===================================================== --}}

                <div class="min-w-0">

                    <div class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        {{-- HEADER --}}

                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

                            <div class="min-w-0">

                                <h3 class="truncate text-lg font-semibold text-gray-900">

                                    Top 5 Suppliers

                                    <span class="font-normal text-gray-500">
                                        (by Purchase Value)
                                    </span>

                                </h3>

                                <p class="mt-0.5 text-xs text-gray-500">
                                    Approved purchase orders
                                </p>

                            </div>


                            {{-- PERIOD --}}

                            <div
                                class="flex flex-shrink-0 items-center gap-2 rounded-xl border border-gray-200
                                       bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm"
                            >

                                <span>
                                    This Period
                                </span>

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-3.5 w-3.5 text-gray-400"
                                />

                            </div>

                        </div>


                        {{-- SUPPLIER DATA --}}

                        <div class="px-6 pb-6 pt-4">

                            @php
                                $topSuppliers = $this->topPurchaseSuppliers;
                            @endphp

                            @if(count($topSuppliers))

                                {{-- TABLE HEADER --}}

                                <div
                                    class="grid grid-cols-[minmax(0,1.6fr)_70px_minmax(100px,1fr)_110px]
                                           items-center gap-3 border-b border-gray-100
                                           px-1 pb-2 text-[10px] font-semibold uppercase
                                           tracking-wide text-gray-400"
                                >

                                    <div>
                                        Supplier
                                    </div>

                                    <div class="text-right">
                                        PO Count
                                    </div>

                                    <div class="text-right">
                                        Purchase Value
                                    </div>

                                    <div class="text-right">
                                        On-Time Delivery
                                    </div>

                                </div>


                                {{-- SUPPLIER ROWS --}}

                                <div class="divide-y divide-gray-100">

                                    @foreach($topSuppliers as $supplier)

                                        @php

                                            $otd = $supplier['on_time_delivery'];

                                            if ($otd === null) {

                                                $otdText = '—';
                                                $otdWidth = 0;
                                                $otdColor = 'bg-gray-300';
                                                $otdTextColor = 'text-gray-400';

                                            } elseif ($otd >= 80) {

                                                $otdText = $otd . '%';
                                                $otdWidth = $otd;
                                                $otdColor = 'bg-emerald-500';
                                                $otdTextColor = 'text-emerald-600';

                                            } elseif ($otd >= 60) {

                                                $otdText = $otd . '%';
                                                $otdWidth = $otd;
                                                $otdColor = 'bg-amber-500';
                                                $otdTextColor = 'text-amber-600';

                                            } else {

                                                $otdText = $otd . '%';
                                                $otdWidth = $otd;
                                                $otdColor = 'bg-rose-500';
                                                $otdTextColor = 'text-rose-600';

                                            }

                                        @endphp


                                        <div
                                            class="grid grid-cols-[minmax(0,1.6fr)_70px_minmax(100px,1fr)_110px]
                                                   items-center gap-3 px-1 py-3"
                                        >

                                            {{-- SUPPLIER --}}

                                            <div class="min-w-0">

                                                <div
                                                    class="truncate text-sm font-medium text-gray-800"
                                                    title="{{ $supplier['supplier_name'] }}"
                                                >
                                                    {{ $supplier['supplier_name'] }}
                                                </div>

                                            </div>


                                            {{-- PO COUNT --}}

                                            <div class="text-right text-xs font-medium text-gray-600">

                                                {{ number_format($supplier['po_count']) }}

                                            </div>


                                            {{-- PURCHASE VALUE --}}

                                            <div class="text-right">

                                                <div class="text-xs font-semibold text-gray-800">

                                                    Rp {{ number_format(
                                                        $supplier['purchase_value'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }}

                                                </div>

                                            </div>


                                            {{-- ON-TIME DELIVERY --}}

                                            <div>

                                                <div class="flex items-center justify-end gap-2">

                                                    <span
                                                        class="text-xs font-semibold {{ $otdTextColor }}"
                                                    >
                                                        {{ $otdText }}
                                                    </span>

                                                    <div
                                                        class="h-1.5 w-14 overflow-hidden rounded-full bg-gray-100"
                                                    >

                                                        <div
                                                            class="h-full rounded-full {{ $otdColor }} transition-all duration-500"
                                                            style="width: {{ $otdWidth }}%"
                                                        ></div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>


                            @else

                                {{-- EMPTY STATE --}}

                                <div class="flex min-h-[280px] items-center justify-center">

                                    <div class="text-center">

                                        <div class="text-sm font-medium text-gray-500">
                                            No supplier data
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            No approved purchase orders found.
                                        </div>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- SPEND BY DEPARTMENT --}}
                {{-- ===================================================== --}}

                <div class="min-w-0">

                    <div class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        {{-- HEADER --}}

                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">

                            <div class="min-w-0">

                                <h3 class="text-lg font-semibold text-gray-900">

                                    Spend by Department

                                    <span class="font-normal text-gray-500">
                                        (MTD)
                                    </span>

                                </h3>

                                <p class="mt-0.5 text-xs text-gray-500">
                                    Approved purchase value
                                </p>

                            </div>


                            {{-- PERIOD --}}

                            <div
                                class="flex flex-shrink-0 items-center gap-1.5 rounded-xl border border-gray-200
                                       bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm"
                            >

                                <span>
                                    This Month
                                </span>

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-3.5 w-3.5 text-gray-400"
                                />

                            </div>

                        </div>


                        {{-- DEPARTMENT SPEND --}}

                        <div class="px-6 pb-5 pt-4">

                            @php

                                $departmentSpend = $this->spendByDepartment;

                                $departmentMax = collect($departmentSpend)
                                    ->max('purchase_value') ?? 0;

                                $departmentMax = max(
                                    (float) $departmentMax,
                                    1
                                );

                            @endphp


                            @if(count($departmentSpend))

                                <div class="space-y-5">

                                    @foreach($departmentSpend as $department)

                                        @php

                                            $value = (float) $department['purchase_value'];

                                            $barWidth = (
                                                $value / $departmentMax
                                            ) * 100;

                                        @endphp


                                        <div>

                                            {{-- LABEL / VALUE / PERCENTAGE --}}

                                            <div
                                                class="mb-2 grid grid-cols-[minmax(100px,1fr)_110px_45px]
                                                       items-center gap-3"
                                            >

                                                <div
                                                    class="truncate text-xs font-medium text-gray-700"
                                                    title="{{ $department['department_name'] }}"
                                                >
                                                    {{ $department['department_name'] }}
                                                </div>


                                                <div
                                                    class="text-right text-xs font-semibold text-gray-800"
                                                >
                                                    Rp {{ number_format(
                                                        $value,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }}
                                                </div>


                                                <div
                                                    class="text-right text-xs font-semibold text-indigo-600"
                                                >
                                                    {{ number_format(
                                                        $department['percentage'],
                                                        0
                                                    ) }}%
                                                </div>

                                            </div>


                                            {{-- PROGRESS BAR --}}

                                            <div
                                                class="h-2 overflow-hidden rounded-full bg-gray-100"
                                            >

                                                <div
                                                    class="h-full rounded-full
                                                           bg-gradient-to-r
                                                           from-indigo-500 to-violet-500
                                                           transition-all duration-500"
                                                    style="width: {{ $barWidth }}%"
                                                ></div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>


                            @else

                                {{-- EMPTY STATE --}}

                                <div class="flex min-h-[190px] items-center justify-center">

                                    <div class="text-center">

                                        <div class="text-sm font-medium text-gray-500">
                                            No department data
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            No approved purchase orders found.
                                        </div>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>
            {{-- END ROW 2 --}}

        </div>
        {{-- END PURCHASING ANALYTICS --}}


    </div>    

</x-filament-panels::page>