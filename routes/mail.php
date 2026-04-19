<?php

use App\Http\Controllers\CampaignController;
 codex/task-title-htqlyo
use App\Http\Controllers\InboundMailController;
use App\Http\Controllers\MailboxController;

 main
use App\Http\Controllers\MailController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

 codex/task-title-htqlyo
Route::post('/mail/inbound', [InboundMailController::class, 'receive'])
    ->middleware('throttle:mail-inbound')
    ->name('mail.inbound.receive');


 main
Route::middleware(['auth:sanctum'])->prefix('mail')->group(function (): void {
    Route::get('/dashboard', [MailController::class, 'dashboard'])->name('mail.dashboard');

    Route::post('/send', [MailController::class, 'send'])->middleware('can:send-mail')->name('mail.send');
    Route::get('/logs', [MailController::class, 'logs'])->middleware('can:view-mail-logs')->name('mail.logs');

    Route::apiResource('/templates', TemplateController::class)
        ->middleware('can:manage-mail-templates');

    Route::apiResource('/campaigns', CampaignController::class)
        ->middleware('can:manage-mail-campaigns');

    Route::post('/campaigns/{campaign}/dispatch', [CampaignController::class, 'dispatch'])
        ->middleware('can:dispatch-mail-campaigns')
        ->name('mail.campaigns.dispatch');
 codex/task-title-htqlyo

    Route::apiResource('/mailboxes', MailboxController::class)
        ->only(['index', 'store', 'show', 'update'])
        ->middleware('can:manage-mailboxes');

    Route::get('/mailboxes/{mailbox}/inbox', [InboundMailController::class, 'inbox'])
        ->middleware('can:view-mailboxes')
        ->name('mail.mailboxes.inbox');

 main
});
