<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\Item;
use App\Models\PurchaseRequisition;

use App\Models\PurchaseRequisitionItem;
use Illuminate\Support\Str;


class PurchaseRequisitionItemService
{
    /**
     * Temporary estimated price.
     *
     * Will be replaced by Pricing Engine.
     */
    private const DEFAULT_ESTIMATED_UNIT_PRICE = 0.00;

    /**
     * Build default Purchase Requisition Item
     * from Item Master.
     *
     * Future:
     * - Vendor Price
     * - Last Purchase Price
     * - Blanket Contract
     * - Purchase Agreement
     *
     * @return array<string,mixed>
     */
    public function buildDefaults(Item $item): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Master Reference
            |--------------------------------------------------------------------------
            */

            'item_id' => $item->id,

            'item_code' => $item->item_code,

            'item_name' => $item->item_name,

            'description' => $item->description,

            /*
            |--------------------------------------------------------------------------
            | Purchasing Defaults
            |--------------------------------------------------------------------------
            */

            'uom_id' => $item->purchase_uom_id
                ?? $item->base_uom_id,

            'warehouse_id' => $item->default_warehouse_id,

            /*
            |--------------------------------------------------------------------------
            | Transaction Defaults
            |--------------------------------------------------------------------------
            */

            'quantity' => 1,

            'estimated_unit_price' => self::DEFAULT_ESTIMATED_UNIT_PRICE,

            'estimated_amount' => 0,

        ];
    }

    /**
     * Calculate estimated amount.
     */
    public function calculateEstimatedAmount(
        float|int $quantity,
        float|int $price,
    ): float {

        return round(
            max(0, $quantity)
            *
            max(0, $price),
            2,
        );
    }

    /**
     * Prepare item payload before save.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    public function prepare(array $data): array
    {
        $quantity = (float) ($data['quantity'] ?? 0);

        $price = (float) (
            $data['estimated_unit_price']
            ?? self::DEFAULT_ESTIMATED_UNIT_PRICE
        );

        $data['quantity'] = $quantity;

        $data['estimated_unit_price'] = $price;

        $data['description'] = trim(
            (string) ($data['description'] ?? '')
        );

        $data['estimated_amount'] = $this->calculateEstimatedAmount(
            quantity: $quantity,
            price: $price,
        );

        return $data;
    }

    /**
     * Recalculate Purchase Requisition Header.
     *
     * Future:
     * - Total Lines
     * - Total Quantity
     * - Estimated Amount
     * - Discount
     * - Tax
     * - Grand Total
     */
    public function recalculateHeader(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        // Sprint 2.2.5

    }

    /**
     * Create Purchase Requisition Item.
     *
     * @param array<string,mixed> $data
     */
    public function create(
        PurchaseRequisition $purchaseRequisition,
        array $data,
    ): PurchaseRequisitionItem {

        $payload = $this->prepare($data);

        return PurchaseRequisitionItem::create([

            'uuid' => (string) Str::uuid(),

            'purchase_requisition_id' => $purchaseRequisition->id,

            'item_id' => $payload['item_id'],

            'uom_id' => $payload['uom_id'],

            'warehouse_id' => $payload['warehouse_id'],

            'quantity' => $payload['quantity'],

            'required_date' => $payload['required_date'],

            'remarks' => $payload['description'],

            'estimated_unit_price' => $payload['estimated_unit_price'],

            'status' => PurchaseRequisitionItem::STATUS_DRAFT,

            'created_by' => auth()->id(),

        ]);

    }

    /**
     * Delete Material Requisition Item.
     */
    public function delete(
        PurchaseRequisitionItem $item,
    ): void {

        $item->delete();

    }  
    

    /**
     * Update Purchase Requisition Item.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        PurchaseRequisitionItem $item,
        array $data,
    ): PurchaseRequisitionItem {

        $payload = $this->prepare($data);

        $item->update([

            'item_id' => $payload['item_id'],

            'uom_id' => $payload['uom_id'],

            'warehouse_id' => $payload['warehouse_id'],

            'quantity' => $payload['quantity'],

            'required_date' => $payload['required_date'],

            'remarks' => $payload['description'],

            'estimated_unit_price' => $payload['estimated_unit_price'],

            'updated_by' => auth()->id(),

        ]);

        return $item->refresh();
    }      

    /**
     * Update Purchase Requisition Item.
     */
    public function updateItem(
        int $itemId,
        array $data,
    ): PurchaseRequisitionItem {

        $item = PurchaseRequisitionItem::findOrFail($itemId);

        $item->update($data);

        $this->recalculateHeader(
            $item->purchaseRequisition
        );

        return $item->refresh();
    }

    /**
     * Delete Purchase Requisition Item.
     */
    public function deleteItem(
        int $itemId,
    ): bool {

        $item = PurchaseRequisitionItem::findOrFail($itemId);

        $purchaseRequisition = $item->purchaseRequisition;

        $deleted = (bool) $item->delete();

        $this->recalculateHeader(
            $purchaseRequisition
        );

        return $deleted;
    }

    /**
     * Remove all items from Purchase Requisition.
     */
    public function clearItems(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        $purchaseRequisition
            ->items()
            ->delete();

        $this->recalculateHeader(
            $purchaseRequisition
        );
    }
}