<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TicketController;
use \App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('tickets', TicketController::class)->except([
    'create',
    'edit',
    'destroy',
]);

Route::resource('messages', MessageController::class)->only([
    'store',
]);

Route::resource('organizations', OrganizationController::class)->only([
    'update',
]);

Route::prefix('tickets')->group(function () {
    Route::get('/organization/{organizationId}', [TicketController::class, 'getByOrganization']);
});

Route::get('/attachment/{attachment}/download', [AttachmentController::class, 'downloadFileContents']);

Route::patch('/tickets/{ticket}/reopen', [TicketController::class, 'reopenTicket']);
Route::patch('/tickets/{ticket}/close/{success?}', [TicketController::class, 'closeTicket']);