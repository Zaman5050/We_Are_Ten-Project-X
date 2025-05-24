<?php

namespace App\Events\Teams;

use Illuminate\Foundation\Events\Dispatchable;

class MemberRegistered
{
    use Dispatchable;

    public $member;
    public $company;

    public function __construct($member, $company)
    {
        logger(__CLASS__.' Event');

        $this->member = $member;
        $this->company = $company;
    }
}
