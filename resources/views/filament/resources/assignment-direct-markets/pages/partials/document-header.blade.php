<div
    class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6 dark:border-gray-700 dark:bg-gray-900"
>

    <div class="px-6 py-5">

        <div class="flex items-start justify-between gap-6">

            {{-- =====================================================
                 DOCUMENT INFORMATION
            ====================================================== --}}

            <div class="min-w-0">

                <div
                    class="mt-2 text-3xl font-bold tracking-wide text-primary-600"
                >
                    {{ $record->document_no }}
                </div>

                <div
                    class="mt-3 text-sm text-gray-500 dark:text-gray-400"
                >
                    Document Date :

                    <span
                        class="font-medium text-gray-700 dark:text-gray-200"
                    >
                        {{ optional($record->document_date)->format('d M Y') }}
                    </span>
                </div>

                {{-- =================================================
                     DIRECT MARKET REFERENCE
                ================================================== --}}

                @if ($record->directMarket)

                    <div
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Direct Market :

                        <span
                            class="font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $record->directMarket->dm_no }}
                        </span>
                    </div>

                @endif

            </div>


            {{-- =====================================================
                 STATUS
            ====================================================== --}}

            <div class="shrink-0">

                <x-filament::badge
                    :color="match (strtolower((string) $record->status) ) {

                        'draft' =>
                            'warning',

                        'updated' =>
                            'info',

                        'submitted' =>
                            'info',

                        'waiting approval' =>
                            'warning',

                        'approved' =>
                            'success',

                        'completed' =>
                            'success',

                        'rejected' =>
                            'danger',

                        'cancelled' =>
                            'danger',

                        default =>
                            'gray',
                    }"
                >

                    {{ strtoupper((string) $record->status) }}

                </x-filament::badge>

            </div>

        </div>

    </div>

</div>