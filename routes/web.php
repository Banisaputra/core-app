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
use App\Http\Controllers\SavingController;
use App\Http\Controllers\EmployeeController;
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

    // member
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
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
    Route::get('/items/{id}', [MasterItemController::class, 'show'])->name('items.show');
    Route::get('/items/{id}/edit', [MasterItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [MasterItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [MasterItemController::class, 'destroy'])->name('items.destroy');

});

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
