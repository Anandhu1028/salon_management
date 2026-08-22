<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range');
        $query = Expense::with(['category', 'staff'])->latest('expense_date');

        if ($range === 'today') $query->whereDate('expense_date', today());
        if ($range === 'week') $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
        if ($range === 'month') $query->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year);

        foreach (['expense_category_id', 'staff_id', 'payment_method'] as $field) {
            if ($request->filled($field)) $query->where($field, $request->$field);
        }

        if ($request->filled('from')) $query->whereDate('expense_date', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('expense_date', '<=', $request->to);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('staff', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        return view('expenses.index', [
            'expenses' => $query->paginate(12)->withQueryString(),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'staff' => Staff::where('status', 'active')->orderBy('name')->get(),
            'monthTotal' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'monthCount' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->count(),
            'categoriesCount' => ExpenseCategory::count(),
            'total' => Expense::sum('amount'),
        ]);
    }

    public function store(Request $request)
    {
        Expense::create($this->rules($request));
        return back()->with('success', 'Expense recorded successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($this->rules($request));
        return back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted successfully.');
    }

    public function storeCategory(Request $request)
    {
        ExpenseCategory::create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]));
        return back()->with('success', 'Expense category added successfully.');
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'staff_id' => ['nullable', 'exists:staff,id'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['Cash', 'UPI', 'Card', 'Bank Transfer', 'Other'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}