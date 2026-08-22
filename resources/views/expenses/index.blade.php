@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Expenses')
@section('content')

<style>
    :root{
        --exp-purple:#6d5ef8;
        --exp-purple-light:#efeafe;
        --exp-blue:#3b82f6;
        --exp-blue-light:#e8f0fe;
        --exp-green:#10b981;
        --exp-green-light:#e6f9f2;
        --exp-orange:#f59e0b;
        --exp-orange-light:#fef3e2;
    }
    .exp-stat-card{
        border:none;border-radius:16px;padding:20px;position:relative;overflow:hidden;
        background:#fff;box-shadow:0 1px 3px rgba(16,24,40,.06);height:100%;
    }
    .exp-stat-icon{
        width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;
        font-size:18px;color:#fff;
    }
    .exp-stat-label{font-size:.72rem;letter-spacing:.04em;text-transform:uppercase;color:#8a8fa3;font-weight:600;}
    .exp-stat-value{font-size:1.6rem;font-weight:700;color:#1f2430;margin-top:4px;}
    .exp-stat-sub{font-size:.78rem;color:#9aa0b4;}
    .exp-stat-dot{position:absolute;top:14px;right:14px;width:8px;height:8px;border-radius:50%;}

    .exp-filter-bar{background:#fff;border-radius:14px;padding:14px 16px;box-shadow:0 1px 3px rgba(16,24,40,.06);}
    .exp-filter-bar .form-select,.exp-filter-bar .form-control{
        border-radius:10px;border-color:#e7e8f0;font-size:.9rem;
    }
    .exp-search-input{border-radius:10px;border-color:#e7e8f0;}

    .exp-table-card{border:none;border-radius:16px;box-shadow:0 1px 3px rgba(16,24,40,.06);overflow:hidden;}
    .exp-table-card table thead th{
        border-bottom:1px solid #eef0f5;background:#fafafe;color:#8a8fa3;font-size:.7rem;
        letter-spacing:.05em;text-transform:uppercase;font-weight:700;padding:14px 16px;white-space:nowrap;
    }
    .exp-table-card table tbody td{padding:14px 16px;vertical-align:middle;border-bottom:1px solid #f3f4f8;font-size:.88rem;}
    .exp-table-card table tbody tr:last-child td{border-bottom:none;}
    .exp-table-card table tbody tr:hover{background:#fafbff;}

    .exp-date-cell .exp-time{display:block;font-size:.75rem;color:#9aa0b4;}
    .exp-badge{
        display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;
        font-size:.78rem;font-weight:600;
    }
    .exp-avatar{
        width:34px;height:34px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;
        color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;
    }
    .exp-staff-name{font-weight:600;color:#1f2430;font-size:.85rem;}
    .exp-staff-role{font-size:.74rem;color:#9aa0b4;}
    .exp-desc-main{color:#1f2430;}
    .exp-desc-sub{font-size:.76rem;color:#9aa0b4;}
    .exp-amount{font-weight:700;color:#1f2430;}
    .exp-pay{display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;}
    .exp-pay-dot{width:8px;height:8px;border-radius:50%;display:inline-block;}

    .exp-action-btn{
        width:32px;height:32px;border-radius:8px;border:1px solid #eef0f5;background:#fff;
        display:inline-flex;align-items:center;justify-content:center;color:#6b7280;
    }
    .exp-action-btn:hover{background:#f5f6fb;}

    .exp-pagination .page-link{border-radius:8px;margin:0 2px;border:1px solid #e7e8f0;color:#4b5165;}
    .exp-pagination .page-item.active .page-link{background:var(--exp-purple);border-color:var(--exp-purple);}
</style>

<div class="container-fluid py-3">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold">Expense Management</h4>
            <small class="text-muted">This month: ₹{{ number_format($monthTotal,2) }} · Total: ₹{{ number_format($total,2) }}</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary rounded-3" data-bs-toggle="modal" data-bs-target="#categoryModal">Add category</button>
            <button class="btn btn-primary rounded-3" style="background:var(--exp-purple);border-color:var(--exp-purple);" onclick="openExpense()">+ Add expense</button>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="exp-stat-card">
                <span class="exp-stat-dot" style="background:var(--exp-purple);"></span>
                <div class="exp-stat-icon mb-2" style="background:var(--exp-purple);">₹</div>
                <div class="exp-stat-label">This Month</div>
                <div class="exp-stat-value">₹{{ number_format($monthTotal,2) }}</div>
                <div class="exp-stat-sub">Expenses this month</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="exp-stat-card">
                <span class="exp-stat-dot" style="background:var(--exp-blue);"></span>
                <div class="exp-stat-icon mb-2" style="background:var(--exp-blue);">Σ</div>
                <div class="exp-stat-label">Total Expenses</div>
                <div class="exp-stat-value">₹{{ number_format($total,2) }}</div>
                <div class="exp-stat-sub">All time expenses</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="exp-stat-card">
                <span class="exp-stat-dot" style="background:var(--exp-green);"></span>
                <div class="exp-stat-icon mb-2" style="background:var(--exp-green);">＋</div>
                <div class="exp-stat-label">Categories</div>
                <div class="exp-stat-value">{{ $categoriesCount }}</div>
                <div class="exp-stat-sub">Total categories</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="exp-stat-card">
                <span class="exp-stat-dot" style="background:var(--exp-orange);"></span>
                <div class="exp-stat-icon mb-2" style="background:var(--exp-orange);">👤</div>
                <div class="exp-stat-label">This Month Count</div>
                <div class="exp-stat-value">{{ $monthCount }}</div>
                <div class="exp-stat-sub">Total transactions</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form class="exp-filter-bar row g-2 align-items-center mb-3">
        <div class="col-auto"><select name="range" class="form-select"><option value="">All dates</option><option value="today" @selected(request('range')=='today')>Today</option><option value="week" @selected(request('range')=='week')>This week</option><option value="month" @selected(request('range')=='month')>This month</option></select></div>
        <div class="col-auto"><select name="expense_category_id" class="form-select"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('expense_category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-auto"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-auto"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-auto flex-grow-1"><input type="text" name="search" value="{{ request('search') }}" class="form-control exp-search-input" placeholder="Search expenses..."></div>
        <div class="col-auto"><button class="btn btn-outline-primary rounded-3">Filter</button></div>
    </form>

    {{-- Table --}}
    <div class="exp-table-card">
        <div class="d-flex justify-content-between align-items-center px-3 pt-3">
            <div>
                <div class="fw-bold">Expense List</div>
                <small class="text-muted">{{ $expenses->total() }} total expenses</small>
            </div>
        </div>
        <div class="table-responsive mt-2">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th><th>Date</th><th>Category</th><th>Staff</th><th>Description</th><th>Amount</th><th>Payment</th><th></th>
                </tr>
                </thead>
                <tbody>
                @php
                    $badgePalette = [
                        ['bg' => '#efeafe', 'text' => '#6d5ef8'],
                        ['bg' => '#fef3e2', 'text' => '#f59e0b'],
                        ['bg' => '#e8f0fe', 'text' => '#3b82f6'],
                        ['bg' => '#fde8ee', 'text' => '#ef4477'],
                        ['bg' => '#e6f9f2', 'text' => '#10b981'],
                        ['bg' => '#fff2e0', 'text' => '#f97316'],
                    ];
                    $avatarPalette = ['#6d5ef8', '#3b82f6', '#10b981', '#f59e0b', '#ef4477', '#0ea5e9'];
                    $payMeta = [
                        'Cash' => ['color' => '#10b981', 'icon' => '💵'],
                        'UPI' => ['color' => '#3b82f6', 'icon' => '📱'],
                        'Card' => ['color' => '#f59e0b', 'icon' => '💳'],
                        'Bank Transfer' => ['color' => '#6d5ef8', 'icon' => '🏦'],
                        'Other' => ['color' => '#9aa0b4', 'icon' => '⋯'],
                    ];
                @endphp
                @forelse($expenses as $expense)
                    @php
                        $catColor = $badgePalette[$expense->expense_category_id % count($badgePalette)];
                        $avColor = $avatarPalette[($expense->staff_id ?? 0) % count($avatarPalette)];
                        $pay = $payMeta[$expense->payment_method] ?? ['color' => '#9aa0b4', 'icon' => '⋯'];
                        $initial = $expense->staff ? strtoupper(substr($expense->staff->name, 0, 1)) : '—';
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="exp-date-cell">
                            {{ $expense->expense_date->format('d M Y') }}
                            <span class="exp-time">{{ $expense->created_at?->format('h:i A') }}</span>
                        </td>
                        <td>
                            <span class="exp-badge" style="background:{{ $catColor['bg'] }};color:{{ $catColor['text'] }};">{{ $expense->category->name }}</span>
                        </td>
                        <td>
                            @if($expense->staff)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="exp-avatar" style="background:{{ $avColor }};">{{ $initial }}</span>
                                    <div>
                                        <div class="exp-staff-name">{{ $expense->staff->name }}</div>
                                        <div class="exp-staff-role">{{ $expense->staff->role ?? $expense->staff->position ?? '' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="exp-desc-main">{{ $expense->description ?? '—' }}</div>
                            @if($expense->notes)<div class="exp-desc-sub">{{ \Illuminate\Support\Str::limit($expense->notes, 40) }}</div>@endif
                        </td>
                        <td class="exp-amount">₹{{ number_format($expense->amount,2) }}</td>
                        <td>
                            <span class="exp-pay" style="color:{{ $pay['color'] }};">
                                <span>{{ $pay['icon'] }}</span>{{ $expense->payment_method }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="exp-action-btn" data-bs-toggle="dropdown">⋮</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button class="dropdown-item" onclick='openExpense(@json($expense))'>Edit</button></li>
                                    <li>
                                        <form action="{{ route('expenses.destroy',$expense) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No expenses found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center px-3 py-3">
            <small class="text-muted">Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries</small>
            <div class="exp-pagination">{{ $expenses->links() }}</div>
        </div>
    </div>
</div>

{{-- Expense modal --}}
<div class="modal fade" id="expenseModal"><div class="modal-dialog"><form class="modal-content" id="expenseForm" method="POST" action="{{ route('expenses.store') }}">@csrf <input id="expenseMethod" type="hidden" name="_method"><div class="modal-header"><h5 id="expenseTitle">Add Expense</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body row g-3"><div class="col-6"><label>Date</label><input class="form-control" id="expenseDate" type="date" name="expense_date" required></div><div class="col-6"><label>Category</label><select class="form-select" id="expenseCategory" name="expense_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" data-staff="{{ in_array($category->name,['Staff Salary','OT Staff','Staff Incentive']) }}">{{ $category->name }}</option>@endforeach</select></div><div class="col-12" id="expenseStaffWrap"><label>Staff</label><select class="form-select" id="expenseStaff" name="staff_id"><option value="">Not applicable</option>@foreach($staff as $member)<option value="{{ $member->id }}">{{ $member->name }}</option>@endforeach</select></div><div class="col-12"><label>Description</label><input class="form-control" id="expenseDescription" name="description"></div><div class="col-6"><label>Amount</label><input class="form-control" id="expenseAmount" type="number" step="0.01" name="amount" required></div><div class="col-6"><label>Payment</label><select class="form-select" id="expensePayment" name="payment_method" required>@foreach(['Cash','UPI','Card','Bank Transfer','Other'] as $method)<option>{{ $method }}</option>@endforeach</select></div><div class="col-12"><label>Notes</label><textarea class="form-control" id="expenseNotes" name="notes"></textarea></div></div><div class="modal-footer"><button class="btn btn-primary">Save expense</button></div></form></div></div>

{{-- Category modal --}}
<div class="modal fade" id="categoryModal"><div class="modal-dialog"><form class="modal-content" method="POST" action="{{ route('expense-categories.store') }}">@csrf <div class="modal-header"><h5>Add Expense Category</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input class="form-control" name="name" placeholder="Category name" required><input type="hidden" name="status" value="active"></div><div class="modal-footer"><button class="btn btn-primary">Add</button></div></form></div></div>

@push('scripts')
<script>
const expenseModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('expenseModal'));
function syncStaff(){document.getElementById('expenseStaffWrap').style.display=document.getElementById('expenseCategory').selectedOptions[0].dataset.staff==='1'?'block':'none'}
document.getElementById('expenseCategory').onchange=syncStaff;
function openExpense(e=null){
    const f=document.getElementById('expenseForm');
    f.reset();
    document.getElementById('expenseMethod').value=e?'PUT':'';
    f.action=e?`/expenses/${e.id}`:`{{ route('expenses.store') }}`;
    document.getElementById('expenseTitle').textContent=e?'Edit Expense':'Add Expense';
    document.getElementById('expenseDate').value=e?e.expense_date:'{{ now()->toDateString() }}';
    if(e){
        ['Category','Staff','Description','Amount','Payment','Notes'].forEach(x=>document.getElementById('expense'+x).value=e[{Category:'expense_category_id',Staff:'staff_id',Description:'description',Amount:'amount',Payment:'payment_method',Notes:'notes'}[x]]??'')
    }
    syncStaff();
    expenseModal.show();
}
</script>
@endpush
@endsection