<?php

declare(strict_types=1);

use App\Chat\Presentation\Controller\SendMessageController;
use App\Chat\Presentation\Controller\ListMessagesController;
use App\Chat\Presentation\Controller\ShowConversationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'account.active', 'verified'])->group(static function (): void {
    Route::get('/conversations/{conversation}', ShowConversationController::class)
        ->whereNumber('conversation')
        ->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', SendMessageController::class)
        ->whereNumber('conversation')
        ->name('conversations.messages.send');
});

Route::middleware(['auth', 'account.active', 'verified', 'dating.profile'])->group(static function (): void {
    Route::get('/messages', ListMessagesController::class)
        ->name('messages.index');
});
