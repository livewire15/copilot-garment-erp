<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Http\Requests\RecordPaymentRequest;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show form to record payment for an invoice.
     */
    public function create(Invoice $invoice)
    {
        if ($invoice->isCancelled()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Cannot record payment for cancelled invoice.');
        }

        if ($invoice->status === 'CLEARED') {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('info', 'Invoice is already fully paid.');
        }

        $invoice->load('payments');

        return view('payments.create', compact('invoice'));
    }

    /**
     * Store payment record and update invoice status.
     */
    public function store(RecordPaymentRequest $request, Invoice $invoice)
    {
        if ($invoice->isCancelled()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Cannot record payment for cancelled invoice.');
        }

        return DB::transaction(function () use ($request, $invoice) {
            $data = $request->validated();

            // Record payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $data['payment_date'],
                'mode' => $data['mode'],
                'amount' => $data['amount'],
            ]);

            // Update invoice totals
            $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');
            $balanceAmount = max(0, $invoice->grand_total - $totalPaid);

            // Determine status
            $status = 'BALANCE_PENDING';
            if ($balanceAmount <= 0) {
                $status = 'CLEARED';
            }

            $invoice->update([
                'total_paid' => $totalPaid,
                'balance_amount' => $balanceAmount,
                'status' => $status,
            ]);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Payment recorded successfully.');
        });
    }

    /**
     * Delete payment record (admin only).
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;

        $payment->delete();

        // Recalculate invoice totals
        $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');
        $balanceAmount = $invoice->grand_total - $totalPaid;

        $status = 'BALANCE_PENDING';
        if ($balanceAmount <= 0) {
            $status = 'CLEARED';
        }

        $invoice->update([
            'total_paid' => $totalPaid,
            'balance_amount' => $balanceAmount,
            'status' => $status,
        ]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Payment deleted.');
    }
}
