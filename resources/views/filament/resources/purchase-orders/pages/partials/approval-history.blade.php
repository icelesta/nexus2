@php
    use App\Models\ApprovalTransaction;
    use App\Models\User;

    $transaction = ApprovalTransaction::query()
        ->where('document_type', 'PURCHASE_ORDER')
        ->where('document_id', $record->getKey())
        ->latest('id')
        ->with('steps')
        ->first();

    $steps = $transaction?->steps
        ?->sortBy('approval_level')
        ->values();
@endphp

<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

    {{-- Header --}}
    <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">

        <div class="flex items-center gap-2.5">

            <div class="flex h-7 w-7 items-center justify-center rounded-md bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                <x-heroicon-o-document-duplicate class="h-4 w-4" />
            </div>

            <div>
                <div class="text-sm font-semibold text-gray-950 dark:text-white">
                    Approval History
                </div>

                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                    Approval activity and decision history
                </div>
            </div>

        </div>

    </div>

    @if (! $transaction)

        <div class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
            Approval has not been submitted yet.
        </div>

    @elseif ($steps->isEmpty())

        <div class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
            No approval steps available.
        </div>

    @else

        <div class="divide-y divide-gray-100 dark:divide-gray-800">

            @foreach ($steps as $step)

                @php
                    $status = strtoupper(
                        (string) ($step->status ?? 'PENDING')
                    );

                    $level = (int) $step->approval_level;

                    $roleName = filled($step->role_name)
                        ? $step->role_name
                        : 'Approver';

                    $approverName = '-';

                    if ($step->approved_by) {
                        $approverName = User::query()
                            ->whereKey($step->approved_by)
                            ->value('name') ?? '-';
                    } elseif ($status === 'PENDING') {
                        $approverName = 'Waiting for approval';
                    }

                    $statusLabel = match ($status) {
                        'APPROVED' => 'Approved',
                        'REJECTED' => 'Rejected',
                        'PENDING' => 'Pending',
                        default => ucfirst(strtolower($status)),
                    };

                    $statusClass = match ($status) {
                        'APPROVED'
                            => 'border-success-200 bg-success-50 text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400',

                        'REJECTED'
                            => 'border-danger-200 bg-danger-50 text-danger-700 dark:border-danger-800 dark:bg-danger-900/20 dark:text-danger-400',

                        default
                            => 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400',
                    };

                    $iconClass = match ($status) {
                        'APPROVED'
                            => 'text-success-600 dark:text-success-400',

                        'REJECTED'
                            => 'text-danger-600 dark:text-danger-400',

                        default
                            => 'text-gray-400',
                    };

                    $icon = match ($status) {
                        'APPROVED' => 'heroicon-o-check-circle',
                        'REJECTED' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-clock',
                    };

                    $actedAt = $step->acted_at
                        ? $step->acted_at
                            ->timezone(config('app.timezone'))
                            ->format('d M Y H:i')
                        : '-';
                @endphp

                <div class="px-4 py-3">

                    {{-- Main approval row --}}
                    <div class="flex items-start gap-3">

                        {{-- Status Icon --}}
                        <div class="mt-0.5 shrink-0">
                            <x-dynamic-component
                                :component="$icon"
                                class="h-4 w-4 {{ $iconClass }}"
                            />
                        </div>

                        {{-- Main information --}}
                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">

                                <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                    Approval Level {{ $level }}
                                </span>

                                <span class="text-xs text-gray-400">
                                    ·
                                </span>

                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $roleName }}
                                </span>

                            </div>

                            <div class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $approverName }}
                                <span class="mx-1">·</span>
                                {{ $actedAt }}
                            </div>

                        </div>

                        {{-- Status --}}
                        <div class="flex shrink-0 items-center gap-1.5">

                            <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>

                            @if ($step->action)

                                <span class="text-[10px] font-medium text-gray-400">
                                    {{ $step->action }}
                                </span>

                            @endif

                        </div>

                    </div>

                    {{-- Remarks --}}
                    @if (filled($step->remarks))

                        <div class="ml-7 mt-2 border-l-2 border-gray-200 pl-3 dark:border-gray-700">

                            <div class="text-[9px] font-semibold uppercase tracking-wide text-gray-400">
                                Remarks
                            </div>

                            <div class="mt-0.5 whitespace-pre-line text-xs text-gray-600 dark:text-gray-300">
                                {{ $step->remarks }}
                            </div>

                        </div>

                    @endif

                </div>

            @endforeach

        </div>

    @endif

</div>