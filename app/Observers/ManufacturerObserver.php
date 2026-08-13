<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Manufacturer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManufacturerObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(Manufacturer $manufacturer): void
    {
        if (blank($manufacturer->uuid)) {
            $manufacturer->uuid = (string) Str::uuid();
        }

        if (Auth::check()) {
            $manufacturer->created_by = Auth::id();
            $manufacturer->updated_by = Auth::id();
        }

        $manufacturer->is_active ??= true;
        $manufacturer->sort_order ??= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Saving
    |--------------------------------------------------------------------------
    */

    public function saving(Manufacturer $manufacturer): void
    {
        $this->normalize($manufacturer);
    }

    /*
    |--------------------------------------------------------------------------
    | Created
    |--------------------------------------------------------------------------
    */

    public function created(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(Manufacturer $manufacturer): void
    {
        if (Auth::check()) {
            $manufacturer->updated_by = Auth::id();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Updated
    |--------------------------------------------------------------------------
    */

    public function updated(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleted
    |--------------------------------------------------------------------------
    */

    public function deleted(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restored
    |--------------------------------------------------------------------------
    */

    public function restored(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(Manufacturer $manufacturer): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Data
    |--------------------------------------------------------------------------
    */

    protected function normalize(
        Manufacturer $manufacturer,
    ): void {

        if (! blank($manufacturer->manufacturer_code)) {
            $manufacturer->manufacturer_code = strtoupper(
                trim($manufacturer->manufacturer_code)
            );
        }

        if (! blank($manufacturer->manufacturer_name)) {
            $manufacturer->manufacturer_name = trim(
                $manufacturer->manufacturer_name
            );
        }

        if (! blank($manufacturer->short_name)) {
            $manufacturer->short_name = strtoupper(
                trim($manufacturer->short_name)
            );
        }

        if (! blank($manufacturer->website)) {
            $manufacturer->website = strtolower(
                trim($manufacturer->website)
            );
        }

        if (! blank($manufacturer->email)) {
            $manufacturer->email = strtolower(
                trim($manufacturer->email)
            );
        }

        if (! blank($manufacturer->phone)) {
            $manufacturer->phone = trim(
                $manufacturer->phone
            );
        }

        if (! blank($manufacturer->address)) {
            $manufacturer->address = trim(
                $manufacturer->address
            );
        }

        if (! blank($manufacturer->city)) {
            $manufacturer->city = trim(
                $manufacturer->city
            );
        }

        if (! blank($manufacturer->state)) {
            $manufacturer->state = trim(
                $manufacturer->state
            );
        }

        if (! blank($manufacturer->postal_code)) {
            $manufacturer->postal_code = strtoupper(
                trim($manufacturer->postal_code)
            );
        }

        if (! blank($manufacturer->country)) {
            $manufacturer->country = trim(
                $manufacturer->country
            );
        }

        if (! blank($manufacturer->remarks)) {
            $manufacturer->remarks = trim(
                $manufacturer->remarks
            );
        }
    }
}