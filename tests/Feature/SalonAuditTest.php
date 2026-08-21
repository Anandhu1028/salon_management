<?php

use App\Models\Customer;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\PaymentType;
use App\Models\Role;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'administrator']);
    $this->managerRole = Role::firstOrCreate(['name' => 'manager']);
    $this->staffRole = Role::firstOrCreate(['name' => 'staff']);

    $this->adminUser = User::updateOrCreate(
        ['email' => 'admin_test@salonpro.com'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role_id' => $this->adminRole->id]
    );

    $this->staffUser = User::updateOrCreate(
        ['email' => 'staff_test@salonpro.com'],
        ['name' => 'Staff Test', 'password' => bcrypt('password'), 'role_id' => $this->staffRole->id]
    );
});

test('role middleware permits administrator to access reports', function () {
    $response = $this->actingAs($this->adminUser)->get(route('reports.index'));

    $response->assertStatus(200);
});

test('role middleware denies staff user from accessing reports with 403', function () {
    $response = $this->actingAs($this->staffUser)->get(route('reports.index'));

    $response->assertStatus(403);
});

test('staff user can access job cards', function () {
    $response = $this->actingAs($this->staffUser)->get(route('job-cards.index'));

    $response->assertStatus(200);
});

test('job card calculates total from job card services amount minus discount', function () {
    $paymentType = PaymentType::firstOrCreate(['name' => 'Cash'], ['is_active' => true]);
    $service = Service::firstOrCreate(
        ['service_name' => 'Test Haircut'],
        ['category' => 'Hair', 'status' => 'active']
    );
    $customer = Customer::firstOrCreate(
        ['name' => 'Test Customer'],
        ['mobile_country_code' => '+91', 'mobile_number' => '9999999999']
    );

    $jobCard = JobCard::create([
        'job_card_name' => 'Test Audit Job Card',
        'customer_id' => $customer->id,
        'status' => 'completed',
        'discount_amount' => 100.00,
    ]);

    JobCardService::create([
        'job_card_id' => $jobCard->id,
        'service_id' => $service->id,
        'subcategory' => 'Cut',
        'amount' => 500.00,
        'payment_type_id' => $paymentType->id,
    ]);

    JobCardService::create([
        'job_card_id' => $jobCard->id,
        'service_id' => $service->id,
        'subcategory' => 'Wash',
        'amount' => 200.00,
        'payment_type_id' => $paymentType->id,
    ]);

    $jobCard->load('serviceItems');

    expect($jobCard->getSubtotalAmount())->toBe(700.00);
    expect($jobCard->getDiscountAmount())->toBe(100.00);
    expect($jobCard->getTotalAmount())->toBe(600.00);
    expect($jobCard->getFinalAmount())->toBe(600.00);
});

test('dashboard staff performance endpoint returns valid data structure', function () {
    $response = $this->actingAs($this->adminUser)->get(route('dashboard.staff-performance', ['period' => '7']));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data',
        'range',
        'average_performance',
    ]);
});
