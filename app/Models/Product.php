<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'style_id',
        'name',
        'fabric',
        'cost_price',
        'selling_price',
        'image_path',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Get all variants for this product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get total inventory quantity across all variants and sizes.
     */
    public function getTotalInventoryAttribute(): int
    {
        return $this->variants()
            ->join('product_variant_inventory', 'product_variants.id', '=', 'product_variant_inventory.product_variant_id')
            ->sum('product_variant_inventory.quantity');
    }
}
