<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $query = ProductPurchase::with('product')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($purchase) use ($search) {
                    $purchase->where('purchase_number', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($product) => $product
                            ->where('product_name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('subcategory', 'like', "%{$search}%"));
                });
            });

        $allPurchases = ProductPurchase::query();
        $thisMonth = ProductPurchase::whereYear('purchase_date', now()->year)
            ->whereMonth('purchase_date', now()->month);

        return view('product-purchases.index', [
            'purchases' => $query->latest('purchase_date')->latest('id')->paginate(12)->withQueryString(),
            'products' => Product::where('status', 'active')->orderBy('product_name')->get(),
            'search' => $search,
            'totalPurchases' => $allPurchases->count(),
            'totalSpent' => ProductPurchase::sum('total_amount'),
            'totalProducts' => ProductPurchase::distinct('product_id')->count('product_id'),
            'monthSpent' => $thisMonth->sum('total_amount'),
        ]);
    }
    public function store(Request $request)
    {
        $data = $this->rules($request);
        return DB::transaction(function () use ($data) {
            $next = (int) ProductPurchase::lockForUpdate()->max('id') + 1;
            $data['purchase_number'] = 'PUR-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $data['total_amount'] = $data['quantity'] * $data['unit_price'];
            ProductPurchase::create($data);
            return back()->with('success', 'Purchase recorded successfully.');
        });
    }
    public function update(Request $request, ProductPurchase $productPurchase) { $data = $this->rules($request); $data['total_amount'] = $data['quantity'] * $data['unit_price']; $productPurchase->update($data); return back()->with('success', 'Purchase updated successfully.'); }
    public function destroy(ProductPurchase $productPurchase) { $productPurchase->delete(); return back()->with('success', 'Purchase deleted successfully.'); }
    private function rules(Request $request): array { return $request->validate(['product_id' => ['required','exists:products,id'], 'purchase_date' => ['required','date'], 'quantity' => ['required','integer','min:1'], 'unit_price' => ['required','numeric','min:0'], 'payment_method' => ['required', Rule::in(['Cash','UPI','Card','Bank Transfer','Other'])], 'notes' => ['nullable','string','max:2000']]); }
}
