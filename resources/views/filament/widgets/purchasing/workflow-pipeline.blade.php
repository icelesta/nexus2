<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Purchasing Workflow Pipeline
        </x-slot>

        <x-slot name="description">
            Material Requisition → Assignment → Purchase Order → Goods Receipt → Vendor Invoice → Payment
        </x-slot>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">

            @foreach ($this->getWorkflowStages() as $stage)

                <div
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">

                    <div class="flex items-center justify-between">

                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                {{ $stage['code'] }}
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $stage['title'] }}
                            </div>
                        </div>

                        <x-filament::icon
                            :icon="$stage['icon']"
                            class="h-7 w-7 text-primary-600"
                        />

                    </div>

                    <div class="mt-6 text-3xl font-bold text-primary-600">
                        {{ number_format($stage['count']) }}
                    </div>

                    <div class="mt-2">
                        <span
                            class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 dark:bg-primary-900/20 dark:text-primary-300">
                            {{ $stage['status'] }}
                        </span>
                    </div>

                </div>

            @endforeach

        </div>
    </x-filament::section>
</x-filament-widgets::widget>