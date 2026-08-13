<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\JobCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/
Route::get('/staff', [StaffController::class, 'index']) ->name('staff.index');
Route::get('/staff/export/excel', [StaffController::class, 'exportExcel'])->name('staff.export.excel');
Route::get('/staff/export/pdf', [StaffController::class, 'exportPdf'])->name('staff.export.pdf');
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
Route::put('/staff/{staff}', [StaffController::class, 'update']) ->name('staff.update');
Route::patch('/staff/{staff}/status', [StaffController::class, 'toggleStatus']) ->name('staff.toggle-status');


/*
|--------------------------------------------------------------------------
| Customers
|--------------------------------------------------------------------------
*/

Route::get('/customers', [CustomerController::class, 'index']) ->name('customers.index');
Route::get('/customers/export/excel', [CustomerController::class, 'exportExcel'])->name('customers.export.excel');
Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
Route::post('/customers', [CustomerController::class, 'store']) ->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update']) ->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']) ->name('customers.destroy');

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

Route::get('/services', [ServiceController::class, 'index']) ->name('services.index');
Route::get('/services/export/excel', [ServiceController::class, 'exportExcel'])->name('services.export.excel');
Route::get('/services/export/pdf', [ServiceController::class, 'exportPdf'])->name('services.export.pdf');
Route::get('/services/icon-suggest', [ServiceController::class, 'suggestIcon'])->name('services.icon-suggest');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::put('/services/{service}', [ServiceController::class, 'update']) ->name('services.update');
Route::patch('/services/{service}/status', [ServiceController::class, 'toggleStatus']) ->name('services.toggle-status');
Route::delete('/services/{service}', [ServiceController::class, 'destroy']) ->name('services.destroy');

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index']) ->name('products.index');
Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
Route::post('/products', [ProductController::class, 'store']) ->name('products.store');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::patch('/products/{product}/status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

/*
|--------------------------------------------------------------------------
| Job Cards
|--------------------------------------------------------------------------
*/

Route::get('/job-cards', [JobCardController::class, 'index']) ->name('job-cards.index');
Route::get('/job-cards/export/excel', [JobCardController::class, 'exportExcel'])->name('job-cards.export.excel');
Route::get('/job-cards/export/pdf', [JobCardController::class, 'exportPdf'])->name('job-cards.export.pdf');
Route::post('/job-cards', [JobCardController::class, 'store']) ->name('job-cards.store');
Route::put('/job-cards/{jobCard}', [JobCardController::class, 'update']) ->name('job-cards.update');
Route::delete('/job-cards/{jobCard}', [JobCardController::class, 'destroy'])->name('job-cards.destroy');
