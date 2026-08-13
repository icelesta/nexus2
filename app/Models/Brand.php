<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'brands';

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
        | Brand Information
        |--------------------------------------------------------------------------
        */

        'brand_code',
        'brand_name',
        'short_name',

        /*
        |--------------------------------------------------------------------------
        | Manufacturer
        |--------------------------------------------------------------------------
        |
        | Temporary field.
        | Will be replaced by manufacturer_id after
        | Manufacturer Master is implemented.
        |
        */

        'manufacturer_name',

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
        | Country
        |--------------------------------------------------------------------------
        */

        'country_id',

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
     * Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(
            Item::class,
            'brand_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->brand_code} - {$this->brand_name}";
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
            ->orderBy('brand_code')
            ->orderBy('brand_name');
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
                ->where('brand_code', 'like', "%{$keyword}%")
                ->orWhere('brand_name', 'like', "%{$keyword}%")
                ->orWhere('short_name', 'like', "%{$keyword}%")
                ->orWhere('manufacturer_name', 'like', "%{$keyword}%");

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