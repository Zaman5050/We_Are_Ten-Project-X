<?php

namespace App\Notifications\Teams;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamMemberRegisteredNotification extends Notification
{
    use Queueable;

    public $company;
    /**
     * Create a new notification instance.
     */
    public function __construct($company)
    {
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {

        $data = [
            'company_uuid' => $this->company?->uuid,
            'member_uuid' => $notifiable?->uuid
        ];

        $token = base64_encode(json_encode($data));

        $createPasswordLink = tenant_route('create-password.view', ['token' => $token]);

        return (new MailMessage)
        ->subject('Create Password - ' . config('app.name'))
        ->markdown('emails.teams.register', [
            'member' => $notifiable,
            'adminName' => $this->company?->name,
            'createPasswordLink' => $createPasswordLink
         ]); 
 
    }
}
