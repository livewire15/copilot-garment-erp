@extends('layouts.app')

@section('title', 'Edit Invoice: ' . $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Invoice: {{ $invoice->invoice_number }}</h1>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Invoice Header -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Invoice Header</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="header-form">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" 
                               value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3">Charges & Taxes</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shipping Charges</label>
                                <input type="number" name="shipping_charges" class="form-control" 
                                       value="{{ old('shipping_charges', $invoice->shipping_charges) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Discount %</label>
                                <input type="number" name="discount_percentage" class="form-control" 
                                       value="{{ old('discount_percentage', $invoice->discount_percentage) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">CGST %</label>
                                <input type="number" name="cgst" class="form-control" 
                                       value="{{ old('cgst', $invoice->cgst) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SGST %</label>
                                <input type="number" name="sgst" class="form-control" 
                                       value="{{ old('sgst', $invoice->sgst) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">IGST %</label>
                                <input type="number" name="igst" class="form-control" 
                                       value="{{ old('igst', $invoice->igst) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-circle"></i> Update Header
                    </button>
                </form>
            </div>
        </div>

        <!-- Line Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Line Items</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->items as $item)
                            <tr>
                                <td>{{ $item->variant->product->name }}</td>
                                <td>{{ $item->variant->color }}</td>
                                <td>{{ $item->size }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->line_total, 2) }}</td>
                                <td>
                                    <form action="{{ route('invoice.items.destroy', $item) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Remove item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No items added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Items -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Items</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('invoice.items.store', $invoice) }}" id="add-item-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select id="product-select" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $product)
                                @if ($product->variants->count())
                                    <optgroup label="{{ $product->style_id }} - {{ $product->name }}">
                                        @foreach ($product->variants as $variant)
                                            <option value="{{ $variant->id }}" 
                                                    data-variant='@json($variant)'>
                                                {{ $variant->color }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="product_variant_id" id="variant-input">

                    <div id="inventory-container" class="mb-3" style="display: none;">
                        <label class="form-label">Quantity by Size</label>
                        <div id="sizes-container"></div>
                    </div>

                    <button type="submit" class="btn btn-success" id="add-btn" style="display: none;">
                        <i class="bi bi-plus-circle"></i> Add Items
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>

                @if ($invoice->discount_percentage > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount ({{ $invoice->discount_percentage }}%)</span>
                        <span>-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif

                @if ($invoice->cgst > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST</span>
                        <span>₹{{ number_format($invoice->cgst, 2) }}</span>
                    </div>
                @endif

                @if ($invoice->sgst > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST</span>
                        <span>₹{{ number_format($invoice->sgst, 2) }}</span>
                    </div>
                @endif

                @if ($invoice->igst > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>IGST</span>
                        <span>₹{{ number_format($invoice->igst, 2) }}</span>
                    </div>
                @endif

                @if ($invoice->shipping_charges > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>₹{{ number_format($invoice->shipping_charges, 2) }}</span>
                    </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between mb-3">
                    <strong>Grand Total</strong>
                    <strong>₹{{ number_format($invoice->grand_total, 2) }}</strong>
                </div>

                <div class="d-grid gap-2">
                    <form action="{{ route('invoices.finalize', $invoice) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" {{ !$invoice->items->count() ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle"></i> Finalize & Deduct Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('product-select').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        const variant = JSON.parse(selected.dataset.variant);
        document.getElementById('variant-input').value = variant.id;

        const sizesContainer = document.getElementById('sizes-container');
        sizesContainer.innerHTML = '';

        [38, 40, 42, 44, 46].forEach(size => {
            const available = variant.inventory[size] || 0;
            const html = `
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text">Size ${size}</span>
                    <input type="number" class="form-control" name="items[${size}]" min="0" max="${available}" value="0">
                    <span class="input-group-text text-muted">${available} avail</span>
                </div>
            `;
            sizesContainer.insertAdjacentHTML('beforeend', html);
        });

        document.getElementById('inventory-container').style.display = 'block';
        document.getElementById('add-btn').style.display = 'block';
    } else {
        document.getElementById('inventory-container').style.display = 'none';
        document.getElementById('add-btn').style.display = 'none';
    }
});
</script>
@endsection
