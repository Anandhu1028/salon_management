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

        return view('products.index', compact(
            'products',
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
            ->when(in_array($filter, ['active', 'inactive'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->latest();
    }
}