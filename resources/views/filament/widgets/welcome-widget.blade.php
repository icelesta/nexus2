@php
    $now = now()->setTimezone('Asia/Jakarta');

    $hour = (int) $now->format('H');

    $greeting = match (true) {
        $hour < 12 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };

    $userName = auth()->user()?->name ?? 'User';
@endphp

<div
    style="
        width:100%;
        min-height:220px;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:24px;
        padding:48px;
        box-shadow:0 10px 30px rgba(0,0,0,.05);
    ">

    <div
        style="
            color:#f59e0b;
            font-size:12px;
            font-weight:700;
            letter-spacing:4px;
            text-transform:uppercase;
        ">
        Enterprise Resource Planning
    </div>

    <h1
        style="
            margin-top:8px;
            font-size:72px;
            font-weight:900;
            line-height:1;
            color:#0f172a;
        ">
        NEXUS ERP
    </h1>

    <div
        style="
            margin-top:20px;
            font-size:22px;
            font-weight:700;
        ">
        <span style="color:#f59e0b">
            {{ $greeting }},
        </span>

        {{ $userName }}
    </div>

    <div
        style="
            color:#000000;
            font-size:10px;
            font-weight:700;
            letter-spacing:4px;
            text-transform:uppercase;
        ">

        {{ $now->locale('id')->translatedFormat('l, d F Y') }}

        •

        {{ $now->format('H:i') }} WIB

        •

        Jakarta

    </div>

</div>