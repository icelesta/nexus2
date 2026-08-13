<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Forms\Get;

class ShippingAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table Name.
     */
    protected $table = 'shipping_addresses';

    /**
     * Mass Assignment.
     */
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */

        'shipping_code',
        'shipping_name',

        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        'company_id',
        'business_unit_id',
        'branch_id',
        'warehouse_id',

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */

        'address',
        'city',
        'province',
        'postal_code',
        'country',

        /*
        |--------------------------------------------------------------------------
        | Contact
        |--------------------------------------------------------------------------
        */

        'attention',
        'contact_person',
        'phone',
        'email',

        /*
        |--------------------------------------------------------------------------
        | Additional
        |--------------------------------------------------------------------------
        */

        'remarks',
        'is_default',
        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Attribute Casting.
     */
    protected function casts(): array
    {
        return [

            'is_default' => 'boolean',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
        );
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(
            BusinessUnit::class,
        );
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
        );
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by',
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by',
        );
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by',
        );
    }
}