<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\MarketingActivityController;
use App\Http\Controllers\ReportController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated Application
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/staff-performance', [DashboardController::class, 'staffPerformance'])
        ->name('dashboard.staff-performance');

    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    | Administrator + Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/export/excel', [StaffController::class, 'exportExcel'])->name('staff.export.excel');
        Route::get('/staff/export/pdf', [StaffController::class, 'exportPdf'])->name('staff.export.pdf');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{staff}', [StaffController::class, 'update']) ->name('staff.update');
        Route::patch('/staff/{staff}/status', [StaffController::class, 'toggleStatus']) ->name('staff.toggle-status');
    });


    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    | Administrator + Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    });


    /*
    |--------------------------------------------------------------------------
    | Complaints
    |--------------------------------------------------------------------------
    | Administrator + Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager')->group(function () {
        Route::get('/complaints', [ComplaintsController::class, 'index'])->name('complaints.index');
        Route::post('/complaints', [ComplaintsController::class, 'store'])->name('complaints.store');
        Route::get('/complaints/{complaint}', [ComplaintsController::class, 'show'])->name('complaints.show');
        Route::delete('/complaints/{complaint}', [ComplaintsController::class, 'destroy'])->name('complaints.destroy');
        Route::patch('/complaints/{complaint}/close', [ComplaintsController::class, 'close'])->name('complaints.close');
    });


    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    | Administrator + Manager + Staff
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager,staff')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/export/excel', [CustomerController::class, 'exportExcel']) ->name('customers.export.excel');
        Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf']) ->name('customers.export.pdf');
        Route::post('/customers', [CustomerController::class, 'store']) ->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update']) ->name('customers.update');
        Route::patch('/customers/{customer}/status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']) ->name('customers.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    | Administrator + Manager + Staff
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager,staff')->group(function () {
        Route::get('/services', [ServiceController::class, 'index']) ->name('services.index');
        Route::get('/services/export/excel', [ServiceController::class, 'exportExcel']) ->name('services.export.excel');
        Route::get('/services/export/pdf', [ServiceController::class, 'exportPdf']) ->name('services.export.pdf');
        Route::get('/services/icon-suggest', [ServiceController::class, 'suggestIcon']) ->name('services.icon-suggest');
        Route::post('/services', [ServiceController::class, 'store']) ->name('services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update']) ->name('services.update');
        Route::patch('/services/{service}/status', [ServiceController::class, 'toggleStatus'])  ->name('services.toggle-status');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']) ->name('services.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    | Administrator + Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager')->group(function () {
        Route::get('/products', [ProductController::class, 'index']) ->name('products.index');
        Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/status', [ProductController::class, 'toggleStatus']) ->name('products.toggle-status');
        Route::delete('/products/{product}', [ProductController::class, 'destroy']) ->name('products.destroy');
        Route::post('/products/{product}/purchases', [ProductController::class, 'storePurchase']) ->name('products.purchases.store');
        Route::get('/products/{product}/purchases', [ProductController::class, 'purchaseHistory'])->name('products.purchases.history');
    });


    /*
    |--------------------------------------------------------------------------
    | Job Cards
    |--------------------------------------------------------------------------
    | Administrator + Manager + Staff
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager,staff')->group(function () {
        Route::get('/job-cards', [JobCardController::class, 'index'])  ->name('job-cards.index');
        Route::get('/job-cards/export/excel', [JobCardController::class, 'exportExcel']) ->name('job-cards.export.excel');
        Route::get('/job-cards/export/pdf', [JobCardController::class, 'exportPdf']) ->name('job-cards.export.pdf');
        Route::post('/job-cards', [JobCardController::class, 'store'])->name('job-cards.store');
        Route::put('/job-cards/{jobCard}', [JobCardController::class, 'update']) ->name('job-cards.update');
        Route::delete('/job-cards/{jobCard}', [JobCardController::class, 'destroy']) ->name('job-cards.destroy');
    });
    /*
    |--------------------------------------------------------------------------
    | Report
    |--------------------------------------------------------------------------
    | Administrator + Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator,manager')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/staff-daily-target', [ReportController::class, 'staffDailyTarget'])->name('staff.daily-target');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
    });


    /*
|--------------------------------------------------------------------------
| Daily Marketing Activities
|--------------------------------------------------------------------------
| Administrator + Manager + Staff
|--------------------------------------------------------------------------
*/

    Route::middleware('role:administrator,manager,staff')->group(function () {
        Route::get('/marketing', [ MarketingActivityController::class, 'index' ])->name('marketing.index');
        Route::post('/marketing', [ MarketingActivityController::class, 'store' ])->name('marketing.store');
        Route::get('/marketing/{marketing}', [MarketingActivityController::class,'show' ])->name('marketing.show');
        Route::put('/marketing/{marketing}', [ MarketingActivityController::class, 'update'])->name('marketing.update');
        Route::delete('/marketing/{marketing}', [  MarketingActivityController::class,  'destroy' ])->name('marketing.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy']) ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Breeze Authentication
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
