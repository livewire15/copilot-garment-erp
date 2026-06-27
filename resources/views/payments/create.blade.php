@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Record Payment</h1>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Invoice Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Invoice Number</th>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $invoice->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Invoice Date</th>
                        <td>{{ $invoice->invoice_date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <td><strong>₹{{ number_format($invoice->grand_total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Paid</th>
                        <td class="text-success">₹{{ number_format($invoice->total_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Balance Due</th>
                        <td class="text-danger"><strong>₹{{ number_format($invoice->balance_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        @if ($invoice->payments->count())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Previous Payments</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Mode</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td><span class="badge bg-secondary">{{ $payment->mode }}</span></td>
                                    <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Record New Payment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.store', $invoice) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select name="mode" class="form-select @error('mode') is-invalid @enderror" required>
                            <option value="">-- Select Mode --</option>
                            <option value="CASH" {{ old('mode') === 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="UPI" {{ old('mode') === 'UPI' ? 'selected' : '' }}>UPI</option>
                            <option value="NEFT" {{ old('mode') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                        </select>
                        @error('mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', $invoice->balance_amount) }}" step="0.01" min="0.01" 
                                   max="{{ $invoice->balance_amount }}" required>
                        </div>
                        <small class="text-muted">Max: ₹{{ number_format($invoice->balance_amount, 2) }}</small>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-3">
                        <strong>Note:</strong> Once paid, invoice status will be updated automatically.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Record Payment
                        </button>
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
