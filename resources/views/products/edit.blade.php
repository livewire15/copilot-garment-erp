@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Product: {{ $product->name }}</h1>
    <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">Style ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('style_id') is-invalid @enderror" 
                               name="style_id" value="{{ old('style_id', $product->style_id) }}" required>
                        @error('style_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fabric <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('fabric') is-invalid @enderror" 
                               name="fabric" value="{{ old('fabric', $product->fabric) }}" required>
                        @error('fabric')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cost Price</label>
                                <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                       name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0">
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                       name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" required>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        @if ($product->image_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($product->image_path) }}" alt="Product" 
                                     style="max-width: 150px; border-radius: 4px;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*">
                        <small class="form-text text-muted">Leave blank to keep current image</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3">Color Variants & Inventory</h5>

                    <div id="variants-container">
                        @forelse ($product->variants as $idx => $variant)
                            <div class="variant-item card mb-3 p-3">
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" 
                                           name="variants[{{ $idx }}][color]" 
                                           value="{{ $variant->color }}">
                                </div>

                                <label class="form-label">Inventory</label>
                                <div class="row mb-3">
                                    @foreach ([38, 40, 42, 44, 46] as $size)
                                        @php
                                            $inv = $variant->inventory->where('size', $size)->first();
                                            $qty = $inv ? $inv->quantity : 0;
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Size {{ $size }}</span>
                                                <input type="number" class="form-control" 
                                                       name="variants[{{ $idx }}][inventory][{{ $size }}]" 
                                                       value="{{ $qty }}" min="0">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="variant-item card mb-3 p-3">
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="variants[0][color]">
                                </div>

                                <label class="form-label">Inventory</label>
                                <div class="row mb-3">
                                    @foreach ([38, 40, 42, 44, 46] as $size)
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Size {{ $size }}</span>
                                                <input type="number" class="form-control" 
                                                       name="variants[0][inventory][{{ $size }}]" 
                                                       value="0" min="0">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addVariant()">
                            <i class="bi bi-plus-circle"></i> Add Another Color
                        </button>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Product
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">Cancel</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline ms-auto"
                              onsubmit="return confirm('Delete this product? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Product
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addVariant() {
    const container = document.getElementById('variants-container');
    const newIdx = container.children.length;
    const variantHTML = `
        <div class="variant-item card mb-3 p-3">
            <div class="mb-3">
                <label class="form-label">Color</label>
                <input type="text" class="form-control" name="variants[${newIdx}][color]">
            </div>

            <label class="form-label">Inventory</label>
            <div class="row mb-3">
                ${[38, 40, 42, 44, 46].map(size => `
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Size ${size}</span>
                            <input type="number" class="form-control" 
                                   name="variants[${newIdx}][inventory][${size}]" 
                                   value="0" min="0">
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', variantHTML);
}
</script>
@endsection
