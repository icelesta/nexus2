<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Models\ShippingAddress;
use App\Services\Numbering\NumberingService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShippingAddressService
{
    public function __construct(
        protected NumberingService $numberingService,
    ) {
    }

    /**
     * Get all shipping addresses.
     */
    public function getAll(): Collection
    {
        return ShippingAddress::query()
            ->orderBy('shipping_code')
            ->get();
    }

    /**
     * Find shipping address.
     */
    public function find(int $id): ShippingAddress
    {
        return ShippingAddress::query()->findOrFail($id);
    }

    /**
     * Create shipping address.
     */
    public function create(array $data): ShippingAddress
    {
        return DB::transaction(function () use ($data) {

            $this->validate($data);

            $this->ensureUnique($data);

            $data['shipping_code'] = $this->numberingService->generate(
                documentType: 'SHIPPING_ADDRESS',
                companyId: $data['company_id'] ?? null,
                businessUnitId: $data['business_unit_id'] ?? null,
                branchId: $data['branch_id'] ?? null,
            );

            return ShippingAddress::create($data);
        });
    }

    /**
     * Update shipping address.
     */
    public function update(
        ShippingAddress $shippingAddress,
        array $data,
    ): ShippingAddress {

        return DB::transaction(function () use (
            $shippingAddress,
            $data,
        ) {

            $this->validate(
                $data,
                $shippingAddress->id,
            );

            /*
             * Never regenerate shipping_code.
             */
            unset($data['shipping_code']);

            $this->ensureUnique(
                $data,
                $shippingAddress->id,
            );

            $shippingAddress->update($data);

            return $shippingAddress->refresh();
        });
    }

    /**
     * Delete shipping address.
     */
    public function delete(
        ShippingAddress $shippingAddress,
    ): void {

        DB::transaction(function () use (
            $shippingAddress,
        ) {

            /**
             * Future validation:
             * Prevent deletion when already used by
             * Purchase Order / Material Requisition.
             */

            $shippingAddress->delete();

        });
    }

    /**
     * Validate business rules.
     */
    protected function validate(
        array $data,
        ?int $ignoreId = null,
    ): void {

        if (empty($data['shipping_name'])) {
            throw ValidationException::withMessages([
                'shipping_name' => 'Shipping Name is required.',
            ]);
        }

        if (empty($data['company_id'])) {
            throw ValidationException::withMessages([
                'company_id' => 'Company is required.',
            ]);
        }

        if (empty($data['branch_id'])) {
            throw ValidationException::withMessages([
                'branch_id' => 'Branch is required.',
            ]);
        }
    }

    /**
     * Ensure unique data.
     */
    protected function ensureUnique(
        array $data,
        ?int $ignoreId = null,
    ): void {

        if (! empty($data['shipping_code'])) {

            $exists = ShippingAddress::query()
                ->where('shipping_code', $data['shipping_code'])
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '<>', $ignoreId),
                )
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'shipping_code' => 'Shipping Code already exists.',
                ]);
            }
        }

        $exists = ShippingAddress::query()
            ->where('shipping_name', $data['shipping_name'])
            ->when(
                $ignoreId,
                fn ($query) => $query->where('id', '<>', $ignoreId),
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'shipping_name' => 'Shipping Name already exists.',
            ]);
        }
    }
}