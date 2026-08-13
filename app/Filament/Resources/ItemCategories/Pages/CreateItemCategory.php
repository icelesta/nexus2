<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemCategories\Pages;

use App\Filament\Resources\ItemCategories\ItemCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItemCategory extends CreateRecord
{
    protected static string $resource = ItemCategoryResource::class;

    /**
     * Redirect after successful creation.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * Notification title.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Item Category created successfully.';
    }
}