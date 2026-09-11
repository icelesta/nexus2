@if ($transaction && $transaction->steps->isNotEmpty())

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

        {{-- Header --}}
        <div class="border-b border-gray-200 px-5 py-3 dark:border-gray-700">

            <div class="text-base font-bold text-gray-900 dark:text-white">
                Approval History
            </div>

            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Approval activity and decision history for this Assignment Direct Market.
            </div>

        </div>


        {{-- Approval Steps --}}
        <div class="divide-y divide-gray-200 dark:divide-gray-700">

            @foreach ($transaction->steps as $step)

                <div class="px-5 py-3">

                    <div class="flex items-center gap-4">

                        {{-- Status Icon --}}
                        <div
                            class="
                                flex h-8 w-8 shrink-0 items-center justify-center
                                rounded-full
                                {{
                                    $step->status === 'APPROVED'
                                        ? 'bg-green-100 text-green-600'
                                        : ($step->status === 'REJECTED'
                                            ? 'bg-red-100 text-red-600'
                                            : 'bg-gray-100 text-gray-500')
                                }}
                            "
                        >

                            @if ($step->status === 'REJECTED')

                                <x-heroicon-o-x-mark
                                    class="h-4 w-4"
                                />

                            @elseif ($step->status === 'APPROVED')

                                <x-heroicon-o-check
                                    class="h-4 w-4"
                                />

                            @else

                                <x-heroicon-o-clock
                                    class="h-4 w-4"
                                />

                            @endif

                        </div>


                        {{-- Level / Role --}}
                        <div class="w-52 shrink-0 border-r border-gray-200 pr-4 dark:border-gray-700">

                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                Approval Level {{ $step->approval_level }}
                            </div>

                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ $step->role_name ?? '-' }}
                            </div>

                        </div>


                        {{-- Date --}}
                        <div class="w-48 shrink-0 border-r border-gray-200 pr-4 dark:border-gray-700">

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Date
                            </div>

                            <div class="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $step->acted_at?->format('d M Y H:i') ?? '-' }}
                            </div>

                        </div>


                        {{-- Approver --}}
                        <div class="w-48 shrink-0 border-r border-gray-200 pr-4 dark:border-gray-700">

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                By
                            </div>

                            <div class="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $step->approver?->name ?? '-' }}
                            </div>

                        </div>


                        {{-- Remarks --}}
                        <div class="min-w-0 flex-1">

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Remarks
                            </div>

                            <div
                                class="mt-0.5 truncate text-sm font-medium text-gray-900 dark:text-white"
                                title="{{ $step->remarks ?: '-' }}"
                            >
                                {{ $step->remarks ?: '-' }}
                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="shrink-0">

                            <div
                                class="
                                    rounded-full px-3 py-1
                                    text-xs font-semibold
                                    {{
                                        $step->status === 'APPROVED'
                                            ? 'bg-green-100 text-green-700'
                                            : ($step->status === 'REJECTED'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-gray-100 text-gray-600')
                                    }}
                                "
                            >
                                {{ $step->status }}
                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endif