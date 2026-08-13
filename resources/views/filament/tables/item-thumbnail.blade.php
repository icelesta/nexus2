@php

    $record = $getRecord();

    $image = $record->primaryImage?->image_url
        ?? asset('images/no-image.png');

@endphp

<div
    class="nexus-item-thumbnail"

    data-item-id="{{ $record->id }}"
    data-item-image="{{ $image }}"
    data-item-code="{{ $record->item_code }}"
    data-item-name="{{ $record->item_name }}"
    data-item-category="{{ $record->category?->category_name ?? '-' }}"
    data-item-brand="{{ $record->brand?->brand_name ?? '-' }}"
>

    <img
        src="{{ $image }}"
        class="nexus-thumb"
        loading="lazy"
        draggable="false"
        alt="{{ $record->item_name }}"
    >

</div>

@once
    @include('filament.components.preview-card')
@endonce