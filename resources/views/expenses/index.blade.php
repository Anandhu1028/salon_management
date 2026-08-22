@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Expenses')
@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management/module-lists.css') }}">
<link rel="stylesheet" href="{{ asset('css/job-card/job-card.css') }}">
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
@endpush

<div class="expense-page management-page">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
       
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary rounded-3" data-bs-toggle="modal" data-bs-target="#categoryModal">Add category</button>
            <button class="btn btn-primary rounded-3" style="background:var(--exp-purple);border-color:var(--exp-purple);" onclick="openExpense()">+ Add expense</button>
        </div>
    </div>

    {{-- Premium Statistics --}}
    <div class="mgmt-stats-grid mgmt-stats-grid--4">
        @include('partials.mgmt-stat-card', [
            'theme' => 'indigo',
            'icon' => 'rupee-green',
            'label' => 'This Month',
            'value' => '₹' . number_format($monthTotal, 2),
            'subtext' => 'Expenses this month',
            'sparkColor' => '#6366F1',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'blue',
            'icon' => 'clipboard-cyan',
            'label' => 'Total Expenses',
            'value' => '₹' . number_format($total, 2),
            'subtext' => 'All time expenses',
            'sparkColor' => '#3B82F6',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'box-blue',
            'label' => 'Categories',
            'value' => $categoriesCount,
            'subtext' => 'Total categories',
            'sparkColor' => '#22C55E',
            'trend' => '0.0',
            'trendUp' => true,
        ])
        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'calendar-orange',
            'label' => 'This Month Count',
            'value' => $monthCount,
            'subtext' => 'Total transactions',
            'sparkColor' => '#F59E0B',
            'trend' => '0.0',
            'trendUp' => true,
        ])
    </div>

    {{-- Filter bar --}}
    <form class="exp-filter-bar row g-2 align-items-center mb-3">
        <div class="col-auto"><select name="range" class="form-select"><option value="">All dates</option><option value="today" @selected(request('range')=='today')>Today</option><option value="week" @selected(request('range')=='week')>This week</option><option value="month" @selected(request('range')=='month')>This month</option></select></div>
        <div class="col-auto"><select name="expense_category_id" class="form-select"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('expense_category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-auto"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-auto"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-auto"><button class="btn btn-outline-primary rounded-3">Filter</button></div>
    </form>

    {{-- Premium Expense List --}}
    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h2>Expense List</h2>
                <span>{{ $expenses->total() }} total expenses</span>
            </div>
            <form method="GET" action="{{ route('expenses.index') }}" class="module-list-search">
                @foreach(request()->except('search','page') as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search expenses...">
                    @if(request('search'))
                        <a href="{{ route('expenses.index', request()->except('search','page')) }}" class="text-muted" title="Clear search">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($expenses->count())
            @php
                $listStart = ($expenses->currentPage() - 1) * $expenses->perPage();
                $badgePalette = [
                    ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'class' => 'module-pill--indigo'],
                    ['bg' => '#FFF7ED', 'text' => '#C2410C', 'class' => 'module-pill--orange'],
                    ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'class' => 'module-pill--blue'],
                    ['bg' => '#FDF2F8', 'text' => '#BE185D', 'class' => 'module-pill--pink'],
                    ['bg' => '#F0FDF4', 'text' => '#15803D', 'class' => 'module-pill--green'],
                ];
                $avatarPalette = ['#6366F1','#3B82F6','#10B981','#F59E0B','#EC4899','#0EA5E9'];
                $payMeta = [
                    'Cash' => ['color' => '#16A34A', 'icon' => 'bi-cash'],
                    'UPI' => ['color' => '#2563EB', 'icon' => 'bi-phone'],
                    'Card' => ['color' => '#7C3AED', 'icon' => 'bi-credit-card'],
                    'Bank Transfer' => ['color' => '#0891B2', 'icon' => 'bi-bank'],
                    'Other' => ['color' => '#64748B', 'icon' => 'bi-wallet2'],
                ];
            @endphp

            <div class="premium-list premium-list--expenses">
                <div class="premium-list-head" style="grid-template-columns: 44px 120px minmax(130px,1fr) minmax(160px,1.1fr) minmax(180px,1.35fr) 120px 120px 70px; min-width:1100px;">
                    <span class="pli-head-cell text-center">#</span>
                    <span class="pli-head-cell text-center">Date</span>
                    <span class="pli-head-cell text-center">Category</span>
                    <span class="pli-head-cell text-center">Staff</span>
                    <span class="pli-head-cell text-center">Description</span>
                    <span class="pli-head-cell text-center">Amount</span>
                    <span class="pli-head-cell text-center">Payment</span>
                    <span class="pli-head-cell text-center">Actions</span>
                </div>

                @foreach($expenses as $expense)
                    @php
                        $catColor = $badgePalette[$expense->expense_category_id % count($badgePalette)];
                        $avColor = $avatarPalette[($expense->staff_id ?? 0) % count($avatarPalette)];
                        $pay = $payMeta[$expense->payment_method] ?? $payMeta['Other'];
                        $initial = $expense->staff ? strtoupper(substr($expense->staff->name, 0, 1)) : '?';
                    @endphp

                    <article class="premium-list-item expense-list-row"
                             id="expense-row-{{ $expense->id }}"
                             style="grid-template-columns: 44px 120px minmax(130px,1fr) minmax(160px,1.1fr) minmax(180px,1.35fr) 120px 120px 70px; min-width:1100px;">
                        <div class="pli-rank">{{ $listStart + $loop->iteration }}</div>

                        <div class="pli-col justify-content-center">
                            <div class="text-center">
                                <span class="purchase-date-main" style="display:block;font-size:.76rem;font-weight:700;color:#0F172A;white-space:nowrap;">
                                    <i class="bi bi-calendar3 me-1" style="color:#94A3B8"></i>{{ $expense->expense_date->format('d M Y') }}
                                </span>
                                <span class="module-meta">{{ $expense->created_at?->format('h:i A') }}</span>
                            </div>
                        </div>

                        <div class="pli-col justify-content-center">
                            <span class="module-pill {{ $catColor['class'] }}" title="{{ $expense->category->name }}">
                                <i class="bi bi-tag"></i>{{ $expense->category->name }}
                            </span>
                        </div>

                        <div class="pli-col justify-content-center">
                            @if($expense->staff)
                                <div class="module-person">
                                    <span class="module-avatar" style="background:linear-gradient(135deg,{{ $avColor }},{{ $avColor }}CC);">{{ $initial }}</span>
                                    <span class="module-person-info">
                                        <span class="module-person-name">{{ $expense->staff->name }}</span>
                                        <span class="module-person-sub">{{ $expense->staff->role ?? $expense->staff->position ?? 'Staff member' }}</span>
                                    </span>
                                </div>
                            @else
                                <span class="module-pill module-pill--slate">Not assigned</span>
                            @endif
                        </div>

                        <div class="pli-col justify-content-center">
                            <div style="width:100%;min-width:0;text-align:center;">
                                <span class="pli-col-text" style="font-size:.79rem;font-weight:600;color:#0F172A;" title="{{ $expense->description }}">{{ $expense->description ?: '—' }}</span>
                                @if($expense->notes)
                                    <span class="module-meta" title="{{ $expense->notes }}">{{ \Illuminate\Support\Str::limit($expense->notes, 42) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="pli-col justify-content-center">
                            <span class="module-amount">₹{{ number_format($expense->amount,2) }}</span>
                        </div>

                        <div class="pli-col justify-content-center">
                            <span class="module-pill module-pill--slate" style="color:{{ $pay['color'] }};background:#fff;border-color:#E2E8F0;">
                                <i class="bi {{ $pay['icon'] }}"></i>{{ $expense->payment_method }}
                            </span>
                        </div>

                        <div class="pli-col pli-col-actions">
                            <div class="module-action-menu-wrap">
                                <button type="button" class="module-action-dots" onclick="toggleExpenseActions(this)" aria-label="Expense actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="module-action-popover">
                                    <button type="button" class="module-popover-action"
                                            onclick='openExpense(@json($expense)); closeExpenseActions(this)'>
                                        <span class="module-popover-icon module-popover-icon--edit"><i class="bi bi-pencil"></i></span>
                                        <span>Edit Expense</span>
                                    </button>
                                    <div class="module-popover-divider"></div>
                                    <form method="POST" action="{{ route('expenses.destroy',$expense) }}">
                                        @csrf @method('DELETE')
                                        <button class="module-popover-action" type="submit" onclick="return confirm('Delete this expense?')">
                                            <span class="module-popover-icon module-popover-icon--delete"><i class="bi bi-trash3"></i></span>
                                            <span>Delete Expense</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2">
                <small class="text-muted">
                    Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries
                </small>
                <div class="exp-pagination">{{ $expenses->links() }}</div>
            </div>
        @else
            <div class="module-empty-state">
                <div class="empty-icon"><i class="bi bi-wallet2"></i></div>
                <h3>No expenses found</h3>
                <p>Record your first expense transaction to see it here.</p>
                <button class="btn btn-primary mt-3" style="background:linear-gradient(135deg,#6366F1,#4F46E5);border:0;" onclick="openExpense()">
                    <i class="bi bi-plus-lg me-1"></i> Add Expense
                </button>
            </div>
        @endif
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

function toggleExpenseActions(button) {
    const wrap = button.closest('.module-action-menu-wrap');
    document.querySelectorAll('.module-action-menu-wrap.is-open').forEach(item => {
        if (item !== wrap) item.classList.remove('is-open');
    });
    wrap?.classList.toggle('is-open');
}
function closeExpenseActions(element) {
    element.closest('.module-action-menu-wrap')?.classList.remove('is-open');
}
document.addEventListener('click', function(event) {
    if (!event.target.closest('.module-action-menu-wrap')) {
        document.querySelectorAll('.module-action-menu-wrap.is-open')
            .forEach(item => item.classList.remove('is-open'));
    }
});

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