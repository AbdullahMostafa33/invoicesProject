<?php

use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Auth::routes();

// Home Route
Route::get('/', [HomeController::class, 'home'])->middleware('auth');

// Invoice Routes
Route::get('/status/{invoice}', function (Invoice $invoice) {
   return view('invoices.payment', ['invoice' => $invoice]);
})->middleware('auth');

Route::resource('/invoices', InvoiceController::class)->middleware('auth');
Route::post('/invoices/toArchive', [InvoiceController::class, 'toArchive']);
Route::post('/invoices/restore', [InvoiceController::class, 'restore']);
Route::get('/invoices/print/{invoice}', [InvoiceController::class, 'print']);
Route::get('/payment/{invoice}', [InvoiceController::class, 'payment'])->middleware('auth');

// Report Routes
Route::get('/reports/invoices', [ReportController::class, 'index'])->middleware('auth');
Route::post('/Search_invoices', [ReportController::class, 'searchInvoice'])->middleware('auth');
Route::get('/reports/customers', [ReportController::class, 'indexcustomers'])->middleware('auth')->name('reports.customers');
Route::get('/Search_customers', [ReportController::class, 'searchCustomer'])->middleware('auth');

// User and Role Management Routes
Route::resource('/users', UserController::class)->middleware('auth');
Route::resource('/roles', RoleController::class)->middleware('auth');

// Attachment Routes
Route::resource('/attchments', InvoiceAttachmentController::class)->middleware('auth');
Route::get('/download/{folder}/{name}', [InvoiceAttachmentController::class, 'download'])->middleware('auth');
Route::get('/open/{folder}/{name}', [InvoiceAttachmentController::class, 'open'])->middleware('auth');

// Section and Product Route
Route::resource('/sections', SectionController::class)->middleware('auth');
Route::resource('/products', ProductController::class)->middleware('auth');
Route::get('/getProduct/{id}', [InvoiceController::class, 'getProducts']);
