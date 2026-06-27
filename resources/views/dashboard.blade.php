@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="metric-value">{{ $totalProducts }}</div>
                <div class="metric-label">Total Products</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="metric-value">{{ $totalInventory }}</div>
                <div class="metric-label">Total Inventory Quantity</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="metric-value">₹{{ number_format($totalOutstanding, 2) }}</div>
                <div class="metric-label">Outstanding Balance</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="metric-value">₹{{ number_format($totalSalesThisMonth, 2) }}</div>
                <div class="metric-label">Sales This Month</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Low Stock Products -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i> Low Stock Products
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lowStockProducts->count())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Style ID</th>
                                        <th>Product Name</th>
                                        <th class="text-end">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lowStockProducts as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                                    {{ $product->style_id }}
                                                </a>
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td class="text-end">
                                                <span class="badge bg-warning text-dark">{{ $product->total_quantity }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">All products have sufficient stock.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Recent Invoices
                    </h5>
                </div>
                <div class="card-body">
                    @if ($recentInvoices->count())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->customer->name }}</td>
                                            <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                                            <td>
                                                @if ($invoice->status === 'DRAFT')
                                                    <span class="badge badge-draft">DRAFT</span>
                                                @elseif ($invoice->status === 'BALANCE_PENDING')
                                                    <span class="badge badge-balance_pending">BALANCE PENDING</span>
                                                @else
                                                    <span class="badge badge-cleared">CLEARED</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary mt-3">View All Invoices</a>
                    @else
                        <p class="text-muted text-center mb-0">No invoices yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
