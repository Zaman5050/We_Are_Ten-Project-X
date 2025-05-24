<?php

namespace App\Listeners\Teams;

use App\Events\Teams\MemberRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Teams\TeamMemberRegisteredNotification;

class SendMemberRegisteredNotification
{
    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        logger(__CLASS__.' Listeners');

        $member = $event?->member;
        $company = $event?->company;
        $member->notify(new TeamMemberRegisteredNotification($company));
    }
}
