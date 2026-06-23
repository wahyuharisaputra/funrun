<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminAuthController;

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Registration Flow
Route::get('/register-event', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register-event', [RegistrationController::class, 'submitRegistration']);
Route::get('/checkout/{participant_id}', [RegistrationController::class, 'checkout'])->name('checkout');

// Payment Mock
Route::post('/pay/{ticket_id}', [PaymentController::class, 'processPayment'])->name('payment.process');

// E-Ticket
Route::get('/ticket/{ticket_code}', [TicketController::class, 'showTicket'])->name('ticket.show');

// Admin Panel
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/participants', [AdminController::class, 'participants'])->name('participants');
        Route::get('/participants/{id}/edit', [AdminController::class, 'editParticipant'])->name('participants.edit');
        Route::put('/participants/{id}', [AdminController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{id}', [AdminController::class, 'deleteParticipant'])->name('participants.delete');
        Route::get('/export-csv', [AdminController::class, 'exportCSV'])->name('export');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::post('/payments/{id}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
        Route::get('/scanner', [AdminController::class, 'scanner'])->name('scanner');
        Route::post('/scan', [AdminController::class, 'scanTicket'])->name('scan');
    });
});
