<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with key metrics.
     */
    public function index()
    {
        // Total products count
        $totalProducts = \App\Models\Product::count();

        // Total inventory quantity
        $totalInventory = DB::table('product_variant_inventory')
            ->sum('quantity');

        // Total outstanding balance
        $totalOutstanding = Invoice::where('status', '!=', 'CLEARED')
            ->sum('balance_amount');

        // Total sales this month
        $totalSalesThisMonth = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->where('status', '!=', 'DRAFT')
            ->sum('grand_total');

        // Low stock products (less than 10 units)
        $lowStockProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('product_variant_inventory', 'product_variants.id', '=', 'product_variant_inventory.product_variant_id')
            ->selectRaw('products.id, products.style_id, products.name, SUM(product_variant_inventory.quantity) as total_quantity')
            ->groupBy('products.id', 'products.style_id', 'products.name')
            ->havingRaw('SUM(product_variant_inventory.quantity) < 10')
            ->get();

        // Recent invoices (last 10)
        $recentInvoices = Invoice::with('customer')
            ->orderBy('invoice_date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalInventory',
            'totalOutstanding',
            'totalSalesThisMonth',
            'lowStockProducts',
            'recentInvoices'
        ));
    }
}
