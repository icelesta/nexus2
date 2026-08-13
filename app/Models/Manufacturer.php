<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'manufacturers';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        'uuid',

        /*
        |--------------------------------------------------------------------------
        | Manufacturer Information
        |--------------------------------------------------------------------------
        */

        'manufacturer_code',
        'manufacturer_name',
        'short_name',

        /*
        |--------------------------------------------------------------------------
        | Contact Information
        |--------------------------------------------------------------------------
        */

        'website',
        'email',
        'phone',

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */

        'address',
        'city',
        'state',
        'postal_code',
        'country',

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        'remarks',

        /*
        |--------------------------------------------------------------------------
        | Configuration
        |--------------------------------------------------------------------------
        */

        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'deleted_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [
        'display_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',

            'sort_order' => 'integer',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Created By
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Updated By
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /**
     * Brands
     */
    public function brands(): HasMany
    {
        return $this->hasMany(
            Brand::class,
            'manufacturer_id'
        );
    }

    /**
     * Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(
            Item::class,
            'manufacturer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->manufacturer_code} - {$this->manufacturer_name}";
    }

    /*
    |--------------------------------------------------------------------------
    | String Representation
    |--------------------------------------------------------------------------
    */

    public function __toString(): string
    {
        return $this->display_name;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Active
     */
    public function scopeActive(
        Builder $query,
    ): Builder {

        return $query->where(
            'is_active',
            true
        );
    }

    /**
     * Inactive
     */
    public function scopeInactive(
        Builder $query,
    ): Builder {

        return $query->where(
            'is_active',
            false
        );
    }

    /**
     * Ordered
     */
    public function scopeOrdered(
        Builder $query,
    ): Builder {

        return $query
            ->orderBy('sort_order')
            ->orderBy('manufacturer_code')
            ->orderBy('manufacturer_name');
    }

    /**
     * Search
     */
    public function scopeSearch(
        Builder $query,
        string $keyword,
    ): Builder {

        return $query->where(function (Builder $query) use ($keyword) {

            $query
                ->where('manufacturer_code', 'like', "%{$keyword}%")
                ->orWhere('manufacturer_name', 'like', "%{$keyword}%")
                ->orWhere('short_name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%");

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}