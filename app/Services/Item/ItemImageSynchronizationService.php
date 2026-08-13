<?php

declare(strict_types=1);

namespace App\Services\Item;

use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\DB;

class ItemImageSynchronizationService
{
    /**
     * Synchronize primary image from Item
     * to Item Images table.
     */
    public function synchronize(Item $item): void
    {
        DB::transaction(function () use ($item) {

            /*
            |--------------------------------------------------------------------------
            | Skip Empty Image
            |--------------------------------------------------------------------------
            */

            if (blank($item->image)) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Synchronize Primary Image
            |--------------------------------------------------------------------------
            */

            ItemImage::updateOrCreate(

                [
                    'item_id'    => $item->id,
                    'is_primary' => true,
                ],

                [
                    'image_path' => $item->image,
                    'image_name' => basename($item->image),
                    'sort_order' => 0,
                ]

            );
        });
    }
}