<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BrandObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(Brand $brand): void
    {
        if (blank($brand->uuid)) {
            $brand->uuid = (string) Str::uuid();
        }

        if (Auth::check()) {
            $brand->created_by = Auth::id();
            $brand->updated_by = Auth::id();
        }

        $brand->is_active ??= true;
        $brand->sort_order ??= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Saving
    |--------------------------------------------------------------------------
    |
    | Executed for both Create and Update.
    |
    */

    public function saving(Brand $brand): void
    {
        $this->normalize($brand);
    }

    /*
    |--------------------------------------------------------------------------
    | Created
    |--------------------------------------------------------------------------
    */

    public function created(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(Brand $brand): void
    {
        if (Auth::check()) {
            $brand->updated_by = Auth::id();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Updated
    |--------------------------------------------------------------------------
    */

    public function updated(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleted
    |--------------------------------------------------------------------------
    */

    public function deleted(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restored
    |--------------------------------------------------------------------------
    */

    public function restored(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(Brand $brand): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Normalization
    |--------------------------------------------------------------------------
    */

    protected function normalize(Brand $brand): void
    {
        if (! blank($brand->brand_code)) {
            $brand->brand_code = strtoupper(trim($brand->brand_code));
        }

        if (! blank($brand->brand_name)) {
            $brand->brand_name = trim($brand->brand_name);
        }

        if (! blank($brand->short_name)) {
            $brand->short_name = strtoupper(trim($brand->short_name));
        }

        if (! blank($brand->manufacturer_name)) {
            $brand->manufacturer_name = trim($brand->manufacturer_name);
        }

        if (! blank($brand->website)) {
            $brand->website = strtolower(trim($brand->website));
        }

        if (! blank($brand->email)) {
            $brand->email = strtolower(trim($brand->email));
        }

        if (! blank($brand->phone)) {
            $brand->phone = trim($brand->phone);
        }

        if (! blank($brand->remarks)) {
            $brand->remarks = trim($brand->remarks);
        }
    }
}