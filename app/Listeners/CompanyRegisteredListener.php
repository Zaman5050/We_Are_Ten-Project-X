<?php

namespace App\Listeners;

use App\Events\CompanyRegisteredEvent;
use App\Mail\CompanyRegisteredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyRegisteredListener
{

    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CompanyRegisteredEvent $event): void
    {
        logger('Start of handle function in '.__CLASS__);

        $data['company'] = $event?->company;
        $data['admin'] = $event?->admin;

        Mail::to($event?->company->email)->send(new CompanyRegisteredMail($data));
    }
}
