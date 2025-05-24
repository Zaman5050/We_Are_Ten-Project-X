import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key:'078d34241c1bbbcdaa74',
    cluster:'eu',
    wsHost:`ws-eu.pusher.com`,
    wsPort: 80,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${$('meta[name="csrf-token"]').attr('content')}`,
        },
    },
});

const isChatPage = window.location.href.match('chats');
if (isChatPage != null && isChatPage != false) {
    console.log(import.meta.env);
}