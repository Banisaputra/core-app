<?php

use App\Http\Middleware\LoginAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\PermissionMiddleware;



// auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// admin access
Route::middleware([LoginAuth::class, RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/', function() {
        return view('dashboard');
    })->middleware([PermissionMiddleware::class . ':dashboard']);

    // member
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');


    // savings
    Route::get('/savings', [SavingController::class, 'index'])->name('savings.index');
    Route::get('/savings/create', [SavingController::class, 'create'])->name('savings.create');
    Route::post('/savings', [SavingController::class, 'store'])->name('savings.store');
    Route::get('/savings/{id}/edit', [SavingController::class, 'edit'])->name('savings.edit');
    Route::put('/savings/{id}', [SavingController::class, 'update'])->name('savings.update');
    Route::delete('/savings/{id}', [SavingController::class, 'destroy'])->name('savings.destroy');
});

