@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Invoices</h1>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Invoice
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" action="{{ route('invoices.index') }}" class="mb-0">
            <div class="row g-3">
                <div class="col-md-2">
                    <input type="text" name="invoice_number" class="form-control form-control-sm" 
                           placeholder="Invoice #" value="{{ request('invoice_number') }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="customer_name" class="form-control form-control-sm" 
                           placeholder="Customer" value="{{ request('customer_name') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control form-control-sm" 
                           value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control form-control-sm" 
                           value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                        <option value="BALANCE_PENDING" {{ request('status') === 'BALANCE_PENDING' ? 'selected' : '' }}>BALANCE PENDING</option>
                        <option value="CLEARED" {{ request('status') === 'CLEARED' ? 'selected' : '' }}>CLEARED</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($invoices->count())
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Balance</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td>{{ $invoice->customer->name }}</td>
                        <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="text-end">₹{{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="text-end">₹{{ number_format($invoice->total_paid, 2) }}</td>
                        <td class="text-end">₹{{ number_format($invoice->balance_amount, 2) }}</td>
                        <td>
                            @if ($invoice->status === 'DRAFT')
                                <span class="badge badge-draft">DRAFT</span>
                            @elseif ($invoice->status === 'BALANCE_PENDING')
                                <span class="badge badge-balance_pending">BALANCE PENDING</span>
                            @else
                                <span class="badge badge-cleared">CLEARED</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if ($invoice->isDraft())
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $invoices->links() }}
    </div>
@else
    <div class="alert alert-info">No invoices found.</div>
@endif
@endsection
