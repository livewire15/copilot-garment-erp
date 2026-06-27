@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Invoice</h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <h5 class="mb-3">Invoice Header</h5>

                    <div class="mb-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Select Customer --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                               value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
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
                                       value="{{ old('shipping_charges', 0) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Discount %</label>
                                <input type="number" name="discount_percentage" class="form-control" 
                                       value="{{ old('discount_percentage', 0) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">CGST %</label>
                                <input type="number" name="cgst" class="form-control" 
                                       value="{{ old('cgst', 0) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SGST %</label>
                                <input type="number" name="sgst" class="form-control" 
                                       value="{{ old('sgst', 0) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">IGST %</label>
                                <input type="number" name="igst" class="form-control" 
                                       value="{{ old('igst', 0) }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Invoice (Draft)
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
