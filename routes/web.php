<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReservationCalendarController;
use App\Http\Controllers\ReservationChatController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\PreventReservationOverlap;
use Illuminate\Support\Facades\Route;

// ---------------- Facility Reservations ----------------
Route::middleware('auth')->group(function () {
    // /reservations = calendar (main page)
    Route::get('/reservations', [ReservationCalendarController::class, 'index'])->name('reservations.index');
    // table ng reservations
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.list');

    Route::get('/reservations-create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])
        ->middleware(PreventReservationOverlap::class)
        ->name('reservations.store');
});

// ---------------- Auth ----------------
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index']);

// ---------------- Announcements ----------------
Route::middleware('auth')->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
});

Route::middleware(['auth', 'role:HOA Admin'])->group(function () {
    Route::get('/announcements-create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

// ---------------- Complaints ----------------
Route::middleware('auth')->group(function () {
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints-create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
});

Route::middleware(['auth', 'role:HOA Admin'])->group(function () {
    Route::put('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
});

// ---------------- Password reset ----------------
Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

// ---------------- Community Events ----------------
Route::middleware('auth')->group(function () {
    Route::get('/community-events', [CommunityEventController::class, 'index'])->name('community-events.index');
    Route::get('/community-events/feed', [CommunityEventController::class, 'feed'])->name('community-events.feed');
    Route::post('/community-events', [CommunityEventController::class, 'store']);
    Route::put('/community-events/{event}', [CommunityEventController::class, 'update']);
    Route::delete('/community-events/{event}', [CommunityEventController::class, 'destroy']);
});

// ---------------- Reservation Calendar + Chat ----------------
Route::middleware('auth')->group(function () {
    Route::get('/reservations-calendar', [ReservationCalendarController::class, 'index'])->name('reservations.calendar');
    Route::get('/reservations-calendar/feed', [ReservationCalendarController::class, 'feed'])->name('reservations.calendar.feed');
    Route::post('/reservations-calendar', [ReservationCalendarController::class, 'store'])->name('reservations.calendar.store');
    Route::put('/reservations-calendar/{reservation}', [ReservationCalendarController::class, 'update'])->name('reservations.calendar.update');
    Route::delete('/reservations-calendar/{reservation}', [ReservationCalendarController::class, 'destroy'])->name('reservations.calendar.destroy');
    Route::post('/reservations-calendar/{reservation}/review', [ReservationCalendarController::class, 'review'])->name('reservations.calendar.review');

    // Chat para sa facility reservation
    Route::get('/reservation-chat/threads', [ReservationChatController::class, 'threads']);
    Route::get('/reservation-chat/{resident}', [ReservationChatController::class, 'messages'])->whereNumber('resident');
    Route::post('/reservation-chat/{resident}', [ReservationChatController::class, 'send'])->whereNumber('resident');
    
});

require __DIR__.'/reservation_chat_extra.php';