<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InboundMailController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::post('/mail/inbound', [InboundMailController::class, 'receive'])
    ->middleware('throttle:mail-inbound')
    ->name('mail.inbound.receive');

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

    Route::apiResource('/mailboxes', MailboxController::class)
        ->only(['index', 'store', 'show', 'update'])
        ->middleware('can:manage-mailboxes');

    Route::get('/contacts', [ContactController::class, 'index'])->middleware('can:view-mail-contacts')->name('mail.contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->middleware('can:manage-mail-contacts')->name('mail.contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->middleware('can:manage-mail-contacts')->name('mail.contacts.update');
    Route::post('/contacts/sync', [ContactController::class, 'sync'])->middleware('can:sync-mail-contacts')->name('mail.contacts.sync');

    Route::get('/mailboxes/{mailbox}/inbox', [InboundMailController::class, 'inbox'])
        ->middleware('can:view-mailboxes')
        ->name('mail.mailboxes.inbox');
});
