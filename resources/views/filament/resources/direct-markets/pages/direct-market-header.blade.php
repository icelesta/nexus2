@php

    $status = strtoupper(
        (string) ($record?->status ?? 'DRAFT')
    );

    $statusLabel = match ($status) {

        'APPROVED' => 'APPROVED',

        'SUBMITTED' => 'SUBMITTED',

        'REJECTED' => 'REJECTED',

        'CANCELLED' => 'CANCELLED',

        'CLOSED' => 'CLOSED',

        default => 'DRAFT',

    };

@endphp


<div class="rounded-xl border border-gray-900 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">

    <div class="flex items-center justify-between gap-6">

        <div>

            <div class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Direct Market
            </div>

            <div class="mt-1 text-base font-semibold text-gray-600 dark:text-gray-300">
                {{ $record?->dm_no ?? '-' }}
            </div>

            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                View Direct Market information, requested items, and approval status.
            </div>

        </div>


        <div class="
            inline-flex
            items-center
            rounded-xl
            border
            px-6
            py-4
            text-xl
            font-extrabold
            tracking-wide
            whitespace-nowrap
            {{
                match ($status) {
                    'APPROVED' =>
                        'border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300',

                    'SUBMITTED' =>
                        'border-yellow-300 bg-yellow-50 text-yellow-700 dark:border-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',

                    'REJECTED', 'CANCELLED' =>
                        'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300',

                    'CLOSED' =>
                        'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-300',

                    default =>
                        'border-gray-300 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
                }
            }}
        >

            <span class="mr-3 text-2xl">
                📄
            </span>

            {{ $statusLabel }}

        </div>

    </div>

</div>