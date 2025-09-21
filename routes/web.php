<?php

use App\Http\Middleware\LoginAuth;
use App\Http\Middleware\SqlRunnerKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DevisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RepaymentController;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\SavingTypeController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\StorageLinkController;


// auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// poweruser
Route::middleware([SqlRunnerKey::class])->group(function () {
    Route::get('/sql-runner', [\App\Http\Controllers\SqlRunnerController::class, 'index'])->name('sql.index');
    Route::post('/sql-runner/run', [\App\Http\Controllers\SqlRunnerController::class, 'run'])->name('sql.run');
});

// admin access
Route::middleware([LoginAuth::class, RoleMiddleware::class . ':administrator'])->group(function () {
    Route::get('/backup-db', function () {
        // Jalankan command dan ambil path file
        $exitCode = Artisan::call('db:backup', ['--download' => true]);
        $result = Artisan::output();
        $filePath = cache('last_backup_file');

        // pastikan file ada
        $filePath = trim(str_replace(["\n", "\r"], '', $filePath));
        if (!file_exists($filePath)) {
            return back()->with('error', 'Backup gagal dibuat.');
        }

        return Response::download($filePath);
    });

    Route::get('/backup/download', [UserController::class, 'downloadDB']);
    
    // storage link
    Route::get('/create-storage-link', [StorageLinkController::class, 'create']);

    // role
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/asign', [RoleController::class, 'asign'])->name('roles.asign');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/asign', [RoleController::class, 'updateRoles'])->name('roles.asigned');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

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
    
    // devisions
    Route::get('/devision', [DevisionController::class, 'index'])->name('devision.index');
    Route::get('/devision/create', [DevisionController::class, 'create'])->name('devision.create');
    Route::get('/devision/import', [DevisionController::class, 'downloadTemplate'])->name('devision.template');
    Route::post('/devision/import', [DevisionController::class, 'import'])->name('devision.import');
    Route::post('/devision', [DevisionController::class, 'store'])->name('devision.store');
    Route::get('/devision/{id}/edit', [DevisionController::class, 'edit'])->name('devision.edit');
    Route::put('/devision/{id}', [DevisionController::class, 'update'])->name('devision.update');
    Route::delete('/devision/{id}', [DevisionController::class, 'destroy'])->name('devision.destroy');
    
    // positions
    Route::get('/position', [PositionController::class, 'index'])->name('position.index');
    Route::get('/position/create', [PositionController::class, 'create'])->name('position.create');
    Route::get('/position/import', [PositionController::class, 'downloadTemplate'])->name('position.template');
    Route::post('/position/import', [PositionController::class, 'import'])->name('position.import');
    Route::post('/position', [PositionController::class, 'store'])->name('position.store');
    Route::get('/position/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    
    // member
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::get('/members/import', [MemberController::class, 'downloadTemplate'])->name('members.template');
    Route::post('/members/import', [MemberController::class, 'import'])->name('members.import');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::post('/members/account', [MemberController::class, 'account'])->name('members.account');
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{id}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');
    
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
    
});

Route::get('/', [UserController::class, 'dashboard'])->middleware([PermissionMiddleware::class . ':dashboard']);

// role general
Route::middleware([RoleMiddleware::class . ':administrator,kepala koperasi,bendahara,kepala toko,admin toko,badan pengawas'])->group(function() {
    // search
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::get('/api/roles/search', [RoleController::class, 'search']);
    Route::get('/api/members/search', [MemberController::class, 'search']);
    Route::get('/api/items/search', [MasterItemController::class, 'search']);
    Route::get('/api/category/search', [CategoryController::class, 'search']);
    Route::get('/api/category/{id}/margin', [CategoryController::class, 'getMargin']);
    Route::get('/api/supplier/search', [SupplierController::class, 'search']);
    Route::get('/api/saving-type/search', [SavingTypeController::class, 'search']);

    // profile
    Route::get('/setting/profile', [UserController::class, 'profile'])->name('setting.profile');

    //reports
    Route::get('/report', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/report2', [ReportController::class, 'index2'])->name('reports.index2');
    Route::get('/report/pdf-deduction', [ReportController::class, 'deduction'])->name('reports.deductionPdf');
    Route::get('/report/xlsx-deduction', [ReportController::class, 'exportPotonganGajiExcel'])->name('reports.deductionXlsx');
    Route::post('/report/get', [ReportController::class, 'getReport'])->name('reports.getReport');
    Route::post('/report/get2', [ReportController::class, 'getMemberList'])->name('reports.getMemberList');
    Route::post('/report/get3', [ReportController::class, 'getMemberDetail'])->name('reports.getMemberDetail');
    Route::post('/report/pdf-loanInfo', [ReportController::class, 'loanInfo'])->name('reports.loanInfo');

});

Route::middleware([RoleMiddleware::class . ':administrator,kepala koperasi,bendahara'])->group(function() {
    // loans
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::get('/loans/{id}/edit', [LoanController::class, 'edit'])->name('loans.edit');
    Route::put('/loans/{id}', [LoanController::class, 'update'])->name('loans.update');
    Route::delete('/loans/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');

    // saving_type
    Route::prefix('saving-types')->name('saving-types.')->group(function () {
        Route::get('/', [SavingTypeController::class, 'index'])->name('index');
        Route::post('/', [SavingTypeController::class, 'store'])->name('store');
        Route::post('/schedule', [SavingTypeController::class, 'schedule'])->name('schedule');
        Route::get('/{id}/edit', [SavingTypeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SavingTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [SavingTypeController::class, 'destroy'])->name('destroy');
    });

    // savings
    Route::get('/savings', [SavingController::class, 'index'])->name('savings.index');
    Route::get('/savings/create', [SavingController::class, 'create'])->name('savings.create');
    Route::get('/savings/generate', [SavingController::class, 'generate'])->name('savings.generate');
    Route::post('/savings/generate', [SavingController::class, 'generated'])->name('savings.generated');
    Route::post('/savings', [SavingController::class, 'store'])->name('savings.store');
    Route::post('/savings/confirm', [SavingController::class, 'confirmation'])->name('savings.confirm');
    Route::get('/savings/{id}', [SavingController::class, 'show'])->name('savings.show');
    Route::get('/savings/{id}/edit', [SavingController::class, 'edit'])->name('savings.edit');
    Route::put('/savings/{id}', [SavingController::class, 'update'])->name('savings.update');
    Route::delete('/savings/{id}', [SavingController::class, 'destroy'])->name('savings.destroy');

    // loan_payment
    Route::get('/loanPayments/create', [LoanPaymentController::class, 'create'])->name('loanPayments.create');
    Route::post('/loanPayments', [LoanPaymentController::class, 'settle'])->name('loanPayments.settle');
    
    // repayments
    Route::get('/repayment', [RepaymentController::class, 'index'])->name('repayments.index');
    Route::get('/repayment/create', [RepaymentController::class, 'create'])->name('repayments.create');
    Route::get('/repayment/generate', [RepaymentController::class, 'generate'])->name('repayments.generate');
    Route::post('/repayment/generate', [RepaymentController::class, 'generated'])->name('repayments.generated');
    Route::post('/repayment', [RepaymentController::class, 'settle'])->name('repayments.settle');
    Route::post('/repayment/settle', [RepaymentController::class, 'getSettle'])->name('repayments.getSettle');
    Route::post('/repayment/settle-confirm', [RepaymentController::class, 'settleConfirm'])->name('repayments.settleConfirm');

    // withdrawla
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/withdrawals/confirm', [WithdrawalController::class, 'confirmation'])->name('withdrawals.confirm');
    Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::get('/withdrawals/{id}/edit', [WithdrawalController::class, 'edit'])->name('withdrawals.edit');
    Route::put('/withdrawals/{id}', [WithdrawalController::class, 'update'])->name('withdrawals.update');
    Route::delete('/withdrawals/{id}', [WithdrawalController::class, 'destroy'])->name('withdrawals.destroy');

    // policy
    Route::get('/policy', [PolicyController::class, 'index'])->name('policy.index');
    Route::post('/policy', [PolicyController::class, 'uploadTerms'])->name('policy.upload');
    Route::post('/policy-loanUmum', [PolicyController::class, 'loanUmum'])->name('policy.loanUmum');
    Route::post('/policy-loanKhusus', [PolicyController::class, 'loanKhusus'])->name('policy.loanKhusus');
    Route::post('/policy-loanAgunan', [PolicyController::class, 'loanAgunan'])->name('policy.loanAgunan');
    Route::post('/policy-general', [PolicyController::class, 'general'])->name('policy.general');
    Route::delete('/policy-agunan/{id}', [PolicyController::class, 'agDestroy'])->name('policy.agDestroy');


});

Route::middleware([RoleMiddleware::class . ':administrator,kepala toko,admin toko'])->group(function() {
    // pos
    Route::get('/pos', [PosController::class, 'index2'])->name('pos.index');
    Route::post('/submit-sale', [PosController::class, 'store']);
    Route::get('/sales/{id}/print', [PosController::class, 'printReceipt'])->name('sales.print');

    // purchase
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::post('/purchase/confirm', [PurchaseController::class, 'confirmation'])->name('purchases.confirm');
    Route::get('/purchase/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchase/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    // inventories
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inv.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inv.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inv.store');
    Route::post('/inventory/confirm', [InventoryController::class, 'confirmation'])->name('inv.confirm');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inv.show');
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inv.edit');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inv.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inv.destroy');

    // setting 
    Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
    Route::post('/business-sales', [BusinessController::class, 'sales'])->name('business.sales');

});
