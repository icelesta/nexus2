@php
    use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
@endphp

<x-filament-panels::page>

    <div class="mx-auto max-w-6xl">

        {{-- ==========================================================
        | Toolbar
        ========================================================== --}}
        <div class="mb-6 flex justify-end gap-3">

            <x-filament::button
                color="gray"
                icon="heroicon-o-arrow-left"
                :href="PurchaseRequisitionResource::getUrl()">
                Back
            </x-filament::button>

            <x-filament::button
                color="primary"
                icon="heroicon-o-printer"
                x-on:click="window.print()">
                Print
            </x-filament::button>

            <x-filament::button
                color="success"
                icon="heroicon-o-document-arrow-down"
                disabled>
                Export PDF
            </x-filament::button>

        </div>

        {{-- ==========================================================
        | ERP DOCUMENT
        ========================================================== --}}
        <div class="rounded-xl border border-gray-300 bg-white shadow-sm overflow-hidden">

            {{-- Document Header --}}
            @include('filament.resources.purchase-requisitions.pages.partials.document-header')

            {{-- Document Information --}}
            @include('filament.resources.purchase-requisitions.pages.partials.document-information')

            {{-- Material Requisition Items --}}
            @include('filament.resources.purchase-requisitions.pages.partials.items-table')

            {{-- Document Summary --}}
            @include('filament.resources.purchase-requisitions.pages.partials.document-summary')

            {{-- Signatures --}}
            @include('filament.resources.purchase-requisitions.pages.partials.signatures')

        </div>

    </div>

    @push('styles')

    <style>

    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    @media print {

        html,
        body {
            background: white !important;
        }

        nav,
        aside,
        header,
        footer {
            display: none !important;
        }

        .fi-topbar,
        .fi-sidebar,
        .fi-header,
        .fi-breadcrumbs {
            display: none !important;
        }

        button,
        .fi-btn {
            display: none !important;
        }

        #mr-print-document {

            width: 100% !important;

            margin: 0 !important;

            border: none !important;

            box-shadow: none !important;

            border-radius: 0 !important;

        }

        * {

            -webkit-print-color-adjust: exact;

            print-color-adjust: exact;

        }

        table {

            page-break-inside: avoid;

        }

        tr {

            page-break-inside: avoid;

        }

    }

    </style>

    @endpush    

</x-filament-panels::page>