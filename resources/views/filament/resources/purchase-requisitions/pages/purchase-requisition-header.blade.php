@php

    $status = strtoupper($record->status);

    $style = match ($record->status) {

        'Draft' => [
            'background' => 'bg-amber-100',
            'border'     => 'border-amber-300',
            'divider'    => 'border-amber-300',
            'text'       => 'text-amber-900',
        ],

        'Submitted' => [
            'background' => 'bg-sky-100',
            'border'     => 'border-sky-300',
            'divider'    => 'border-sky-300',
            'text'       => 'text-sky-900',
        ],

        'Approved' => [
            'background' => 'bg-green-100',
            'border'     => 'border-green-300',
            'divider'    => 'border-green-300',
            'text'       => 'text-green-900',
        ],

        'Rejected' => [
            'background' => 'bg-red-100',
            'border'     => 'border-red-300',
            'divider'    => 'border-red-300',
            'text'       => 'text-red-900',
        ],

        'Cancelled' => [
            'background' => 'bg-gray-200',
            'border'     => 'border-gray-400',
            'divider'    => 'border-gray-400',
            'text'       => 'text-gray-800',
        ],

        'Closed' => [
            'background' => 'bg-slate-200',
            'border'     => 'border-slate-400',
            'divider'    => 'border-slate-400',
            'text'       => 'text-slate-800',
        ],

        default => [
            'background' => 'bg-gray-100',
            'border'     => 'border-gray-300',
            'divider'    => 'border-gray-300',
            'text'       => 'text-gray-900',
        ],

    };

@endphp

<div class="rounded-xl border bg-white dark:bg-gray-900 shadow-sm p-8">

    <div class="flex justify-between items-start">

        {{-- LEFT ------------------------------------------------------------}}

        <div>

            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">

                Purchase Requisition

            </h1>

            <div class="mt-3 text-2xl font-semibold text-gray-500">

                {{ $record->pr_no }}

            </div>

            <p class="mt-4 text-sm text-gray-500">

                View purchase requisition information, requested items,
                approval status, and document history.

            </p>

        </div>

        {{-- RIGHT -----------------------------------------------------------}}

        <div
            class="flex items-center gap-6 rounded-2xl border
                   {{ $style['background'] }}
                   {{ $style['border'] }}
                   px-8 py-6 shadow">

            <div class="pr-6 border-r {{ $style['divider'] }}">

                <x-heroicon-o-document-text
                    class="w-14 h-14 {{ $style['text'] }}" />

            </div>

            <div>

                <div
                    class="text-5xl font-black tracking-wide
                           {{ $style['text'] }}">

                    {{ $status }}

                </div>

            </div>

        </div>

    </div>

</div>