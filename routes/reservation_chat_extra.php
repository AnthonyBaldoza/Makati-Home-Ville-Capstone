<?php

use App\Http\Controllers\ReservationChatController;
use Illuminate\Support\Facades\Route;

// Dagdag na routes para sa chat: file, edit, unsend
Route::middleware('auth')->group(function () {
    Route::get('/reservation-chat/file/{message}', [ReservationChatController::class, 'file'])->name('reservation-chat.file');
    Route::put('/reservation-chat/message/{message}', [ReservationChatController::class, 'update'])->name('reservation-chat.update');
    Route::delete('/reservation-chat/message/{message}', [ReservationChatController::class, 'destroy'])->name('reservation-chat.destroy');
});
