@php

    use App\Support\Barcode\QrGenerator;

    $qr = $get('qr_code');

    if (blank($qr)) {
        $qr = '-';
    }

    try {
        $svg = QrGenerator::svg(
            $qr,
            220,
        );
    } catch (\Throwable $e) {
        $svg = null;
    }

@endphp

<div
    class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">

    <div
        class="flex items-center justify-between mb-5">

        <div>

            <h3
                class="text-sm font-bold text-gray-800">

                QR Code Preview

            </h3>

            <p
                class="text-xs text-gray-500">

                Generated automatically from Item Information

            </p>

        </div>

        <div
            class="rounded-full bg-success-50 px-3 py-1 text-xs font-semibold text-success-600">

            QR CODE

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

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mx-auto h-12 w-12 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9 12h6m-3-3v6m8-3A9 9 0 113 12a9 9 0 0118 0z"/>

            </svg>

            <div
                class="mt-3 text-sm text-gray-500">

                QR Code belum dibuat

            </div>

        </div>

    @endif

    <div class="mt-5">

        <div
            class="rounded-lg bg-gray-100 px-3 py-2">

            <div
                class="font-mono text-xs break-all text-center text-gray-900">

                {{ $qr }}

            </div>

        </div>

    </div>

</div>