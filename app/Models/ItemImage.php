<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ItemImage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'item_images';

    protected $fillable = [
        'item_id',
        'image_path',
        'image_name',
        'is_primary',
        'sort_order',
        'remarks',
        'uuid',
        'created_by',
        'updated_by',
        'deleted_by',

    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'is_primary' => 'boolean',

            'sort_order' => 'integer',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        if (blank($this->image_path)) {
            return asset('images/no-image.png');
        }

        return Storage::url($this->image_path);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->image_name
            ?: basename($this->image_path);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary(
        Builder $query
    ): Builder {
        return $query->where(
            'is_primary',
            true
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {
        
    return $query
        ->orderBy('sort_order')
        ->orderBy('id');
    }
    

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function hasImage(): bool
    {
        return filled($this->image_path);
    }

    public function filename(): string
    {
        return basename($this->image_path ?? '');
    }

}