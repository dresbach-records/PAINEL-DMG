<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

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
});
