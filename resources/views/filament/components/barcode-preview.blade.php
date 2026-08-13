@php
    use App\Support\Barcode\BarcodeGenerator;

    $barcode = $get('barcode') ?: '-';

    $type = $get('barcode_type') ?: 'CODE128';

    try {
        $svg = BarcodeGenerator::svg(
            $barcode,
            $type,
        );
    } catch (\Throwable $e) {
        $svg = null;
    }
@endphp

<div
    class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">

    <div class="flex items-center justify-between mb-5">

        <div>

            <h3 class="text-sm font-bold text-gray-800">

                Barcode Preview

            </h3>

            <p class="text-xs text-gray-500">

                Generated automatically from Item Code

            </p>

        </div>

        <div
            class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-600">

            {{ $type }}

        </div>

    </div>

    @if ($svg)

        <div
            class="flex justify-center rounded-lg border bg-gray-50 p-5">

            {!! $svg !!}

        </div>

    @else

        <div
            class="rounded-lg border border-dashed border-gray-300 p-10 text-center">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="mx-auto h-12 w-12 text-gray-400"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M4 7h16M4 12h16M4 17h16"/>

            </svg>

            <div class="mt-3 text-sm text-gray-500">

                Barcode belum dibuat

            </div>

        </div>

    @endif

    <div class="mt-5">

        <div
            class="rounded-lg bg-gray-100 px-3 py-2 text-center">

            <div
                class="font-mono text-sm tracking-[0.25em] font-semibold text-gray-900">

                {{ $barcode }}

            </div>

        </div>

    </div>

</div>