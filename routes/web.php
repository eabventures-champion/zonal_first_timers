<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\RetainingOfficer;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = \App\Models\HomepageSetting::all()->keyBy('key');
    return view('welcome', compact('settings'));
});

// ── Role-based dashboard redirect ────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('Retaining Officer')) {
        return redirect()->route('ro.dashboard');
    }

    if ($user->hasRole('Member')) {
        return redirect()->route('member.dashboard');
    }

    if ($user->hasRole('Bringer')) {
        return redirect()->route('bringer.dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Member Routes ────────────────────────────────────────
Route::middleware(['auth', 'role:Member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Member\DashboardController::class, 'index'])->name('dashboard');
});

// ── Admin Routes ─────────────────────────────────────────
Route::middleware(['auth', 'role:Super Admin,Admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('dashboard/update-target', [Admin\DashboardController::class, 'updateTarget'])->name('dashboard.update-target');

    // Homepage Settings (Super Admin Only)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('homepage-settings', [\App\Http\Controllers\HomepageSettingController::class, 'index'])->name('homepage-settings.index');
        Route::post('homepage-settings', [\App\Http\Controllers\HomepageSettingController::class, 'update'])->name('homepage-settings.update');
    });

    // Church Hierarchy
    Route::resource('church-categories', Admin\ChurchCategoryController::class)->except(['show']);
    Route::resource('church-groups', Admin\ChurchGroupController::class)->except(['show']);
    Route::resource('churches', Admin\ChurchController::class);

    // First Timers & Members
    Route::get('members', [Admin\MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [Admin\MemberController::class, 'show'])->name('members.show');
    Route::get('members/{member}/edit', [Admin\MemberController::class, 'edit'])->name('members.edit');
    Route::put('members/{member}', [Admin\MemberController::class, 'update'])->name('members.update');
    // Route::post('first-timers/check-contact', [Admin\FirstTimerController::class, 'checkContact'])->name('first-timers.check-contact'); // Moved to shared
    Route::get('first-timers/import', [Admin\FirstTimerController::class, 'importForm'])->name('first-timers.import');
    Route::post('first-timers/import', [Admin\FirstTimerController::class, 'import'])->name('first-timers.import.store');
    Route::resource('first-timers', Admin\FirstTimerController::class);

    // Foundation School
    Route::get('foundation-school', [Admin\FoundationSchoolController::class, 'index'])->name('foundation-school.index');
    Route::post('foundation-school/classes', [Admin\FoundationSchoolController::class, 'storeClass'])->name('foundation-school.classes.store');
    Route::put('foundation-school/classes/{class}', [Admin\FoundationSchoolController::class, 'updateClass'])->name('foundation-school.classes.update');
    Route::delete('foundation-school/classes/{class}', [Admin\FoundationSchoolController::class, 'destroyClass'])->name('foundation-school.classes.destroy');
    Route::get('foundation-school/{id}', [Admin\FoundationSchoolController::class, 'show'])->name('foundation-school.show');
    Route::post('foundation-school/{id}/attendance', [Admin\FoundationSchoolController::class, 'recordAttendance'])->name('foundation-school.attendance');

    // Account Deletions
    Route::get('account-deletions', [Admin\AccountDeletionController::class, 'index'])->name('account-deletions.index');
    Route::post('account-deletions/{user}/approve', [Admin\AccountDeletionController::class, 'approve'])->name('account-deletions.approve');
    Route::post('account-deletions/{user}/deny', [Admin\AccountDeletionController::class, 'deny'])->name('account-deletions.deny');

    // Membership Approvals
    Route::get('membership-approvals', [Admin\MembershipApprovalController::class, 'index'])->name('membership-approvals.index');
    Route::post('membership-approvals/{member}/approve', [Admin\MembershipApprovalController::class, 'approve'])->name('membership-approvals.approve');
    Route::post('membership-approvals/bulk-acknowledge', [Admin\MembershipApprovalController::class, 'bulkAcknowledge'])->name('membership-approvals.bulk-acknowledge');
    Route::post('membership-approvals/bulk-sync', [Admin\MembershipApprovalController::class, 'bulkSync'])->name('membership-approvals.bulk-sync');

    // User Management (Super Admin only via controller)
    Route::resource('users', Admin\UserController::class)->except(['show']);

    // Weekly Attendance
    Route::get('attendance', [Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/churches/{church}', [Admin\AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('attendance/toggle', [Admin\AttendanceController::class, 'toggle'])->name('attendance.toggle');

    // Bringers
    Route::get('bringers', [Admin\BringerController::class, 'index'])->name('bringers.index');
});

// Shared Utility Routes (Accessible by Admin and RO)
Route::middleware(['auth', 'role:Super Admin,Admin,Retaining Officer'])->group(function () {
    Route::post('admin/first-timers/check-contact', [Admin\FirstTimerController::class, 'checkContact'])->name('admin.first-timers.check-contact');
    Route::post('admin/bringers/check-contact', [Admin\BringerController::class, 'checkContact'])->name('admin.bringers.check-contact');
    Route::get('admin/bringers/church/{church}', [Admin\BringerController::class, 'getForChurch'])->name('admin.bringers.get-for-church');
});

// ── Bringer Routes ─────────────────────────────────────────
Route::middleware(['auth', 'role:Bringer'])->group(function () {
    Route::get('/bringer/dashboard', [\App\Http\Controllers\Bringer\DashboardController::class, 'index'])->name('bringer.dashboard');
});

// ── Retaining Officer Routes ─────────────────────────────
Route::middleware(['auth', 'role:Retaining Officer'])->prefix('retaining-officer')->name('ro.')->group(function () {

    Route::get('/dashboard', [RetainingOfficer\DashboardController::class, 'index'])->name('dashboard');

    // First Timers & Members (scoped)
    Route::get('members', [\App\Http\Controllers\RO\MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [\App\Http\Controllers\RO\MemberController::class, 'show'])->name('members.show');
    Route::get('first-timers', [RetainingOfficer\FirstTimerController::class, 'index'])->name('first-timers.index');
    Route::get('first-timers/create', [RetainingOfficer\FirstTimerController::class, 'create'])->name('first-timers.create');
    Route::post('first-timers', [RetainingOfficer\FirstTimerController::class, 'store'])->name('first-timers.store');
    Route::get('first-timers/import', [RetainingOfficer\FirstTimerController::class, 'importForm'])->name('first-timers.import.form');
    Route::post('first-timers/import', [RetainingOfficer\FirstTimerController::class, 'import'])->name('first-timers.import.store');
    Route::get('first-timers/{firstTimer}', [RetainingOfficer\FirstTimerController::class, 'show'])->name('first-timers.show');

    // Foundation School (scoped)
    Route::get('foundation-school', [RetainingOfficer\FoundationSchoolController::class, 'index'])->name('foundation-school.index');
    Route::get('foundation-school/{id}', [RetainingOfficer\FoundationSchoolController::class, 'show'])->name('foundation-school.show');
    Route::post('foundation-school/{id}/attendance', [RetainingOfficer\FoundationSchoolController::class, 'recordAttendance'])->name('foundation-school.attendance');

    // Weekly Attendance
    Route::get('attendance', [RetainingOfficer\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/create', [RetainingOfficer\AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance', [RetainingOfficer\AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('attendance/toggle', [RetainingOfficer\AttendanceController::class, 'toggle'])->name('attendance.toggle');

    // Bringers
    Route::get('bringers', [Admin\BringerController::class, 'index'])->name('bringers.index');
});

// ── Profile (Breeze default) ─────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/cancel-deletion', [ProfileController::class, 'cancelDeletion'])->name('profile.cancel-deletion');
});

require __DIR__ . '/auth.php';
