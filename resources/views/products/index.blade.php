@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" action="{{ route('products.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="style_id" class="form-control" placeholder="Search by Style ID" 
                           value="{{ request('style_id') }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Search by Product Name" 
                           value="{{ request('name') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        @if ($products->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Style ID</th>
                            <th>Name</th>
                            <th>Fabric</th>
                            <th class="text-end">Selling Price</th>
                            <th>Image</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td><strong>{{ $product->style_id }}</strong></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->fabric }}</td>
                                <td class="text-end">₹{{ number_format($product->selling_price, 2) }}</td>
                                <td>
                                    @if ($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}" alt="Product" 
                                             style="height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @else
            <p class="text-muted text-center">No products found.</p>
        @endif
    </div>
</div>
@endsection
