@extends('layouts.app')

@section('title', 'Invoice: ' . $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>{{ $invoice->invoice_number }}</h1>
        <p class="text-muted mb-0">{{ $invoice->invoice_date->format('d F Y') }}</p>
    </div>
    <div class="gap-2 d-flex">
        @if ($invoice->isDraft())
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endif
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Invoice Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if ($invoice->status === 'DRAFT')
                            <span class="badge badge-draft">DRAFT</span>
                        @elseif ($invoice->status === 'BALANCE_PENDING')
                            <span class="badge badge-balance_pending">BALANCE PENDING</span>
                        @else
                            <span class="badge badge-cleared">CLEARED</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Customer:</strong> {{ $invoice->customer->name }}
                    </div>
                </div>

                @if ($invoice->customer->address)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Address:</strong> {{ $invoice->customer->address }}
                        </div>
                    </div>
                @endif

                @if ($invoice->customer->gst_number)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>GST Number:</strong> {{ $invoice->customer->gst_number }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No items added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (!$invoice->isCancelled() && $invoice->payments->count())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Mode</th>
                                <th class="text-end">Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td><span class="badge bg-secondary">{{ $payment->mode }}</span></td>
                                    <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete payment?')">
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
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>

                @if ($invoice->discount_amount > 0)
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

                @if (!$invoice->isDraft())
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Paid</span>
                            <span class="text-success">₹{{ number_format($invoice->total_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Balance Due</strong>
                            <strong class="text-danger">₹{{ number_format($invoice->balance_amount, 2) }}</strong>
                        </div>
                    </div>
                @endif

                <hr>

                <div class="d-grid gap-2">
                    @if ($invoice->isDraft())
                        <form action="{{ route('invoices.finalize', $invoice) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Finalize Invoice
                            </button>
                        </form>
                    @endif

                    @if (!$invoice->isCancelled() && !$invoice->isDraft())
                        @if ($invoice->balance_amount > 0)
                            <a href="{{ route('payments.create', $invoice) }}" class="btn btn-primary">
                                <i class="bi bi-cash-coin"></i> Record Payment
                            </a>
                        @endif

                        <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('Cancel this invoice? Inventory will be restored.')">
                                <i class="bi bi-x-circle"></i> Cancel Invoice
                            </button>
                        </form>
                    @endif

                    @if ($invoice->isCancelled())
                        <div class="alert alert-danger mb-0">
                            <strong>Cancelled</strong> on {{ $invoice->cancelled_at->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
