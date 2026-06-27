<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantInventory;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\FinalizeInvoiceRequest;
use App\Http\Requests\RecordPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with search and filters.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        // Search by invoice number
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        // Search by customer name
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request('customer_name') . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::with('variants.inventory')->get();

        return view('invoices.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created invoice in storage (as DRAFT).
     */
    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'status' => 'DRAFT',
            'subtotal' => 0,
            'shipping_charges' => $data['shipping_charges'] ?? 0,
            'discount_percentage' => $data['discount_percentage'] ?? 0,
            'discount_amount' => 0,
            'cgst' => $data['cgst'] ?? 0,
            'sgst' => $data['sgst'] ?? 0,
            'igst' => $data['igst'] ?? 0,
            'grand_total' => 0,
            'balance_amount' => 0,
        ]);

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Invoice created. Add items and finalize.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.variant.product', 'payments');

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        if (!$invoice->isDraft()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $invoice->load('items.variant.product', 'payments');
        $customers = Customer::all();
        $products = Product::with('variants.inventory')->get();

        return view('invoices.edit', compact('invoice', 'customers', 'products'));
    }

    /**
     * Update the specified draft invoice.
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        if (!$invoice->isDraft()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $data = $request->validated();

        $invoice->update([
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'shipping_charges' => $data['shipping_charges'] ?? 0,
            'discount_percentage' => $data['discount_percentage'] ?? 0,
            'cgst' => $data['cgst'] ?? 0,
            'sgst' => $data['sgst'] ?? 0,
            'igst' => $data['igst'] ?? 0,
        ]);

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Invoice updated.');
    }

    /**
     * Finalize invoice and deduct inventory.
     */
    public function finalize(FinalizeInvoiceRequest $request, Invoice $invoice)
    {
        if (!$invoice->isDraft()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be finalized.');
        }

        return DB::transaction(function () use ($request, $invoice) {
            // Validate inventory availability
            foreach ($invoice->items as $item) {
                $inventory = ProductVariantInventory::where('product_variant_id', $item->product_variant_id)
                    ->where('size', $item->size)
                    ->first();

                if (!$inventory || $inventory->quantity < $item->quantity) {
                    throw new \Exception('Insufficient inventory for ' . $item->variant->product->name . ' - ' . $item->variant->color . ' - Size ' . $item->size);
                }
            }

            // Deduct inventory
            foreach ($invoice->items as $item) {
                ProductVariantInventory::where('product_variant_id', $item->product_variant_id)
                    ->where('size', $item->size)
                    ->decrement('quantity', $item->quantity);
            }

            // Calculate totals
            $subtotal = $invoice->items->sum('line_total');
            $discountAmount = $subtotal * ($invoice->discount_percentage / 100);
            $subtotalAfterDiscount = $subtotal - $discountAmount;

            $cgst = ($invoice->cgst > 0) ? $subtotalAfterDiscount * ($invoice->cgst / 100) : 0;
            $sgst = ($invoice->sgst > 0) ? $subtotalAfterDiscount * ($invoice->sgst / 100) : 0;
            $igst = ($invoice->igst > 0) ? $subtotalAfterDiscount * ($invoice->igst / 100) : 0;

            $grandTotal = $subtotalAfterDiscount + $cgst + $sgst + $igst + $invoice->shipping_charges;
            $balanceAmount = $grandTotal;

            // Update invoice
            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'grand_total' => $grandTotal,
                'balance_amount' => $balanceAmount,
                'status' => 'BALANCE_PENDING',
                'finalized_at' => now(),
            ]);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Invoice finalized successfully.');
        });
    }

    /**
     * Cancel invoice and restore inventory.
     */
    public function cancel(Invoice $invoice)
    {
        if ($invoice->isCancelled()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Invoice is already cancelled.');
        }

        return DB::transaction(function () use ($invoice) {
            // Restore inventory if finalized
            if ($invoice->isFinalized()) {
                foreach ($invoice->items as $item) {
                    ProductVariantInventory::where('product_variant_id', $item->product_variant_id)
                        ->where('size', $item->size)
                        ->increment('quantity', $item->quantity);
                }
            }

            // Mark as cancelled
            $invoice->update([
                'cancelled_at' => now(),
            ]);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Invoice cancelled. Inventory restored.');
        });
    }

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . now()->format('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Add item to invoice.
     */
    public function addItem(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'items' => 'required|array',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);

        foreach ($validated['items'] as $size => $quantity) {
            if ($quantity > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_variant_id' => $variant->id,
                    'size' => $size,
                    'quantity' => $quantity,
                    'rate' => $variant->product->selling_price,
                    'line_total' => $quantity * $variant->product->selling_price,
                ]);
            }
        }

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Items added to invoice.');
    }

    /**
     * Remove item from invoice.
     */
    public function removeItem(InvoiceItem $item)
    {
        $invoice = $item->invoice;

        $item->delete();

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Item removed from invoice.');
    }

    /**
     * Update item quantity and rate.
     */
    public function updateItem(Request $request, InvoiceItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'rate' => 'required|numeric|min:0',
        ]);

        $lineTotal = $validated['quantity'] * $validated['rate'];

        $item->update([
            'quantity' => $validated['quantity'],
            'rate' => $validated['rate'],
            'line_total' => $lineTotal,
        ]);

        return redirect()
            ->route('invoices.edit', $item->invoice)
            ->with('success', 'Item updated.');
    }
}
