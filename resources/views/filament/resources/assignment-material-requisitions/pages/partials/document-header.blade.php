<div
    class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6">

    <div class="px-6 py-5">

        <div class="flex items-start justify-between">

            <div>

<!--                 <div
                    class="text-xs uppercase tracking-widest text-gray-500">

                    Edit Assignment Material Requisition

                </div> -->

                <div
                    class="mt-2 text-3xl font-bold tracking-wide text-primary-600">

                    {{ $record->document_no }}

                </div>

                <div
                    class="mt-3 text-sm text-gray-500">

                    Document Date :

                    <span class="font-medium text-gray-700">

                        {{ optional($record->document_date)->format('d M Y') }}

                    </span>

                </div>

            </div>

            <div>

                <x-filament::badge
                    :color="match($record->status){

                        'draft' => 'warning',

                        'submitted' => 'info',

                        'approved' => 'success',

                        'completed' => 'success',

                        'cancelled' => 'danger',

                        default => 'gray',

                    }">

                    {{ strtoupper($record->status) }}

                </x-filament::badge>

            </div>

        </div>

    </div>

</div>