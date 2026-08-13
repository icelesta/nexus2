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
                            152
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
                            118
                        </div>

                        <div class="mt-2 text-sm text-amber-600">
                            Buyer Assignment
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
                            104
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
                            21
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
                            Rp 18 B
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
                            5
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
{{-- PART 1 : ENTERPRISE FOUNDATION --}}
{{-- ========================================================= --}}

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="border-b border-gray-100 px-6 py-5">

        <h3 class="text-lg font-semibold text-gray-900">
            Purchasing Workflow Pipeline
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            End-to-end purchasing document flow
        </p>

    </div>

    {{-- Body --}}
    <div class="overflow-x-auto">

        @php

            $pipeline = [

                [
                    'code'=>'MR',
                    'title'=>'Material Requisition',
                    'count'=>152,
                    'status'=>'Open',
                    'icon'=>'heroicon-o-document-text',

                    'codeBg'      => 'bg-blue-100',
                    'codeColor'   => 'text-blue-700',

                    'iconBg'      => 'bg-gradient-to-br from-blue-50 to-blue-200',
                    'iconColor'   => 'text-blue-600',

                    'countColor'  => 'text-blue-700',

                    'statusBg'    => 'bg-blue-50',
                    'statusColor' => 'text-blue-700',

                    'dotColor'    => 'bg-blue-500',                    
                ],

                [
                    'code'=>'AMR',
                    'title'=>'Assignment',
                    'count'=>118,
                    'status'=>'In Progress',
                    'icon'=>'heroicon-o-user-plus',

                    'codeBg'=>'bg-amber-100',
                    'codeColor'=>'text-amber-700',

                    'iconBg'=>'bg-gradient-to-br from-amber-50 to-amber-200',
                    'iconColor'=>'text-amber-600',

                    'countColor'=>'text-amber-700',

                    'statusBg'=>'bg-amber-50',
                    'statusColor'=>'text-amber-700',

                    'dotColor'=>'bg-amber-500',

                ],

                [
                    'code'=>'PO',
                    'title'=>'Purchase Order',
                    'count'=>104,
                    'status'=>'Generated',
                    'icon'=>'heroicon-o-clipboard-document-list',

                    'codeBg'=>'bg-emerald-100',
                    'codeColor'=>'text-emerald-700',

                    'iconBg'=>'bg-gradient-to-br from-emerald-50 to-emerald-200',
                    'iconColor'=>'text-emerald-600',

                    'countColor'=>'text-emerald-700',

                    'statusBg'=>'bg-emerald-50',
                    'statusColor'=>'text-emerald-700',

                    'dotColor'=>'bg-emerald-500',

                ],

                [
                    'code'=>'GR',
                    'title'=>'Goods Receipt',
                    'count'=>21,
                    'status'=>'Receiving',
                    'icon'=>'heroicon-o-archive-box',

                    'codeBg'=>'bg-rose-100',
                    'codeColor'=>'text-rose-700',

                    'iconBg'=>'bg-gradient-to-br from-rose-50 to-rose-200',
                    'iconColor'=>'text-rose-600',

                    'countColor'=>'text-rose-700',

                    'statusBg'=>'bg-rose-50',
                    'statusColor'=>'text-rose-700',

                    'dotColor'=>'bg-rose-500',

                ],

                [
                    'code'=>'INV',
                    'title'=>'Vendor Invoice',
                    'count'=>8,
                    'status'=>'Pending',
                    'icon'=>'heroicon-o-document-currency-dollar',
                    'codeBg'=>'bg-indigo-100',
                    'codeColor'=>'text-indigo-700',

                    'iconBg'=>'bg-gradient-to-br from-indigo-50 to-indigo-200',
                    'iconColor'=>'text-indigo-600',

                    'countColor'=>'text-indigo-700',

                    'statusBg'=>'bg-indigo-50',
                    'statusColor'=>'text-indigo-700',

                    'dotColor'=>'bg-indigo-500',

                ],

                [
                    'code'=>'PAY',
                    'title'=>'Payment',
                    'count'=>5,
                    'status'=>'Completed',
                    'icon'=>'heroicon-o-banknotes',

                    'codeBg'=>'bg-green-100',
                    'codeColor'=>'text-green-700',

                    'iconBg'=>'bg-gradient-to-br from-green-50 to-green-200',
                    'iconColor'=>'text-green-600',

                    'countColor'=>'text-green-700',

                    'statusBg'=>'bg-green-50',
                    'statusColor'=>'text-green-700',

                    'dotColor'=>'bg-green-500',                    
                ],

            ];

        @endphp

        <div class="flex min-w-max items-center gap-2 px-6 py-8">

            @foreach($pipeline as $stage)

                {{-- ================= CARD ================= --}}

                <div class="relative w-64 flex-shrink-0">

                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">

                        {{-- Header --}}

                        <div class="flex items-start justify-between">

                            <div>

                                <div class="text-xs font-bold tracking-[0.25em] text-gray-500">

                                    {{ $stage['code'] }}

                                </div>

                                <div class="mt-2 text-lg font-semibold text-gray-900">

                                    {{ $stage['title'] }}

                                </div>

                            </div>

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">

                                <x-filament::icon
                                    :icon="$stage['icon']"
                                    class="h-7 w-7 text-gray-600"/>

                            </div>

                        </div>

                        {{-- Count --}}

                        <div class="mt-10">

                            <div class="text-5xl font-bold text-gray-900">

                                {{ number_format($stage['count']) }}

                            </div>

                        </div>

                        {{-- Status --}}

                        <div class="mt-8 flex items-center gap-2">

                            <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>

                            <span class="text-sm font-medium text-gray-600">

                                {{ $stage['status'] }}

                            </span>

                        </div>

                        {{-- Progress Placeholder --}}

                        <div class="absolute right-5 top-24 flex flex-col gap-2">

                            @for($i=0;$i<6;$i++)

                                <span class="h-1.5 w-1.5 rounded-full bg-gray-300"></span>

                            @endfor

                        </div>

                    </div>

                </div>

                {{-- ================= CONNECTOR ================= --}}

                @if(!$loop->last)

                    <div class="flex items-center justify-center">

                        <div class="flex items-center">

                            <div class="h-[2px] w-10 {{ $stage['dotColor'] }} opacity-30"></div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 bg-white shadow-sm">

                                <x-filament::icon
                                    icon="heroicon-m-chevron-right"
                                    class="h-5 w-5 text-gray-500"/>

                            </div>

                            <div class="h-[2px] w-10 {{ $stage['dotColor'] }} opacity-30"></div>

                        </div>

                    </div>

                @endif

            @endforeach

        </div>

    </div>

</div>






        {{-- ========================================================= --}}
        {{-- Recent Purchase Orders --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">

                    <div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            Recent Purchase Orders
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Latest purchasing transactions
                        </p>

                    </div>

                    <button
                        class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-100">

                        View All →

                    </button>

                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    PO Number
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Vendor
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Amount
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Delivery
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            <tr class="cursor-pointer transition hover:bg-blue-50">

                                <td class="px-6 py-4 font-medium text-blue-600">
                                    PO25070001
                                </td>

                                <td class="px-6 py-4">
                                    ABC Steel
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    Rp 21.4 B
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Draft
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-center">
                                    28 Jul
                                </td>

                            </tr>

                            <tr class="cursor-pointer transition hover:bg-blue-50">

                                <td class="px-6 py-4 font-medium text-blue-600">
                                    PO25070002
                                </td>

                                <td class="px-6 py-4">
                                    Sigma Pipe
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    Rp 8.2 B
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Approved
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-center">
                                    30 Jul
                                </td>

                            </tr>

                            <tr class="cursor-pointer transition hover:bg-blue-50">

                                <td class="px-6 py-4 font-medium text-blue-600">
                                    PO25070003
                                </td>

                                <td class="px-6 py-4">
                                    Indotech
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    Rp 2.1 B
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Open
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-center">
                                    02 Aug
                                </td>

                            </tr>

                            <tr class="cursor-pointer transition hover:bg-blue-50">

                                <td class="px-6 py-4 font-medium text-blue-600">
                                    PO25070004
                                </td>

                                <td class="px-6 py-4">
                                    Sinar Baja
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    Rp 5.4 B
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Draft
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-center">
                                    04 Aug
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

    </div>

    {{-- ========================================================= --}}
    {{-- OPERATIONAL WIDGETS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- =============================================== --}}
        {{-- Pending Approval --}}
        {{-- =============================================== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-6 py-4">

                <h3 class="text-lg font-semibold text-gray-900">
                    Pending Approval
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Documents requiring immediate action
                </p>

            </div>

            <div class="divide-y divide-gray-100">

                @foreach ([
                    ['Material Requisition','4','bg-red-100 text-red-700'],
                    ['Purchase Order','2','bg-amber-100 text-amber-700'],
                    ['Goods Receipt','1','bg-blue-100 text-blue-700'],
                    ['Vendor Invoice','3','bg-indigo-100 text-indigo-700'],
                ] as $item)

                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">

                        <span class="font-medium text-gray-700">
                            {{ $item[0] }}
                        </span>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item[2] }}">
                            {{ $item[1] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

        {{-- =============================================== --}}
        {{-- Material Requisition Queue --}}
        {{-- =============================================== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-6 py-4">

                <h3 class="text-lg font-semibold text-gray-900">
                    Material Requisition Queue
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Waiting for buyer assignment
                </p>

            </div>

            <div class="divide-y divide-gray-100">

                @foreach ([
                    ['MR25070001','Valve Carbon Steel','High'],
                    ['MR25070002','Pipe SCH40','Medium'],
                    ['MR25070003','Electric Cable','High'],
                    ['MR25070004','Safety Helmet','Low'],
                ] as $mr)

                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">

                        <div>

                            <div class="font-semibold text-blue-600">
                                {{ $mr[0] }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $mr[1] }}
                            </div>

                        </div>

                        @php
                            $priority = match ($mr[2]) {
                                'High' => 'bg-red-100 text-red-700',
                                'Medium' => 'bg-amber-100 text-amber-700',
                                default => 'bg-green-100 text-green-700',
                            };
                        @endphp

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $priority }}">
                            {{ $mr[2] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

    </div>    

</x-filament-panels::page>