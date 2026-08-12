@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <section class="dashboard-top">
        <div class="dashboard-welcome">
            <span class="dashboard-label">Dashboard</span>
            <h2>Welcome to Salon Management System.</h2>
            <p>Manage your salon operations from one place.</p>
        </div>
    </section>

    <section class="dashboard-cards">
        <article class="dashboard-card">
            <p class="card-label">Customers</p>
            <h3>0</h3>
        </article>
        <article class="dashboard-card">
            <p class="card-label">Appointments</p>
            <h3>0</h3>
        </article>
        <article class="dashboard-card">
            <p class="card-label">Revenue</p>
            <h3>₹0</h3>
        </article>
        <article class="dashboard-card">
            <p class="card-label">Staff</p>
            <h3>0</h3>
        </article>
    </section>

    <section class="dashboard-panel">
        <div class="panel-header">
            <h3>Recent Appointments</h3>
        </div>
        <div class="panel-body empty-state">
            <p>No appointments yet.</p>
            <p>Appointments will appear here once the appointment module is implemented.</p>
        </div>
    </section>
@endsection
