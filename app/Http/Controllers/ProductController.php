<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsManagementList;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use ExportsManagementList;

    /**
     * Product list.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));

        $products = $this->filteredQuery($request)
            ->paginate(9)
            ->withQueryString();

        $filterCategories = Product::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $filterSubcategories = Product::whereNotNull('subcategory')
            ->distinct()
            ->pluck('subcategory')
            ->filter()
            ->values();

        return view('products.index', compact(
            'products',
            'filterCategories',
            'filterSubcategories',
            'search',
            'filter'
        ));
    }

    public function exportExcel(Request $request)
    {
        $headers = ['Product', 'Category', 'Sub Category', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(Product $product) => [
                $product->product_name,
                $product->category,
                $product->subcategory ?: '—',
                ucfirst($product->status),
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'products-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Product', 'Category', 'Sub Category', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn(Product $product) => [
                $product->product_name,
                $product->category,
                $product->subcategory ?: '—',
                ucfirst($product->status),
            ]
        );

        return $this->exportPdfResponse(
            'Products List',
            $headers,
            $rows,
            'products-list',
            'Retail inventory export'
        );
    }

    /**
     * Store product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:150',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'subcategory' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product added successfully.'
            );
    }

    /**
     * Update product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:150',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'subcategory' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    /**
     * Toggle active/inactive.
     */
    public function toggleStatus(Product $product)
    {
        $newStatus = $product->status === 'active'
            ? 'inactive'
            : 'active';

        $product->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active'
                ? 'Product activated successfully.'
                : 'Product deactivated successfully.',
        ]);
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = trim($request->input('filter', ''));
        $name = trim($request->input('name', ''));
        $category = trim($request->input('category', ''));
        $subcategory = trim($request->input('subcategory', ''));
        $status = trim($request->input('status', $filter));

        return Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'product_name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'category',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'subcategory',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->when($name !== '', fn($q) => $q->where('product_name', 'like', "%{$name}%"))
            ->when($category !== '', fn($q) => $q->where('category', $category))
            ->when($subcategory !== '', fn($q) => $q->where('subcategory', $subcategory))
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest();
    }



    /**
     * Store a purchase for a product.
     * Price is taken from the product record — never trusted from the client.
     */
    public function storePurchase(Request $request, Product $product)
    {
        $validated = $request->validate([
            'purchase_date' => [
                'required',
                'date',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999999999',
            ],
        ]);

        $purchase = $product->purchases()->create($validated);

        $pricePerUnit = (float) $product->price;
        $totalAmount  = $pricePerUnit * $purchase->quantity;

        // Return the new purchases_count so the UI can update without a reload.
        $purchasesCount = $product->purchases()->count();

        return response()->json([
            'success'         => true,
            'message'         => 'Purchase recorded successfully.',
            'purchases_count' => $purchasesCount,
            'purchase'        => [
                'id'             => $purchase->id,
                'purchase_date'  => $purchase->purchase_date->format('d/m/Y'),
                'quantity'       => $purchase->quantity,
                'price_per_unit' => number_format($pricePerUnit, 2),
                'total_amount'   => number_format($totalAmount, 2),
            ],
        ]);
    }

    /**
     * Purchase history for a product.
     * All monetary values are derived from products.price — no stored price per row.
     */
    public function purchaseHistory(Product $product)
    {
        $purchases = $product->purchases()
            ->latest('purchase_date')
            ->latest('id')
            ->get();

        $pricePerUnit   = (float) $product->price;
        $totalPurchases = $purchases->count();
        $totalQuantity  = $purchases->sum('quantity');
        $totalAmount    = $purchases->sum(fn ($p) => $p->quantity * $pricePerUnit);

        return response()->json([
            'success' => true,

            'product' => [
                'id'          => $product->id,
                'name'        => $product->product_name,
                'category'    => $product->category,
                'subcategory' => $product->subcategory,
                'price'       => number_format($pricePerUnit, 2),
            ],

            'summary' => [
                'total_purchases' => $totalPurchases,
                'total_quantity'  => $totalQuantity,
                'total_amount'    => number_format($totalAmount, 2),
            ],

            'purchases' => $purchases->map(function ($purchase) use ($pricePerUnit) {
                return [
                    'id'             => $purchase->id,
                    'purchase_date'  => $purchase->purchase_date->format('d M Y'),
                    'quantity'       => $purchase->quantity,
                    'price_per_unit' => number_format($pricePerUnit, 2),
                    'total_amount'   => number_format($purchase->quantity * $pricePerUnit, 2),
                ];
            })->values(),
        ]);
    }
}
