{{-- ===========================================================
 | DOCUMENT INFORMATION
 | Sprint MR-Print 2.2
 =========================================================== --}}

<div class="border-b border-gray-300 px-8 py-6">

    <div class="grid grid-cols-2 gap-10">

        {{-- ===================================================== --}}
        {{-- REQUEST INFORMATION --}}
        {{-- ===================================================== --}}

        <div>

            <h3 class="mb-4 border-b border-gray-200 pb-2 text-sm font-bold uppercase tracking-wider text-blue-700">
                Request Information
            </h3>

            <table class="w-full text-sm">

                <tbody>

                    <tr>
                        <td class="w-44 py-2 font-medium text-gray-600">Requester</td>
                        <td>: {{ $this->record->requester?->name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Request Date</td>
                        <td>: {{ optional($this->record->request_date)->format('d M Y') ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Required Date</td>
                        <td>: {{ optional($this->record->required_date)->format('d M Y') ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Priority</td>
                        <td>: {{ ucfirst($this->record->priority ?? '-') }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Delivery Location</td>
                        <td>: {{ $this->record->delivery_location ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Reference No</td>
                        <td>: {{ $this->record->reference_no ?? '-' }}</td>
                    </tr>

                    <tr class="align-top">
                        <td class="py-2 font-medium text-gray-600">Remarks</td>
                        <td>: {{ $this->record->remarks ?? '-' }}</td>
                    </tr>

                </tbody>

            </table>

        </div>

        {{-- ===================================================== --}}
        {{-- ORGANIZATION INFORMATION --}}
        {{-- ===================================================== --}}

        <div>

            <h3 class="mb-4 border-b border-gray-200 pb-2 text-sm font-bold uppercase tracking-wider text-blue-700">
                Organization Information
            </h3>

            <table class="w-full text-sm">

                <tbody>

                    <tr>
                        <td class="w-44 py-2 font-medium text-gray-600">Company</td>
                        <td>: {{ $this->record->company?->company_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Business Unit</td>
                        <td>: {{ $this->record->businessUnit?->business_unit_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Branch</td>
                        <td>: {{ $this->record->branch?->display_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Department</td>
                        <td>: {{ $this->record->department?->department_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Section</td>
                        <td>: {{ $this->record->section?->section_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Cost Center</td>
                        <td>: {{ $this->record->costCenter?->cost_center_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="py-2 font-medium text-gray-600">Warehouse</td>
                        <td>: {{ $this->record->warehouse?->display_name ?? '-' }}</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>