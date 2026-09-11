@if ($rejectionStep)
    <div class="rounded-xl border border-red-300 bg-red-50 p-5 dark:border-red-700 dark:bg-red-900/20">

        <div class="flex items-start gap-3">

            <div class="mt-0.5 text-xl text-red-600">
                ✕
            </div>

            <div class="flex-1">

                <div class="text-lg font-bold text-red-800 dark:text-red-200">
                    Direct Market Rejected
                </div>

                <div class="mt-1 text-sm text-red-700 dark:text-red-300">
                    This Direct Market has been rejected at Approval Level {{ $rejectionStep->approval_level }}.
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-3">

                    <div>
                        <div class="text-xs font-medium text-red-600 dark:text-red-300">
                            Rejection Remarks
                        </div>

                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $rejectionStep->remarks ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium text-red-600 dark:text-red-300">
                            Rejected By
                        </div>

                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $rejectionStep->approver?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium text-red-600 dark:text-red-300">
                            Rejected At
                        </div>

                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $rejectionStep->acted_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endif