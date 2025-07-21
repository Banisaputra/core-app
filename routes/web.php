<?php

use App\Http\Middleware\LoginAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RepaymentController;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\LoanPaymentController;

// auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// admin access
Route::middleware([LoginAuth::class, RoleMiddleware::class . ':admin'])->group(function () {

    // search
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::get('/api/roles/search', [RoleController::class, 'search']);
    Route::get('/api/members/search', [MemberController::class, 'search']);
    Route::get('/api/items/search', [MasterItemController::class, 'search']);
    Route::get('/api/category/search', [CategoryController::class, 'search']);
    Route::get('/api/supplier/search', [SupplierController::class, 'search']);

    // info
    Route::get('/access/info', [RoleController::class, 'info'])->name('access.info');

    // supplier
    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::get('/supplier/import', [SupplierController::class, 'downloadTemplate'])->name('supplier.template');
    Route::post('/supplier/import', [SupplierController::class, 'import'])->name('supplier.import');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{id}', [SupplierController::class, 'show'])->name('supplier.show');
    Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    
    // categories
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::get('/category/import', [CategoryController::class, 'downloadTemplate'])->name('category.template');
    Route::post('/category/import', [CategoryController::class, 'import'])->name('category.import');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    
    // member
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::get('/members/import', [MemberController::class, 'downloadTemplate'])->name('members.template');
    Route::post('/members/import', [MemberController::class, 'import'])->name('members.import');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');
    
    // role
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/asign', [RoleController::class, 'asign'])->name('roles.asign');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/asign', [RoleController::class, 'updateRoles'])->name('roles.asigned');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // savings
    Route::get('/savings', [SavingController::class, 'index'])->name('savings.index');
    Route::get('/savings/create', [SavingController::class, 'create'])->name('savings.create');
    Route::get('/savings/generate', [SavingController::class, 'generate'])->name('savings.generate');
    Route::post('/savings/generate', [SavingController::class, 'generated'])->name('savings.generated');
    Route::post('/savings', [SavingController::class, 'store'])->name('savings.store');
    Route::get('/savings/{id}', [SavingController::class, 'show'])->name('savings.show');
    Route::get('/savings/{id}/edit', [SavingController::class, 'edit'])->name('savings.edit');
    Route::put('/savings/{id}', [SavingController::class, 'update'])->name('savings.update');
    Route::delete('/savings/{id}', [SavingController::class, 'destroy'])->name('savings.destroy');

    // loan_payment
    Route::get('/loanPayments/create', [LoanPaymentController::class, 'create'])->name('loanPayments.create');

    // withdrawla
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::get('/withdrawals/{id}/edit', [WithdrawalController::class, 'edit'])->name('withdrawals.edit');
    Route::put('/withdrawals/{id}', [WithdrawalController::class, 'update'])->name('withdrawals.update');
    Route::delete('/withdrawals/{id}', [WithdrawalController::class, 'destroy'])->name('withdrawals.destroy');

    // pos
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/submit-sale', [PosController::class, 'store']);

    // items
    Route::get('/items', [MasterItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [MasterItemController::class, 'create'])->name('items.create');
    Route::post('/items', [MasterItemController::class, 'store'])->name('items.store');
    Route::get('/items/import', [MasterItemController::class, 'downloadTemplate'])->name('items.template');
    Route::post('/items/import', [MasterItemController::class, 'import'])->name('items.import');
    Route::get('/items/{id}', [MasterItemController::class, 'show'])->name('items.show');
    Route::get('/items/{id}/edit', [MasterItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [MasterItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [MasterItemController::class, 'destroy'])->name('items.destroy');

    //reports
    Route::get('/report', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/report/get', [ReportController::class, 'getReport'])->name('reports.getReport');
    Route::get('/report/pdf-deduction', [ReportController::class, 'deduction'])->name('reports.deductionPdf');
    Route::get('/report/xlsx-deduction', [ReportController::class, 'exportPotonganGajiExcel'])->name('reports.deductionXlsx');

    // repayments
    Route::get('/repayment', [RepaymentController::class, 'index'])->name('repayments.index');

    // inventories
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inv.index');

    // purchase
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchase/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchase/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');


});

// more role
Route::middleware([RoleMiddleware::class . ':admin,finance'])->group(function() {
    Route::get('/', function() {
        return view('dashboard');
    })->middleware([PermissionMiddleware::class . ':dashboard']);

    // loans
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::get('/loans/{id}/edit', [LoanController::class, 'edit'])->name('loans.edit');
    Route::put('/loans/{id}', [LoanController::class, 'update'])->name('loans.update');
    Route::delete('/loans/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');

});
