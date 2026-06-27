@extends('layouts.app')

@section('title', 'View Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $product->name }}</h1>
    <div class="gap-2 d-flex">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body">
                @if ($product->image_path)
                    <img src="{{ Storage::url($product->image_path) }}" alt="Product" class="img-fluid rounded mb-3">
                @else
                    <div class="bg-light rounded p-5 text-center mb-3">
                        <i class="bi bi-image" style="font-size: 4rem; color: #ccc;"></i>
                    </div>
                @endif

                <table class="table table-sm">
                    <tr>
                        <th>Style ID</th>
                        <td><strong>{{ $product->style_id }}</strong></td>
                    </tr>
                    <tr>
                        <th>Fabric</th>
                        <td>{{ $product->fabric }}</td>
                    </tr>
                    <tr>
                        <th>Cost Price</th>
                        <td>{{ $product->cost_price ? '₹' . number_format($product->cost_price, 2) : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Selling Price</th>
                        <td><strong>₹{{ number_format($product->selling_price, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Color Variants & Inventory</h5>
            </div>
            <div class="card-body">
                @if ($product->variants->count())
                    @foreach ($product->variants as $variant)
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="mb-3">
                                <i class="bi bi-palette"></i> {{ $variant->color }}
                                <span class="badge bg-info">{{ $variant->inventory->sum('quantity') }} units</span>
                            </h6>

                            <table class="table table-sm table-light">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th class="text-end">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ([38, 40, 42, 44, 46] as $size)
                                        @php
                                            $inv = $variant->inventory->where('size', $size)->first();
                                            $qty = $inv ? $inv->quantity : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $size }}</td>
                                            <td class="text-end">{{ $qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No variants added yet. Edit product to add variants.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
