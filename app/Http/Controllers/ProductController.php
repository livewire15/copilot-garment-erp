<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantInventory;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by style ID
        if ($request->filled('style_id')) {
            $query->where('style_id', 'like', '%' . $request->style_id . '%');
        }

        // Search by product name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $products = $query->paginate(15);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product = Product::create($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product with its variants and inventory.
     */
    public function show(Product $product)
    {
        $product->load('variants.inventory');

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load('variants.inventory');

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product->update($data);

        // Update or create variants with inventory
        $this->updateVariants($product, $request->input('variants', []));

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Update product variants and their inventory.
     */
    private function updateVariants(Product $product, array $variants)
    {
        foreach ($variants as $variantData) {
            if (empty($variantData['color'])) {
                continue;
            }

            $variant = ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'color' => $variantData['color'],
                ],
                [
                    'product_id' => $product->id,
                    'color' => $variantData['color'],
                ]
            );

            // Update inventory for each size
            if (isset($variantData['inventory'])) {
                foreach ($variantData['inventory'] as $size => $quantity) {
                    if ($quantity === '' || $quantity === null) {
                        continue;
                    }

                    ProductVariantInventory::updateOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'size' => $size,
                        ],
                        [
                            'quantity' => (int) $quantity,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Get product details as JSON for AJAX.
     */
    public function getProduct(Product $product)
    {
        return response()->json([
            'name' => $product->name,
            'selling_price' => $product->selling_price,
            'variants' => $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'inventory' => $variant->inventory->pluck('quantity', 'size')->toArray(),
                ];
            }),
        ]);
    }
}
