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
                    :color="$pendingCount > 0 ? 'warning' : 'gray'"
                >
                    {{ $pendingCount }}
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
                                        $transaction
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

        {{-- =====================================================
             PAGINATION
        ====================================================== --}}
        <div class="mt-4 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">

            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span>Show</span>

                <select
                    wire:model.live="perPage"
                    class="rounded-lg border-gray-300 bg-white py-1.5 pl-2 pr-8 text-xs shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="all">All</option>
                </select>

                <span>records</span>
            </div>

            @if ($approvals instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $approvals->lastPage() > 1)
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        wire:click="$set('pendingPage', {{ max(1, $approvals->currentPage() - 1) }})"
                        @disabled($approvals->onFirstPage())
                        class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >Previous</button>

                    @for ($page = max(1, $approvals->currentPage() - 1); $page <= min($approvals->lastPage(), $approvals->currentPage() + 1); $page++)
                        <button
                            type="button"
                            wire:click="$set('pendingPage', {{ $page }})"
                            class="rounded-lg border px-2.5 py-1.5 text-xs font-medium transition {{ $page === $approvals->currentPage() ? 'border-primary-500 bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >{{ $page }}</button>
                    @endfor

                    <button
                        type="button"
                        wire:click="$set('pendingPage', {{ min($approvals->lastPage(), $approvals->currentPage() + 1) }})"
                        @disabled($approvals->currentPage() >= $approvals->lastPage())
                        class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >Next</button>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>