<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\RetainingOfficer;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Role-based dashboard redirect ────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('Retaining Officer')) {
        return redirect()->route('ro.dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Admin Routes ─────────────────────────────────────────
Route::middleware(['auth', 'role:Super Admin,Admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Church Hierarchy
    Route::resource('church-categories', Admin\ChurchCategoryController::class)->except(['show']);
    Route::resource('church-groups', Admin\ChurchGroupController::class)->except(['show']);
    Route::resource('churches', Admin\ChurchController::class);

    // First Timers
    Route::get('first-timers/import', [Admin\FirstTimerController::class, 'importForm'])->name('first-timers.import');
    Route::post('first-timers/import', [Admin\FirstTimerController::class, 'import'])->name('first-timers.import.store');
    Route::resource('first-timers', Admin\FirstTimerController::class);

    // Foundation School
    Route::get('foundation-school', [Admin\FoundationSchoolController::class, 'index'])->name('foundation-school.index');
    Route::get('foundation-school/{firstTimer}', [Admin\FoundationSchoolController::class, 'show'])->name('foundation-school.show');
    Route::post('foundation-school/{firstTimer}/attendance', [Admin\FoundationSchoolController::class, 'recordAttendance'])->name('foundation-school.attendance');

    // User Management (Super Admin only via controller)
    Route::resource('users', Admin\UserController::class)->except(['show']);
});

// ── Retaining Officer Routes ─────────────────────────────
Route::middleware(['auth', 'role:Retaining Officer'])->prefix('retaining-officer')->name('ro.')->group(function () {

    Route::get('/dashboard', [RetainingOfficer\DashboardController::class, 'index'])->name('dashboard');

    // First Timers (scoped)
    Route::get('first-timers', [RetainingOfficer\FirstTimerController::class, 'index'])->name('first-timers.index');
    Route::get('first-timers/{firstTimer}', [RetainingOfficer\FirstTimerController::class, 'show'])->name('first-timers.show');

    // Foundation School (scoped)
    Route::get('foundation-school', [RetainingOfficer\FoundationSchoolController::class, 'index'])->name('foundation-school.index');
    Route::get('foundation-school/{firstTimer}', [RetainingOfficer\FoundationSchoolController::class, 'show'])->name('foundation-school.show');
    Route::post('foundation-school/{firstTimer}/attendance', [RetainingOfficer\FoundationSchoolController::class, 'recordAttendance'])->name('foundation-school.attendance');

    // Weekly Attendance
    Route::get('attendance', [RetainingOfficer\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/create', [RetainingOfficer\AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance', [RetainingOfficer\AttendanceController::class, 'store'])->name('attendance.store');
});

// ── Profile (Breeze default) ─────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
