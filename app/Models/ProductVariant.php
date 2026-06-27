<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color',
    ];

    /**
     * Get the product this variant belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get inventory records for this variant.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(ProductVariantInventory::class);
    }

    /**
     * Get total quantity for this variant across all sizes.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->inventory()->sum('quantity');
    }

    /**
     * Get inventory for a specific size.
     */
    public function getQuantityForSize(int $size): int
    {
        return $this->inventory()
            ->where('size', $size)
            ->value('quantity') ?? 0;
    }
}
