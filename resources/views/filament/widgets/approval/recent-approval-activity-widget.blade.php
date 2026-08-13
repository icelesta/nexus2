<x-filament-widgets::widget>
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-800">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                    <x-filament::icon
                        icon="heroicon-o-clock"
                        class="h-5 w-5"
                    />
                </div>

                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                        Recent Approval Activity
                    </h2>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Latest approval workflow activity
                    </p>
                </div>

            </div>

            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Last 10 activities
            </div>

        </div>


        {{-- =========================================================
            CONTENT
        ========================================================== --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800">

            @forelse ($activities as $activity)

                @php
                    $status = $activity->status;
                    $statusLabel = $this->getStatusLabel($status);
                    $statusColor = $this->getStatusColor($status);
                    $statusIcon = $this->getStatusIcon($status);

                    $documentUrl = $this->getDocumentUrl(
                        $activity->document_type,
                        (int) $activity->document_id
                    );
                @endphp


                <div class="group flex items-center gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-white/5">

                    {{-- =================================================
                        STATUS ICON
                    ================================================== --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                        bg-{{ $statusColor }}-50
                        text-{{ $statusColor }}-600
                        dark:bg-{{ $statusColor }}-500/10
                        dark:text-{{ $statusColor }}-400">

                        <x-filament::icon
                            :icon="$statusIcon"
                            class="h-5 w-5"
                        />

                    </div>


                    {{-- =================================================
                        DOCUMENT
                    ================================================== --}}
                    <div class="min-w-0 flex-1">

                        @if ($documentUrl)

                            <a
                                href="{{ $documentUrl }}"
                                class="block text-sm font-semibold text-gray-950 transition hover:text-primary-600 dark:text-white dark:hover:text-primary-400"
                            >
                                {{ $activity->document_no }}
                            </a>

                        @else

                            <div class="text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $activity->document_no }}
                            </div>

                        @endif


                        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">

                            <span>
                                {{ str_replace('_', ' ', $activity->document_type) }}
                            </span>

                            @if ($activity->completed_at)

                                <span>•</span>

                                <span>
                                    {{ $activity->completed_at->format('d M Y H:i') }}
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- =================================================
                        STATUS
                    ================================================== --}}
                    <div class="shrink-0">

                        <span
                            @class([
                                'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $statusColor === 'success',
                                'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' => $statusColor === 'danger',
                                'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' => $statusColor === 'warning',
                                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $statusColor === 'gray',
                            ])
                        >
                            {{ $statusLabel }}
                        </span>

                    </div>


                    {{-- =================================================
                        ACTION
                    ================================================== --}}
                    @if ($documentUrl)

                        <div class="shrink-0 opacity-0 transition group-hover:opacity-100">

                            <a
                                href="{{ $documentUrl }}"
                                class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400"
                            >
                                View

                                <x-filament::icon
                                    icon="heroicon-m-arrow-top-right-on-square"
                                    class="h-3.5 w-3.5"
                                />
                            </a>

                        </div>

                    @endif

                </div>

            @empty

                {{-- =================================================
                    EMPTY STATE
                ================================================== --}}
                <div class="flex flex-col items-center justify-center px-5 py-12 text-center">

                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">

                        <x-filament::icon
                            icon="heroicon-o-clipboard-document-list"
                            class="h-6 w-6"
                        />

                    </div>

                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        No Recent Approval Activity
                    </h3>

                    <p class="mt-1 max-w-md text-xs text-gray-500 dark:text-gray-400">
                        Approval activity will appear here when documents
                        move through the approval workflow.
                    </p>

                </div>

            @endforelse

        </div>

    </div>
</x-filament-widgets::widget>