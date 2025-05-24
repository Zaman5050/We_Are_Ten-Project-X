<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('project-chat-channel.{receiverUserUuid}', function ($user, $receiverUserUuid) {
    return (int) $user->uuid === (int) $receiverUserUuid;
});

Broadcast::channel('project-chat', function ($user) {
    return true;
});

Broadcast::channel('tenant-leave-channel.{clientUserUuid}', function ($user, $clientUserUuid) {
    return $user->uuid === $clientUserUuid;
});

// routes/channels.php
Broadcast::channel('presence-chat', function ($user) {
    return ['id' => $user->id, 'uuid' => $user->uuid, 'name' => $user->name];
});
