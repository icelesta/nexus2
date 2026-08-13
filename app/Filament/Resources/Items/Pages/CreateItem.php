<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected ?string $heading = 'Create Item';

    protected ?string $subheading = 'Register a new inventory item.';

    /**
     * --------------------------------------------------------------------------
     * Before Create
     * --------------------------------------------------------------------------
     */
    protected function beforeCreate(): void
    {
        //
        // Future:
        // - Validate business rules
        // - Verify item uniqueness
        // - Check numbering configuration
        //
    }

    /**
     * --------------------------------------------------------------------------
     * Normalize Form Data
     * --------------------------------------------------------------------------
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ([
            'item_code',
            'item_name',
            'short_name',
            'search_name',
            'part_number',
            'model_number',
            'drawing_number',
            'revision_no',
            'barcode',
        ] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    /**
     * --------------------------------------------------------------------------
     * After Create
     * --------------------------------------------------------------------------
     */
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Item Created Successfully')
            ->body(
                sprintf(
                    '%s (%s) has been created successfully.',
                    $this->record->item_name,
                    $this->record->item_code
                )
            )
            ->success()
            ->duration(5000)
            ->send();

        //
        // Future:
        //
        // - Generate default barcode
        // - Generate QR Code
        // - Create default Item Price
        // - Create default Item Stock
        // - Create Activity Log
        // - Dispatch ItemCreated Event
        //
    }

    /**
     * --------------------------------------------------------------------------
     * Redirect
     * --------------------------------------------------------------------------
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(
            'edit',
            [
                'record' => $this->record,
            ]
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Unsaved Changes Alert
     * --------------------------------------------------------------------------
     */
    public function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }
}