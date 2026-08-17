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
        $headers = ['Product', 'Category', 'Sub Category', 'Price', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Product $product) => [
                $product->product_name,
                $product->category,
                $product->subcategory ?: '—',
                number_format((float) $product->price, 2),
                ucfirst($product->status),
            ]
        );

        return $this->exportCsvResponse($headers, $rows, 'products-list');
    }

    public function exportPdf(Request $request)
    {
        $headers = ['Product', 'Category', 'Sub Category', 'Price', 'Status'];
        $rows = $this->mapRowsFromQuery(
            $this->filteredQuery($request),
            fn (Product $product) => [
                $product->product_name,
                $product->category,
                $product->subcategory ?: '—',
                '₹' . number_format((float) $product->price, 2),
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

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
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
    public function update( Request $request, Product $product ) {
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

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
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
        $priceRange = $request->input('price_range');
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
            ->when($name !== '', fn ($q) => $q->where('product_name', 'like', "%{$name}%"))
            ->when($category !== '', fn ($q) => $q->where('category', $category))
            ->when($subcategory !== '', fn ($q) => $q->where('subcategory', $subcategory))
            ->when(!empty($priceRange) && $priceRange !== 'all', function ($query) use ($priceRange) {
                match ($priceRange) {
                    'under_500' => $query->where('price', '<', 500),
                    '500_1000' => $query->whereBetween('price', [500, 1000]),
                    '1001_2500' => $query->whereBetween('price', [1001, 2500]),
                    '2501_5000' => $query->whereBetween('price', [2501, 5000]),
                    'above_5000' => $query->where('price', '>', 5000),
                    default => null,
                };
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest();
    }
}