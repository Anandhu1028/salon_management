@extends('layouts.app')

@section('title', 'Daily Marketing')
@section('page-title', 'Daily Marketing')

@push('styles')
<style>

    /* ==========================================================
       MARKETING TABLE — 7 COLUMNS (# TYPE DATE LOCATION COUNT STAFF ACTION)
       ========================================================== */

    .premium-list--marketing {
    --marketing-grid:
        48px
        minmax(220px, 1.55fr)
        minmax(145px, .9fr)
        minmax(150px, 1fr)
        105px
        minmax(150px, 1fr)
        90px;
    }

    .premium-list--marketing .premium-list-head,
    .premium-list--marketing .premium-list-item {
        display: grid !important;
        grid-template-columns: var(--marketing-grid) !important;
        align-items: center !important;
        column-gap: 20px !important;
        width: 100%;
        min-width: 900px;
    }

    .premium-list--marketing .premium-list-head {
        min-height: 44px;
        padding: 0 20px;
        color: #536482;
        border: 0;
        border-bottom: 1px solid #edf1f6;
        border-radius: 0;
        background: transparent;
    }

    .premium-list--marketing .premium-list-item {
        min-height: 54px;
        margin-top: 6px;
        padding: 7px 20px;
        border: 1px solid #e8edf4;
        border-radius: 11px;
        background: #fff;
        box-shadow: none;
    }

    .premium-list--marketing .premium-list-item > * {
        min-width: 0;
    }

    /* DATE */
    .marketing-date {
        display: flex;
        align-items: center;
        gap: 0;
        white-space: nowrap;
        font-size: .88rem;
        font-weight: 600;
        color: #253451;
    }

    .marketing-date i { display: none; }

    /* LOCATION */
    .marketing-location {
        display: flex;
        align-items: center;
        gap: 0;
        min-width: 0;
    }

    .marketing-location-icon { display: none; }

    .marketing-location-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: .88rem;
        font-weight: 600;
        color: #334155;
    }

    /* TYPE — plain icon + bold label, no badge/background */
    .marketing-type-plain {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .marketing-type-plain i {
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .marketing-type-label {
        font-size: .92rem;
        font-weight: 800;
        color: #1E293B;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* COUNT — bold number + star rating, no badge/background */
    .marketing-count-plain {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
    }

    .marketing-count-value {
        font-size: .92rem;
        font-weight: 800;
        color: #1E293B;
        line-height: 1;
    }

    .marketing-count-stars {
        display: flex;
        align-items: center;
        gap: 1px;
        color: #F59E0B;
        font-size: .7rem;
        line-height: 1;
    }

    .marketing-count-stars .plus {
        font-weight: 800;
        color: #F59E0B;
        margin-left: 2px;
        font-size: .66rem;
    }

    /* STAFF */
    .marketing-staff {
        display: flex;
        align-items: center;
        gap: 0;
        min-width: 0;
    }

    .marketing-staff-avatar { display: none; }

    .marketing-staff-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: .88rem;
        font-weight: 600;
        color: #334155;
    }

    .marketing-unassigned {
        font-size: .74rem;
        color: #94A3B8;
        font-weight: 600;
    }

    /* ACTIONS */
    .premium-list--marketing .pli-col-actions {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
    }

    .premium-list--marketing .pli-btn-icon {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        max-width: 32px !important;
        flex: 0 0 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .premium-list--marketing .premium-list-item > :nth-child(2) { grid-column: 3; grid-row: 1; }
    .premium-list--marketing .premium-list-item > :nth-child(3) { grid-column: 4; grid-row: 1; }
    .premium-list--marketing .premium-list-item > :nth-child(4) { grid-column: 2; grid-row: 1; }
    .premium-list--marketing .premium-list-item > :nth-child(5) { grid-column: 5; grid-row: 1; }
    .premium-list--marketing .premium-list-item > :nth-child(6) { grid-column: 6; grid-row: 1; }
    .premium-list--marketing .premium-list-item > :nth-child(7) { grid-column: 7; grid-row: 1; }

    .premium-list--marketing .pli-action-dots {
        width: 40px;
        height: 40px;
        color: #415278;
        border: 1px solid #e2e8f2;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 7px rgba(30, 41, 59, .03);
    }

    .premium-list--marketing .pli-action-dots:hover,
    .premium-list--marketing .pli-action-dots.is-open {
        color: #5b3df5;
        border-color: #d9d0ff;
        background: #f8f6ff;
    }

    /* MODAL */
    #marketingModal .modal-dialog { max-width: 760px; }
    #viewMarketingModal .modal-dialog { max-width: 650px; }

    .marketing-modal-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 26px 18px;
        border-bottom: 1px solid #F1F5F9;
    }

    .marketing-modal-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        background: #EDE9FE;
        color: #7C3AED;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
    }

    .marketing-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1E293B;
    }

    .marketing-modal-subtitle {
        margin: 2px 0 0;
        font-size: .82rem;
        color: #64748B;
    }

    .marketing-section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        font-size: .72rem;
        font-weight: 800;
        color: #1E293B;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .marketing-section-label::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7C3AED, #5B21B6);
    }

    .marketing-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .marketing-field label {
        display: block;
        margin-bottom: 7px;
        font-size: .7rem;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .marketing-field label span { color: #EF4444; }

    .marketing-field .form-control,
    .marketing-field .form-select {
        height: 50px;
        border-radius: 12px;
        border: 1.5px solid #E4EBFB;
        font-size: .86rem;
        color: #1E293B;
        transition: .18s ease;
    }

    .marketing-field textarea.form-control {
        height: auto;
        min-height: 105px;
        resize: vertical;
        padding: 12px 14px;
    }

    .marketing-field .form-control:focus,
    .marketing-field .form-select:focus {
        border-color: #8B5CF6;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12);
        outline: none;
    }

    /* VIEW MODAL */
    .marketing-view-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .marketing-view-card {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 14px 16px;
        border: 1.5px solid #E4EBFB;
        border-radius: 14px;
        min-width: 0;
    }

    .marketing-view-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .marketing-view-label {
        margin-bottom: 4px;
        font-size: .67rem;
        font-weight: 800;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .marketing-view-value {
        font-size: .88rem;
        font-weight: 700;
        color: #1E293B;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .marketing-view-block { margin-top: 18px; }

    .marketing-view-block-label {
        margin-bottom: 8px;
        font-size: .67rem;
        font-weight: 800;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .marketing-view-box {
        padding: 14px 16px;
        border: 1.5px solid #E4EBFB;
        border-radius: 12px;
        background: #F8FAFF;
        font-size: .86rem;
        color: #334155;
        line-height: 1.6;
    }

    @media (max-width: 650px) {
        .marketing-grid-2,
        .marketing-view-grid {
            grid-template-columns: 1fr;
        }
    }

</style>
@endpush


@section('content')

<div class="management-page">

    {{-- TOP ACTIONS --}}
    @include('partials.mgmt-top-actions', [
        'addLabel' => 'Add Marketing Activity',
        'addModal' => '#marketingModal',
        'addOnclick' => 'openAddMarketingModal()',
        'filterModule' => 'marketing',
        'filterRoute' => route('marketing.index'),
    ])

    {{-- STATS --}}
    <div class="mgmt-stats-grid mgmt-stats-grid--4">

        @include('partials.mgmt-stat-card', [
            'theme' => 'indigo',
            'icon' => 'people-purple',
            'label' => 'Total Activities',
            'value' => $totalActivities,
            'subtext' => 'All marketing activities',
            'sparkColor' => '#6366F1',
            'trend' => null,
        ])

        @include('partials.mgmt-stat-card', [
            'theme' => 'blue',
            'icon' => 'calendar-blue',
            'label' => 'Today',
            'value' => $todayActivities,
            'subtext' => 'Activities today',
            'sparkColor' => '#3B82F6',
            'trend' => null,
        ])

        @include('partials.mgmt-stat-card', [
            'theme' => 'green',
            'icon' => 'check-green',
            'label' => "Today's Output",
            'value' => $todayCount,
            'subtext' => 'Total marketing count',
            'sparkColor' => '#22C55E',
            'trend' => null,
        ])

        @include('partials.mgmt-stat-card', [
            'theme' => 'orange',
            'icon' => 'clock-orange',
            'label' => 'Google Reviews',
            'value' => $googleReviewsToday,
            'subtext' => 'Generated today',
            'sparkColor' => '#F59E0B',
            'trend' => null,
        ])

    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- MAIN CARD --}}
    <div class="content-card">

        <div class="content-card-header">
            <div>
                <h2>Daily Marketing Activities</h2>
                <span>{{ $marketingActivities->total() }} activity(s) found</span>
            </div>

            <div class="content-card-header-actions">
                <form method="GET" action="{{ route('marketing.index') }}" class="marketing-search">
                    <input type="hidden" name="activity_date" value="{{ $activityDate }}">
                    <input type="hidden" name="marketing_type" value="{{ $marketingType }}">
                    <input type="hidden" name="location" value="{{ $location }}">
                    <input type="hidden" name="staff_id" value="{{ $staffId }}">

                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search marketing...">
                        @if($search)
                            <a href="{{ route('marketing.index', array_filter(['activity_date' => $activityDate ?? '', 'marketing_type' => $marketingType ?? '', 'location' => $location ?? '', 'staff_id' => $staffId ?? ''])) }}"
                                title="Clear search">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($marketingActivities->count())

            <div class="premium-list premium-list--marketing premium-list--mgmt">

                <div class="premium-list-head">
                    <span class="pli-head-cell col-center">#</span>
                    <span class="pli-head-cell col-left">MARKETING TYPE</span>
                    <span class="pli-head-cell col-left">DATE</span>
                    <span class="pli-head-cell col-left">LOCATION</span>
                    <span class="pli-head-cell col-center">COUNT</span>
                    <span class="pli-head-cell col-left">STAFF</span>
                    <span class="pli-head-cell col-center">ACTION</span>
                </div>

                @php
                    $listStart = ($marketingActivities->currentPage() - 1) * $marketingActivities->perPage();
                @endphp

                @foreach($marketingActivities as $activity)

                    @php
                        $typeLower = strtolower($activity->marketing_type);

                        // [icon, accent color]
                        $badge = match(true) {
                            str_contains($typeLower, 'google')    => ['bi-google', '#D97706'],
                            str_contains($typeLower, 'instagram') => ['bi-instagram', '#BE185D'],
                            str_contains($typeLower, 'facebook')  => ['bi-facebook', '#2563EB'],
                            str_contains($typeLower, 'whatsapp')  => ['bi-whatsapp', '#15803D'],
                            default                                 => ['bi-megaphone', '#6D28D9'],
                        };

                        $staffName = $activity->staff->name ?? null;
                        $starCount = min((int) $activity->count, 5);
                    @endphp

                    <article class="premium-list-item" id="marketing-row-{{ $activity->id }}">

                        <div class="pli-rank col-center">{{ $listStart + $loop->iteration }}</div>

                        {{-- DATE --}}
                        <div class="pli-col col-left">
                            <span class="marketing-date">
                                <i class="bi bi-calendar3"></i>
                                {{ $activity->activity_date->format('d M Y') }}
                            </span>
                        </div>

                        {{-- LOCATION --}}
                        <div class="pli-col col-left">
                            <div class="marketing-location">
                                <span class="marketing-location-icon"><i class="bi bi-geo-alt"></i></span>
                                <span class="marketing-location-name">{{ $activity->location }}</span>
                            </div>
                        </div>

                        {{-- TYPE --}}
                        <div class="pli-col col-left">
                            <div class="marketing-type-plain">
                                <i class="bi {{ $badge[0] }}" style="color:{{ $badge[1] }};"></i>
                                <span class="marketing-type-label">{{ $activity->marketing_type }}</span>
                            </div>
                        </div>

                        {{-- COUNT --}}
                        <div class="pli-col col-center">
                            <div class="marketing-count-plain">
                                <span class="marketing-count-value">{{ $activity->count }}</span>
                                <span class="marketing-count-stars">
                                    @for($i = 0; $i < $starCount; $i++)
                                        <i class="bi bi-star-fill"></i>
                                    @endfor
                                    @if($activity->count > 5)
                                        <span class="plus">+</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- STAFF --}}
                        <div class="pli-col col-left">
                            @if($staffName)
                                <div class="marketing-staff">
                                    <span class="marketing-staff-avatar">{{ strtoupper(substr($staffName, 0, 1)) }}</span>
                                    <span class="marketing-staff-name">{{ $staffName }}</span>
                                </div>
                            @else
                                <span class="marketing-unassigned">Unassigned</span>
                            @endif
                        </div>

                        {{-- ACTIONS --}}
                        <div class="pli-col pli-col-actions col-center">

                            {{-- Mobile: same dropdown pattern used on other pages --}}
                            <div class="dropdown pli-dots-dropdown d-md-none">
                                <button class="pli-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end pli-action-menu">
                                    <li>
                                        <button type="button" class="dropdown-item pli-menu-item"
                                            onclick="openViewMarketingModal({{ $activity->id }})">
                                            <span class="pli-menu-icon pli-menu-icon--view"><i class="bi bi-eye"></i></span>
                                            <span>View Marketing  </span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item pli-menu-item"
                                            data-bs-toggle="modal" data-bs-target="#marketingModal"
                                            onclick='openEditMarketingModal(@json($activity))'>
                                            <span class="pli-menu-icon pli-menu-icon--edit"><i class="bi bi-pencil"></i></span>
                                            <span>Edit Marketing</span>
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item pli-menu-item pli-menu-item--danger"
                                            onclick="openDeleteMarketingModal({{ $activity->id }}, @js($activity->marketing_type), @js($activity->location))">
                                            <span class="pli-menu-icon pli-menu-icon--delete"><i class="bi bi-trash3"></i></span>
                                            <span>Delete Marketing</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            {{-- Desktop: 3-dot action popover (same pattern as Job Cards) --}}
                            <div class="pli-action-menu-wrap pli-action-buttons-desktop">
                                <button
                                    type="button"
                                    class="pli-action-dots"
                                    aria-label="Marketing actions"
                                    aria-expanded="false"
                                    onclick="togglePliActions(this)"
                                >
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <div class="pli-action-popover">
                                    <button
                                        type="button"
                                        class="pli-popover-action"
                                        onclick="openViewMarketingModal({{ $activity->id }}); closePliActions(this)"
                                    >
                                        <span class="pli-popover-icon pli-popover-icon--view">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                        <span>View</span>
                                    </button>

                                    <button
                                        type="button"
                                        class="pli-popover-action"
                                        data-bs-toggle="modal"
                                        data-bs-target="#marketingModal"
                                        onclick='openEditMarketingModal(@json($activity)); closePliActions(this)'
                                    >
                                        <span class="pli-popover-icon pli-popover-icon--edit">
                                            <i class="bi bi-pencil"></i>
                                        </span>
                                        <span>Edit</span>
                                    </button>

                                    <div class="pli-popover-divider"></div>

                                    <button
                                        type="button"
                                        class="pli-popover-action pli-popover-action--danger"
                                        onclick="openDeleteMarketingModal({{ $activity->id }}, @js($activity->marketing_type), @js($activity->location)); closePliActions(this)"
                                    >
                                        <span class="pli-popover-icon pli-popover-icon--delete">
                                            <i class="bi bi-trash3"></i>
                                        </span>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

            <div class="content-card-footer table-pagination">
                @include('partials.pagination-bar', ['paginator' => $marketingActivities])
            </div>

        @else

            <div class="empty-state">
                <div style="text-align:center;padding:60px 20px;">
                    <div style="width:72px;height:72px;border-radius:20px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:30px;color:#7C3AED;">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h4 style="font-size:1.05rem;font-weight:700;color:#1E293B;margin-bottom:6px;">No Marketing Activities Found</h4>
                    <p style="color:#64748B;font-size:.88rem;margin-bottom:20px;">No marketing activities have been recorded yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#marketingModal" onclick="openAddMarketingModal()">
                        <i class="bi bi-plus-lg me-1"></i> Add Marketing Activity
                    </button>
                </div>
            </div>

        @endif
    </div>
</div>


{{-- ADD / EDIT MODAL --}}
<div class="modal fade premium-modal" id="marketingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">
            <form id="marketingForm" method="POST" action="{{ route('marketing.store') }}">
                @csrf
                <input type="hidden" name="marketing_id" id="marketingId" value="">
                <div id="marketingMethodContainer"></div>

                <div class="marketing-modal-header">
                    <div class="marketing-modal-icon"><i class="bi bi-megaphone"></i></div>
                    <div>
                        <h5 class="marketing-modal-title" id="marketingModalTitle">Add Marketing Activity</h5>
                        <p class="marketing-modal-subtitle" id="marketingModalSubtitle">Record daily marketing activity</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="padding:26px;">

                    <div class="marketing-section-label">Marketing Information</div>

                    {{-- Date --}}
                    <div class="marketing-field mb-4">
                        <label>Date <span>*</span></label>
                        <input type="date" name="activity_date" id="marketingDate" class="form-control" required>
                    </div>

                    {{-- Type / Location --}}
                    <div class="marketing-grid-2 mb-4">

                        <div class="marketing-field">
                            <label>Marketing Type <span>*</span></label>
                            <input
                                type="text"
                                name="marketing_type"
                                id="marketingType"
                                class="form-control"
                                list="marketingTypeSuggestions"
                                placeholder="e.g. Google Review"
                                maxlength="60"
                                autocomplete="off"
                                required
                            >
                            <datalist id="marketingTypeSuggestions">
                                @foreach($marketingTypes as $suggestion)
                                    <option value="{{ $suggestion }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="marketing-field">
                            <label>Location <span>*</span></label>
                            <input type="text" name="location" id="marketingLocation" class="form-control"
                                   placeholder="e.g. Kochi" maxlength="150" required>
                        </div>

                    </div>

                    {{-- Count / Staff --}}
                    <div class="marketing-grid-2 mb-4">

                        <div class="marketing-field">
                            <label>Count <span>*</span></label>
                            <input type="number" name="count" id="marketingCount" class="form-control" min="1" value="1" required>
                        </div>

                        <div class="marketing-field">
                            <label>Staff</label>
                            <select name="staff_id" id="marketingStaff" class="form-select">
                                <option value="">No staff assigned</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Notes --}}
                    <div class="marketing-field">
                        <label>Notes</label>
                        <textarea name="notes" id="marketingNotes" class="form-control" rows="4" maxlength="2000"
                                  placeholder="Add additional details..."></textarea>
                    </div>

                </div>

                <div class="modal-footer" style="padding:16px 26px; border-top:1px solid #F1F5F9; background:#FAFBFF;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius:10px; padding:9px 20px; font-weight:600;">
                        <i class="bi bi-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="marketingSubmitButton"
                            style="border-radius:10px; padding:9px 22px; font-weight:700; background:linear-gradient(135deg,#6366f1,#4f46e5); border:none; box-shadow:0 3px 10px rgba(99,102,241,.35);">
                        <i class="bi bi-check2 me-1"></i> Save Activity
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


{{-- VIEW MODAL --}}
<div class="modal fade premium-modal" id="viewMarketingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">

            <div class="marketing-modal-header">
                <div class="marketing-modal-icon"><i class="bi bi-megaphone"></i></div>
                <div>
                    <h5 class="marketing-modal-title">Marketing Activity Details</h5>
                    <p class="marketing-modal-subtitle">View complete activity information</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding:24px 26px;" id="viewMarketingContent">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>

            <div class="modal-footer" style="padding:16px 26px; border-top:1px solid #F1F5F9; background:#FAFBFF;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="border-radius:10px; padding:9px 20px; font-weight:600;">
                    <i class="bi bi-x me-1"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>


{{-- DELETE MODAL --}}
<div class="modal fade premium-modal" id="deleteMarketingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:18px; border:1.5px solid #E4EBFB; overflow:hidden;">

            <div class="marketing-modal-header">
                <div class="marketing-modal-icon" style="background:#FEE2E2; color:#DC2626;"><i class="bi bi-trash3"></i></div>
                <div>
                    <h5 class="marketing-modal-title">Delete Marketing Activity</h5>
                    <p class="marketing-modal-subtitle">This action cannot be undone</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <p style="color:#64748B;margin-bottom:0;">
                    Are you sure you want to delete <strong id="deleteMarketingType"></strong>
                    at <strong id="deleteMarketingLocation"></strong>? This action cannot be reversed.
                </p>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #F1F5F9; background:#FAFBFF;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteMarketingForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="border-radius:10px; padding:9px 22px; font-weight:700;">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('js/pli-action-popover.js') }}"></script>
<script>

    function openAddMarketingModal()
    {
        const form = document.getElementById('marketingForm');
        form.reset();

        document.getElementById('marketingId').value = '';
        document.getElementById('marketingModalTitle').textContent = 'Add Marketing Activity';
        document.getElementById('marketingModalSubtitle').textContent = 'Record daily marketing activity';
        document.getElementById('marketingSubmitButton').innerHTML = '<i class="bi bi-check2 me-1"></i> Save Activity';
        form.action = '{{ route('marketing.store') }}';
        document.getElementById('marketingMethodContainer').innerHTML = '';

        const now = new Date();
        const date = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

        document.getElementById('marketingDate').value = date;
        document.getElementById('marketingCount').value = 1;
    }

    function openEditMarketingModal(activity)
    {
        const form = document.getElementById('marketingForm');

        document.getElementById('marketingId').value = activity.id;
        document.getElementById('marketingDate').value = activity.activity_date;
        document.getElementById('marketingType').value = activity.marketing_type;
        document.getElementById('marketingLocation').value = activity.location;
        document.getElementById('marketingCount').value = activity.count;
        document.getElementById('marketingStaff').value = activity.staff_id || '';
        document.getElementById('marketingNotes').value = activity.notes || '';

        document.getElementById('marketingModalTitle').textContent = 'Edit Marketing Activity';
        document.getElementById('marketingModalSubtitle').textContent = 'Update marketing activity details';
        document.getElementById('marketingSubmitButton').innerHTML = '<i class="bi bi-check2 me-1"></i> Update Activity';

        form.action = '{{ url('/marketing') }}/' + activity.id;
        document.getElementById('marketingMethodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }

    function openViewMarketingModal(id)
    {
        const modalElement = document.getElementById('viewMarketingModal');
        const content = document.getElementById('viewMarketingContent');

        content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(modalElement).show();

        const url = '{{ url('/marketing') }}/' + id;

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error('Failed to load activity');
                return response.json();
            })
            .then(activity => {

                const staffName = activity.staff?.name || 'Unassigned';
                const icon = getMarketingIcon(activity.marketing_type);
                const colors = getMarketingColors(activity.marketing_type);
                const date = formatMarketingDate(activity.activity_date);

                content.innerHTML = `
                    <div class="marketing-view-grid">

                        <div class="marketing-view-card">
                            <div class="marketing-view-icon" style="background:#EDE9FE; color:#7C3AED;">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="marketing-view-label">Date</div>
                                <div class="marketing-view-value">${escapeHtml(date)}</div>
                            </div>
                        </div>

                        <div class="marketing-view-card">
                            <div class="marketing-view-icon" style="background:#DCFCE7; color:#15803D;">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="marketing-view-label">Location</div>
                                <div class="marketing-view-value">${escapeHtml(activity.location)}</div>
                            </div>
                        </div>

                        <div class="marketing-view-card">
                            <div class="marketing-view-icon" style="background:${colors.bg}; color:${colors.fg};">
                                <i class="bi ${icon}"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="marketing-view-label">Marketing Type</div>
                                <div class="marketing-view-value">${escapeHtml(activity.marketing_type)}</div>
                            </div>
                        </div>

                        <div class="marketing-view-card">
                            <div class="marketing-view-icon" style="background:#ECFDF5; color:#047857;">
                                <i class="bi bi-123"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="marketing-view-label">Count</div>
                                <div class="marketing-view-value">${activity.count}</div>
                            </div>
                        </div>

                        <div class="marketing-view-card" style="grid-column: span 2;">
                            <div class="marketing-view-icon" style="background:#EEF2FF; color:#6366F1;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="marketing-view-label">Staff</div>
                                <div class="marketing-view-value">${escapeHtml(staffName)}</div>
                            </div>
                        </div>

                    </div>

                    <div class="marketing-view-block">
                        <div class="marketing-view-block-label">Notes</div>
                        <div class="marketing-view-box">${escapeHtml(activity.notes || 'No notes added.')}</div>
                    </div>
                `;
            })
            .catch(() => {
                content.innerHTML = '<div class="alert alert-danger">Unable to load marketing activity.</div>';
            });
    }

    function openDeleteMarketingModal(id, type, location)
    {
        document.getElementById('deleteMarketingType').textContent = type;
        document.getElementById('deleteMarketingLocation').textContent = location;
        document.getElementById('deleteMarketingForm').action = '{{ url('/marketing') }}/' + id;
        new bootstrap.Modal(document.getElementById('deleteMarketingModal')).show();
    }

    function getMarketingIcon(type)
    {
        const t = (type || '').toLowerCase();
        if (t.includes('google')) return 'bi-google';
        if (t.includes('instagram')) return 'bi-instagram';
        if (t.includes('facebook')) return 'bi-facebook';
        if (t.includes('whatsapp')) return 'bi-whatsapp';
        return 'bi-megaphone';
    }

    function getMarketingColors(type)
    {
        const t = (type || '').toLowerCase();
        if (t.includes('google')) return { bg: 'linear-gradient(135deg,#F59E0B,#D97706)', fg: '#fff' };
        if (t.includes('instagram')) return { bg: 'linear-gradient(135deg,#F58529,#DD2A7B 55%,#8134AF)', fg: '#fff' };
        if (t.includes('facebook')) return { bg: 'linear-gradient(135deg,#3B82F6,#1D4ED8)', fg: '#fff' };
        if (t.includes('whatsapp')) return { bg: 'linear-gradient(135deg,#22C55E,#15803D)', fg: '#fff' };
        return { bg: 'linear-gradient(135deg,#8B5CF6,#6D28D9)', fg: '#fff' };
    }

    function formatMarketingDate(value)
    {
        if (!value) return '—';
        const date = new Date(value + 'T00:00:00');
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function escapeHtml(value)
    {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

</script>
@endpush

@endsection
