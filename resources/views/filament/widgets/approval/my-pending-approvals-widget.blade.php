<x-filament-widgets::widget>
    <x-filament::section>

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="flex items-center justify-between gap-4">

            <div>

                <h2
                    class="text-base font-semibold text-gray-950 dark:text-white"
                >
                    My Pending Approvals
                </h2>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Documents currently waiting for your approval.
                </p>

            </div>

            <div
                class="flex items-center gap-2"
            >

                <x-filament::badge
                    :color="$approvals->count() > 0 ? 'warning' : 'gray'"
                >
                    {{ $approvals->count() }}
                </x-filament::badge>

            </div>

        </div>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        @if ($approvals->isEmpty())

            {{-- =================================================
                 EMPTY STATE
            ================================================== --}}

            <div
                class="mt-6 flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-900/40"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400"
                >
                    <x-heroicon-o-check-circle
                        class="h-7 w-7"
                    />
                </div>

                <h3
                    class="mt-4 text-sm font-semibold text-gray-950 dark:text-white"
                >
                    No Pending Approvals
                </h3>

                <p
                    class="mt-1 max-w-md text-sm text-gray-500 dark:text-gray-400"
                >
                    You currently have no documents waiting for your approval.
                </p>

            </div>

        @else

            {{-- =================================================
                 APPROVAL LIST
            ================================================== --}}

            <div
                class="mt-6 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
            >

                <div
                    class="divide-y divide-gray-200 dark:divide-gray-700"
                >

                    @foreach ($approvals as $transaction)

                        <div
                            class="group flex flex-col gap-4 px-5 py-4 transition hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-gray-800/50"
                        >

                            {{-- =================================
                                 DOCUMENT INFORMATION
                            ================================== --}}

                            <div
                                class="min-w-0"
                            >

                                <div
                                    class="flex flex-wrap items-center gap-2"
                                >

                                    <span
                                        class="text-sm font-semibold text-gray-950 dark:text-white"
                                    >
                                        {{ $transaction->document_no }}
                                    </span>

                                    <x-filament::badge
                                        color="warning"
                                    >
                                        {{ $this->getCurrentLevel($transaction) }}
                                    </x-filament::badge>

                                    <x-filament::badge
                                        color="gray"
                                    >
                                        {{ $this->getCurrentRole($transaction) }}
                                    </x-filament::badge>

                                </div>

                                <div
                                    class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400"
                                >

                                    <span>
                                        {{ $transaction->document_type }}
                                    </span>

                                    @if ($transaction->submitted_at)

                                        <span>
                                            Submitted
                                            {{ \App\Support\Timezone\UserTimezone::format(
                                                $transaction->submitted_at,
                                                'd M Y H:i'
                                            ) }}
                                        </span>

                                    @endif

                                </div>

                            </div>


                            {{-- =================================
                                 ACTION
                            ================================== --}}

                            <div
                                class="flex shrink-0 items-center"
                            >

                                <x-filament::button
                                    tag="a"
                                    size="sm"
                                    color="primary"
                                    icon="heroicon-m-arrow-top-right-on-square"
                                    :href="$this->getDocumentUrl(
                                        (int) $transaction->document_id
                                    )"
                                >
                                    View & Approve
                                </x-filament::button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    </x-filament::section>
</x-filament-widgets::widget>