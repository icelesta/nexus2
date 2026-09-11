@if ($rejectionStep)

    <div
        class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm"
    >
        <div class="flex gap-4">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                <x-heroicon-o-exclamation-triangle
                    class="h-6 w-6 text-red-600"
                />
            </div>

            <div class="min-w-0 flex-1">

                <h3 class="text-sm font-semibold text-red-800">
                    Assignment Direct Market Rejected
                </h3>

                <div class="mt-4">

                    <div class="text-xs font-semibold uppercase tracking-wide text-red-700">
                        Rejection Remarks
                    </div>

                    <div class="mt-1 rounded-lg border border-red-200 bg-white px-4 py-3 text-sm text-gray-800">
                        {{ $rejectionStep->remarks ?: '-' }}
                    </div>

                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-red-700">
                            Rejected By
                        </div>

                        <div class="mt-1 text-sm text-gray-800">
                            {{ $rejectionStep->approver?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-red-700">
                            Rejected At
                        </div>

                        <div class="mt-1 text-sm text-gray-800">
                            {{ $rejectionStep->acted_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

@endif