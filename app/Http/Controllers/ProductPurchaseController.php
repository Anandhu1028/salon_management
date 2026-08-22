<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $dateFrom = trim($request->input('date_from', ''));
        $dateTo = trim($request->input('date_to', ''));
        $customerId = trim($request->input('customer_id', ''));
        $paymentTypeId = trim($request->input('payment_type_id', ''));
        $productId = trim($request->input('product_id', ''));

        $query = ProductPurchase::with(['customer', 'paymentType', 'items.product'])
            ->when($search !== '', function ($purchaseQuery) use ($search) {
                $purchaseQuery->where(function ($purchase) use ($search) {
                    $purchase->where('purchase_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer
                            ->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('items.product', fn ($product) => $product
                            ->where('product_name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('subcategory', 'like', "%{$search}%"));
                });
            })
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('purchase_date', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('purchase_date', '<=', $dateTo))
            ->when($customerId !== '', fn ($query) => $query->where('customer_id', $customerId))
            ->when($paymentTypeId !== '', fn ($query) => $query->where('payment_type_id', $paymentTypeId))
            ->when($productId !== '', fn ($query) => $query->whereHas('items', fn ($items) => $items->where('product_id', $productId)));

        $thisMonth = ProductPurchase::whereYear('purchase_date', now()->year)
            ->whereMonth('purchase_date', now()->month);

        return view('product-purchases.index', [
            'purchases' => $query->latest('purchase_date')->latest('id')->paginate(12)->withQueryString(),
            'products' => Product::where('status', 'active')->orderBy('product_name')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'paymentTypes' => PaymentType::orderBy('name')->get(),
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'customerId' => $customerId,
            'paymentTypeId' => $paymentTypeId,
            'productId' => $productId,
            'totalPurchases' => ProductPurchase::count(),
            'totalSpent' => ProductPurchase::sum('total_amount'),
            'totalProducts' => ProductPurchaseItem::distinct('product_id')->count('product_id'),
            'monthSpent' => $thisMonth->sum('total_amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePurchase($request);

        DB::transaction(function () use ($data) {
            $purchase = ProductPurchase::create([
                'purchase_number' => $this->nextPurchaseNumber(),
                'customer_id' => $data['customer_id'],
                'purchase_date' => now()->toDateString(),
                'payment_type_id' => $data['payment_type_id'],
                'total_amount' => 0,
            ]);

            $total = $this->syncItems($purchase, $data['products']);

            $purchase->update(['total_amount' => $total]);
        });

        return back()->with('success', 'Purchase recorded successfully.');
    }

    public function update(Request $request, ProductPurchase $productPurchase): RedirectResponse
    {
        $data = $this->validatePurchase($request);

        DB::transaction(function () use ($data, $productPurchase) {
            $productPurchase->update([
                'customer_id' => $data['customer_id'],
                'payment_type_id' => $data['payment_type_id'],
                // purchase_date is intentionally left untouched — the UI
                // never exposes it for editing.
            ]);

            // Simplest correct way to keep items in sync with an
            // arbitrarily added/removed/edited row set: replace them.
            $productPurchase->items()->delete();

            $total = $this->syncItems($productPurchase, $data['products']);

            $productPurchase->update(['total_amount' => $total]);
        });

        return back()->with('success', 'Purchase updated successfully.');
    }

    public function destroy(ProductPurchase $productPurchase): RedirectResponse
    {
        DB::transaction(function () use ($productPurchase) {
            // Explicit delete in addition to the FK's cascadeOnDelete, so
            // this stays correct even if the DB constraint is ever missing.
            $productPurchase->items()->delete();
            $productPurchase->delete();
        });

        return back()->with('success', 'Purchase deleted successfully.');
    }

    /**
     * Create ProductPurchaseItem rows for a purchase from validated product
     * rows, computing every line total server-side. Returns the purchase
     * total (sum of line totals) — the frontend's numbers are never trusted.
     */
    private function syncItems(ProductPurchase $purchase, array $products): float
    {
        $total = 0.0;

        foreach ($products as $row) {
            $quantity = (int) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            $lineTotal = round($quantity * $unitPrice, 2);

            ProductPurchaseItem::create([
                'product_purchase_id' => $purchase->id,
                'product_id' => $row['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $lineTotal,
            ]);

            $total += $lineTotal;
        }

        return round($total, 2);
    }

    private function validatePurchase(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    /**
     * Transaction-safe sequential purchase number (PUR-001, PUR-002, ...),
     * consistent with the existing application's approach.
     */
    private function nextPurchaseNumber(): string
    {
        $next = (int) ProductPurchase::lockForUpdate()->max('id') + 1;

        return 'PUR-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
