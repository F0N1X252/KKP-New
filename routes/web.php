<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// --- Import Controllers Auth ---
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

// --- Import Controllers Frontend ---
use App\Http\Controllers\TicketController;

// --- Import Controllers Admin ---
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\StatusesController;
use App\Http\Controllers\Admin\PrioritiesController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CommentsController;
use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\TicketsController as AdminTicketsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route Home (Logic Redirect User vs Admin dengan Email Verification)
Route::get('/home', function () {
    // Cek apakah user sudah verify email
    if (!auth()->user()->email_verified_at) {
        return redirect()->route('verification.notice');
    }
    
    $route = Gate::denies('dashboard_access') ? 'tickets.create' : 'admin.home';
    
    if (session('status')) {
        return redirect()->route($route)->with('status', session('status'));
    }

    return redirect()->route($route);
})->middleware(['auth']);

// --- Authentication Routes ---
// Kita matikan register default Laravel UI karena kita akan membuatnya manual di bawah
Auth::routes(['register' => false, 'verify' => false]);

// Custom Register Routes (Dapat diakses Guest)
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// --- Email Verification Routes (Cross-Device Support) ---
// Verification notice (requires auth)
Route::get('email/verify', [VerificationController::class, 'show'])
    ->name('verification.notice')
    ->middleware('auth');

// Email verification handler (NO AUTH required for cross-device support)
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware(['signed', 'throttle:6,1']);

// Resend verification (requires auth)
Route::post('email/resend', [VerificationController::class, 'resend'])
    ->name('verification.resend')
    ->middleware(['auth', 'throttle:6,1']);

// --- Frontend Ticket Routes ---
// (Route ini untuk user biasa jika Anda memisahkan controller frontend dan backend)
Route::post('tickets/media', [AdminTicketsController::class, 'storeMedia'])->name('tickets.storeMedia');
Route::post('tickets/comment/{ticket}', [AdminTicketsController::class, 'storeComment'])->name('tickets.storeComment');
Route::resource('tickets', AdminTicketsController::class)->only(['show', 'create', 'store']);

// --- Admin / Dashboard Routes (Tanpa middleware verified untuk sementara) ---
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    
    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Dashboard data endpoint
    Route::get('/dashboard-data', [HomeController::class, 'getDashboardData'])->name('dashboard.data');

    // Permissions
    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionsController::class);

    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);

    // Users
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);

    // Statuses
    Route::delete('statuses/destroy', [StatusesController::class, 'massDestroy'])->name('statuses.massDestroy');
    Route::resource('statuses', StatusesController::class);

    // Priorities
    Route::delete('priorities/destroy', [PrioritiesController::class, 'massDestroy'])->name('priorities.massDestroy');
    Route::resource('priorities', PrioritiesController::class);

    // Categories
    Route::delete('categories/destroy', [CategoriesController::class, 'massDestroy'])->name('categories.massDestroy');
    Route::resource('categories', CategoriesController::class);

    // Tickets (Admin Side)
    Route::delete('tickets/destroy', [AdminTicketsController::class, 'massDestroy'])->name('tickets.massDestroy');
    Route::post('tickets/media', [AdminTicketsController::class, 'storeMedia'])->name('tickets.storeMedia');
    Route::post('tickets/comment/{ticket}', [AdminTicketsController::class, 'storeComment'])->name('tickets.storeComment');
    Route::delete('tickets/{ticket}/attachment', [AdminTicketsController::class, 'deleteAttachment'])->name('tickets.deleteAttachment');
    
    // Export Routes
    Route::get('tickets-export', [AdminTicketsController::class, 'export'])->name('tickets.export');
    Route::get('tickets/{ticket}/export', [AdminTicketsController::class, 'exportDetail'])->name('tickets.exportDetail');
    
    Route::resource('tickets', AdminTicketsController::class);

    // Comments
    Route::delete('comments/destroy', [CommentsController::class, 'massDestroy'])->name('comments.massDestroy');
    Route::resource('comments', CommentsController::class);

    // Audit Logs
    Route::resource('audit-logs', AuditLogsController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);
});

// Authentication Routes (Manual)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // Register
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    // Password Reset
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

// --- User Tickets Routes (Dengan pengecekan email verification manual) ---
Route::middleware(['auth'])->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::get('{ticket}', [TicketController::class, 'show'])->name('show');
});