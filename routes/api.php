<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\Admin\PermissionsApiController;
use App\Http\Controllers\Api\V1\Admin\RolesApiController;
use App\Http\Controllers\Api\V1\Admin\UsersApiController;
use App\Http\Controllers\Api\V1\Admin\StatusesApiController;
use App\Http\Controllers\Api\V1\Admin\PrioritiesApiController;
use App\Http\Controllers\Api\V1\Admin\CategoriesApiController;
use App\Http\Controllers\Api\V1\Admin\TicketsApiController;
use App\Http\Controllers\Api\V1\Admin\CommentsApiController;

// Authentication routes (tanpa middleware)
Route::prefix('v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Protected API routes
Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', PermissionsApiController::class);

    // Roles
    Route::apiResource('roles', RolesApiController::class);

    // Users
    Route::get('users/dropdown', [UsersApiController::class, 'dropdown'])->name('users.dropdown');
    Route::apiResource('users', UsersApiController::class);

    // Statuses
    Route::get('statuses/dropdown', [StatusesApiController::class, 'dropdown'])->name('statuses.dropdown');
    Route::apiResource('statuses', StatusesApiController::class);

    // Priorities
    Route::get('priorities/dropdown', [PrioritiesApiController::class, 'dropdown'])->name('priorities.dropdown');
    Route::apiResource('priorities', PrioritiesApiController::class);

    // Categories
    Route::get('categories/dropdown', [CategoriesApiController::class, 'dropdown'])->name('categories.dropdown');
    Route::apiResource('categories', CategoriesApiController::class);

    // Tickets
    Route::delete('tickets/bulk', [TicketsApiController::class, 'bulkDelete'])->name('tickets.bulkDelete');
    Route::post('tickets/media', [TicketsApiController::class, 'storeMedia'])->name('tickets.storeMedia');
    Route::apiResource('tickets', TicketsApiController::class);

    // Comments
    Route::apiResource('comments', CommentsApiController::class);
    
    // Combined dropdown endpoint
    Route::get('dropdown/all', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'statuses' => \App\Status::select('id', 'name')->get(),
                'priorities' => \App\Priority::select('id', 'name')->get(),
                'categories' => \App\Category::select('id', 'name')->get(),
                'users' => \App\User::select('id', 'name', 'email')->get(),
            ]
        ]);
    })->name('dropdown.all');
});

// Public API routes (tanpa authentication)
Route::group(['prefix' => 'v1/public', 'as' => 'api.public.'], function () {
    Route::post('tickets', [TicketsApiController::class, 'publicStore'])->name('tickets.store');
});
