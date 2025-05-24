<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Events\Project\ChatMessagePublicEvent;
use Illuminate\Broadcasting\BroadcastController;


Route::get('/projects/{project}/chats', [ ChatController::class, 'getChats' ])->name('projects.chats');


Route::prefix("chats")->name("chats.")->group(function () { 
    
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::post('/create-direct-chat', [ChatController::class, 'createDirectChat'])->name('createDirectChat');
    Route::post('/{project}/create-group-chat', [ChatController::class, 'create'])->name('create');
    Route::post('/{project}/start-group-chat', [ChatController::class, 'startProjectChat'])->name('project.startChat');
    Route::post('/{chatUuid}/add-participants', [ChatController::class, 'addParticipant'])->name('add.participant');

    Route::post('/{chatUuid}/sned-message', [ChatController::class, 'sendMessage'])->name('project.send-message');
    Route::get('/{chatUuid}/get-messages', [ChatController::class, 'getMessages'])->name('get-messages');

    Route::get('/mark-unseen-messages/{chatUuid}', [ChatController::class, 'markSeenMessages'])->name('mark-unseen');
    Route::get('/send-private-message', function () {
        $userId = auth()->id(); // Assuming the user is authenticated
        $message = "This is a private message! form : {$userId}";
        
        broadcast(new ChatMessagePublicEvent($message, $userId))->toOthers();
    
        return 'Private message sent!';
    });
    
});

Route::post('broadcasting/auth', [BroadcastController::class, 'authenticate']);

