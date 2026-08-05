<?php

use App\Http\Controllers\AdjustmentTypeController;
use App\Http\Controllers\AdvanceTypeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::post('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['auth', 'role.restrict'])->group(function (): void {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);

    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('employees', EmployeeController::class);

    Route::get('attendances/daily', [AttendanceController::class, 'daily'])->name('attendances.daily');
    Route::post('attendances/daily', [AttendanceController::class, 'storeDaily'])->name('attendances.daily.store');
    Route::resource('attendances', AttendanceController::class)->except(['create', 'store', 'show']);

    Route::get('payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::post('payrolls/generate', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::post('payrolls/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payrolls.mark-paid');
    Route::post('payrolls/{payroll}/mark-unpaid', [PayrollController::class, 'markUnpaid'])->name('payrolls.mark-unpaid');
    Route::post('payrolls/{payroll}/adjustments', [PayrollController::class, 'storeAdjustment'])->name('payrolls.adjustments.store');
    Route::delete('payrolls/{payroll}/adjustments/{adjustment}', [PayrollController::class, 'destroyAdjustment'])->name('payrolls.adjustments.destroy');

    Route::resource('advance-types', AdvanceTypeController::class)->except(['show']);
    Route::resource('adjustment-types', AdjustmentTypeController::class)->except(['show']);

    Route::get('cash-advances/daily', [CashAdvanceController::class, 'daily'])->name('cash-advances.daily');
    Route::post('cash-advances/daily', [CashAdvanceController::class, 'storeDaily'])->name('cash-advances.daily.store');
    Route::resource('cash-advances', CashAdvanceController::class)->except(['show']);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
    Route::get('reports/cash-advances', [ReportController::class, 'cashAdvances'])->name('reports.cash-advances');
});
