<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\StaffScheduleController;
use App\Http\Controllers\DownpaymentController;
use App\Http\Controllers\PostProductionController;
use App\Http\Controllers\StaffPostProductionController;
use App\Http\Controllers\RescheduleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\OutsourcedStaffController;
use App\Http\Controllers\UserManagementController;

Route::get('/', [BookingController::class, 'index'])->name('home');

Route::get('/login', [ClientAccountController::class, 'showLogin'])->name('client.login');
Route::post('/login', [ClientAccountController::class, 'login'])->middleware('throttle:client-login')->name('client.login.post');
Route::post('/logout', [ClientAccountController::class, 'logout'])->name('client.logout');

Route::middleware('auth:client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/change-password', [ClientAccountController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [ClientAccountController::class, 'changePassword'])->name('change-password.post');
    Route::post('/reschedule', [RescheduleController::class, 'store'])->name('reschedule.store');
    Route::post('/cancel', [CancellationController::class, 'store'])->name('cancellation.store');
    Route::post('/downpayment', [DownpaymentController::class, 'submit'])->name('downpayment.submit');
    Route::post('/downpayment/{downpayment}/resubmit', [DownpaymentController::class, 'resubmit'])->name('downpayment.resubmit');
    Route::post('/final-payment', [DownpaymentController::class, 'submitFinalPayment'])->name('final-payment.submit');
    Route::post('/final-payment/{downpayment}/resubmit', [DownpaymentController::class, 'resubmitFinalPayment'])->name('final-payment.resubmit');
});

Route::get('/admin/login', Login::class)->name('login');
Route::post('/admin/login', [Login::class, 'login'])->middleware('throttle:login')->name('login.post');
Route::post('/admin/logout', [Login::class, 'logout'])->name('logout');

Route::get('/staff/login', [StaffPostProductionController::class, 'showLogin'])->name('staff.login');
Route::post('/staff/login', [StaffPostProductionController::class, 'login'])->name('staff.login.post');
Route::post('/staff/logout', [StaffPostProductionController::class, 'logout'])->name('staff.logout');

Route::middleware('auth:staff')->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffPostProductionController::class, 'dashboard'])->name('dashboard');
    Route::get('/task/{task}', [StaffPostProductionController::class, 'showTask'])->name('task.show');
    Route::put('/task/{task}', [StaffPostProductionController::class, 'updateTask'])->name('task.update');
});

Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/status/{bookingRef}', [BookingController::class, 'status'])->name('booking.status');
Route::get('/api/booking/check-date', [BookingController::class, 'checkDate'])->name('booking.checkDate');
Route::get('/api/booking/check-inventory', [BookingController::class, 'checkInventory'])->name('booking.checkInventory');
Route::get('/api/booking/check-email', [BookingController::class, 'checkEmail'])->name('booking.checkEmail');

Route::post('/api/otp/generate', [OtpController::class, 'generate'])->name('otp.generate');
Route::post('/api/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/api/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard/dashboard');
    })->name('admin.dashboard');

    Route::get('/package', [PackageController::class, 'index'])->name('package');
    Route::post('/package', [PackageController::class, 'store'])->name('package.store');
    Route::get('/package/{package}/edit', [PackageController::class, 'edit'])->name('package.edit');
    Route::get('/package/{package}', [PackageController::class, 'show'])->name('package.show');
    Route::put('/package/{package}', [PackageController::class, 'update'])->name('package.update');
    Route::delete('/package/{package}', [PackageController::class, 'destroy'])->name('package.destroy');

    Route::get('/addon', [AddonController::class, 'index'])->name('addon.index');
    Route::post('/addon', [AddonController::class, 'store'])->name('addon.store');
    Route::get('/addon/{addon}/edit', [AddonController::class, 'edit'])->name('addon.edit');
    Route::get('/addon/{addon}', [AddonController::class, 'show'])->name('addon.show');
    Route::put('/addon/{addon}', [AddonController::class, 'update'])->name('addon.update');
    Route::delete('/addon/{addon}', [AddonController::class, 'destroy'])->name('addon.destroy');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{inventoryItem}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/api/inventory/search', [InventoryController::class, 'search'])->name('inventory.search');

    Route::get('/admin/booking', [BookingController::class, 'adminIndex'])->name('booking.admin.index');
    Route::post('/admin/booking/{booking}/approve', [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/admin/booking/{booking}/reject', [BookingController::class, 'reject'])->name('booking.reject');

    Route::get('/admin/payment-verification', [DownpaymentController::class, 'adminIndex'])->name('payment-verification.index');
    Route::post('/admin/payment-verification/{downpayment}/verify', [DownpaymentController::class, 'adminVerify'])->name('payment-verification.verify');
    Route::post('/admin/payment-verification/{downpayment}/reject', [DownpaymentController::class, 'adminReject'])->name('payment-verification.reject');

    Route::post('/admin/booking/{booking}/complete', [PostProductionController::class, 'markEventComplete'])->name('booking.complete');
    Route::get('/admin/post-production', [PostProductionController::class, 'index'])->name('post-production.index');
    Route::get('/admin/post-production/create/{booking}', [PostProductionController::class, 'create'])->name('post-production.create');
    Route::post('/admin/post-production/{booking}', [PostProductionController::class, 'store'])->name('post-production.store');
    Route::get('/admin/post-production/{postProduction}', [PostProductionController::class, 'show'])->name('post-production.show');
    Route::put('/admin/post-production/{postProduction}/notes', [PostProductionController::class, 'updateNotes'])->name('post-production.update-notes');
    Route::post('/admin/post-production/task/{task}/approve', [PostProductionController::class, 'approveTask'])->name('post-production.approve-task');
    Route::post('/admin/post-production/task/{task}/revision', [PostProductionController::class, 'requestRevision'])->name('post-production.request-revision');
    Route::post('/admin/post-production/task/{task}/link', [PostProductionController::class, 'adminUpdateTaskLink'])->name('post-production.admin-link');
    Route::post('/admin/post-production/task/{task}/outsourced-account', [PostProductionController::class, 'createOutsourcedAccount'])->name('post-production.outsourced-account');
    Route::post('/admin/post-production/{booking}/unlock', [PostProductionController::class, 'unlockDeliverables'])->name('post-production.unlock');

    Route::post('/admin/outsourced-staff', [OutsourcedStaffController::class, 'store'])->name('outsourced-staff.store');
    Route::put('/admin/outsourced-staff/{outsourcedStaff}', [OutsourcedStaffController::class, 'update'])->name('outsourced-staff.update');
    Route::delete('/admin/outsourced-staff/{outsourcedStaff}', [OutsourcedStaffController::class, 'destroy'])->name('outsourced-staff.destroy');

    Route::post('/admin/reschedule/{rescheduleRequest}/approve', [RescheduleController::class, 'approve'])->name('reschedule.approve');
    Route::post('/admin/reschedule/{rescheduleRequest}/reject', [RescheduleController::class, 'reject'])->name('reschedule.reject');

    Route::get('/admin/staff', [StaffController::class, 'index'])->name('staff.admin.index');
    Route::post('/admin/staff', [StaffController::class, 'store'])->name('staff.admin.store');
    Route::put('/admin/staff/{staff}', [StaffController::class, 'update'])->name('staff.admin.update');
    Route::post('/admin/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.admin.resetPassword');
    Route::post('/admin/staff/{staff}/generate-password', [StaffController::class, 'generatePassword'])->name('staff.admin.generatePassword');
    Route::post('/admin/staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.admin.toggleStatus');
    Route::delete('/admin/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.admin.destroy');

    Route::get('/admin/team', fn () => redirect()->route('staff.admin.index', ['tab' => 'teams']))->name('team.index');
    Route::post('/admin/team', [TeamController::class, 'store'])->name('team.store');
    Route::put('/admin/team/{team}', [TeamController::class, 'update'])->name('team.update');
    Route::post('/admin/team/{team}/toggle-status', [TeamController::class, 'toggleStatus'])->name('team.toggleStatus');
    Route::delete('/admin/team/{team}', [TeamController::class, 'destroy'])->name('team.destroy');

    Route::get('/admin/schedule', [StaffScheduleController::class, 'index'])->name('staff-schedule.index');
    Route::put('/admin/schedule/{schedule}', [StaffScheduleController::class, 'update'])->name('staff-schedule.update');
    Route::get('/admin/schedule/calendar', [StaffScheduleController::class, 'calendar'])->name('staff-schedule.calendar');
    Route::post('/api/staff-schedule/check-availability', [StaffScheduleController::class, 'checkAvailability'])->name('staff-schedule.checkAvailability');

    Route::get('/admin/cancellations', [CancellationController::class, 'adminIndex'])->name('cancellation.admin.index');
    Route::post('/admin/cancellations/{cancellation}/approve', [CancellationController::class, 'approve'])->name('cancellation.approve');
    Route::post('/admin/cancellations/{cancellation}/reject', [CancellationController::class, 'reject'])->name('cancellation.reject');

    Route::get('/admin/clients', [ClientAccountController::class, 'adminIndex'])->name('clients.admin.index');
    Route::post('/admin/clients/{account}/restore', [ClientAccountController::class, 'restore'])->name('clients.admin.restore');

    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/admin/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.admin.index');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.admin.store');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.admin.update');
    Route::post('/admin/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.admin.toggleStatus');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.admin.destroy');
});
