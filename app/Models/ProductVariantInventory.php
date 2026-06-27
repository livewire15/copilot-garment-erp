<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantInventory extends Model
{
    protected $table = 'product_variant_inventory';

    protected $fillable = [
        'product_variant_id',
        'size',
        'quantity',
    ];

    protected $casts = [
        'size' => 'integer',
        'quantity' => 'integer',
    ];

    /**
     * Get the variant this inventory belongs to.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
