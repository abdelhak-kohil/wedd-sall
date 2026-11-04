<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SpaceController;
use Illuminate\Support\Facades\Route;

// Redirection root
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Language switcher (public)
Route::get('/lang/{locale}', [AuthController::class, 'switchLanguage'])->name('lang.switch');

// Authenticated session routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Phase 5: Hall & Calendar Management
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/check', [AvailabilityController::class, 'check'])->name('availability.check');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    // Space and slot configuration
    Route::get('/settings/spaces', [SpaceController::class, 'index'])->name('settings.spaces');
    Route::post('/settings/spaces', [SpaceController::class, 'storeSpace'])->name('settings.spaces.store');
    Route::post('/settings/slots', [SpaceController::class, 'storeSlot'])->name('settings.slots.store');

    // Phase 6: Customers & Reservations
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/api/wilayas/{wilaya}/communes', [CustomerController::class, 'communes'])->name('api.communes');

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');
    Route::get('/reservations/{reservation}/contract', [ContractController::class, 'show'])->name('reservations.contract');

    // Phase 7: Packages & Quotations
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::patch('/packages/{package}/toggle', [PackageController::class, 'toggle'])->name('packages.toggle');

    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');


    // Phase 8: Payments & Cash Registers
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/reservations/{reservation}/payments', [PaymentController::class, 'store'])->name('reservations.payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::get('/cash-registers', [CashRegisterController::class, 'index'])->name('cash-registers.index');
    Route::post('/cash-registers/open', [CashRegisterController::class, 'open'])->name('cash-registers.open');
    Route::post('/cash-registers/{register}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');

    // Phase 9: Expenses & Profitability
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/report', [ExpenseController::class, 'report'])->name('expenses.report');

    // Phase 10: Inventory / Stock Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{item}/move', [InventoryController::class, 'move'])->name('inventory.move');
    Route::post('/inventory/{item}/quickstock', [InventoryController::class, 'quickstock'])->name('inventory.quickstock');

    // Phase 11: Operations & Day-of Event Management
    Route::get('/operations', [OperationsController::class, 'index'])->name('operations.index');
    Route::get('/operations/{reservation}', [OperationsController::class, 'show'])->name('operations.show');
    Route::get('/operations/{reservation}/print', [OperationsController::class, 'printRoadmap'])->name('operations.print');
    Route::post('/operations/{reservation}/checklist/generate', [OperationsController::class, 'generateChecklist'])->name('operations.checklist.generate');
    Route::post('/operations/{reservation}/timeline/generate', [OperationsController::class, 'generateTimeline'])->name('operations.timeline.generate');
    Route::post('/operations/{reservation}/tasks', [OperationsController::class, 'storeTask'])->name('operations.tasks.store');
    Route::patch('/operations/tasks/{task}/toggle', [OperationsController::class, 'toggleTask'])->name('operations.tasks.toggle');
    Route::delete('/operations/tasks/{task}', [OperationsController::class, 'destroyTask'])->name('operations.tasks.destroy');
    Route::post('/operations/{reservation}/vendors', [OperationsController::class, 'storeVendor'])->name('operations.vendors.store');
    Route::delete('/operations/vendors/{vendor}', [OperationsController::class, 'destroyVendor'])->name('operations.vendors.destroy');
    Route::post('/operations/{reservation}/timeline', [OperationsController::class, 'storeTimeline'])->name('operations.timeline.store');
    Route::patch('/operations/timeline/{timeline}/toggle', [OperationsController::class, 'toggleTimeline'])->name('operations.timeline.toggle');
    Route::delete('/operations/timeline/{timeline}', [OperationsController::class, 'destroyTimeline'])->name('operations.timeline.destroy');
});
