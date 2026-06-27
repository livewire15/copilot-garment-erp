<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_variant_id',
        'size',
        'quantity',
        'rate',
        'line_total',
    ];

    protected $casts = [
        'size' => 'integer',
        'quantity' => 'integer',
        'rate' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Get the invoice this item belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the product variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
