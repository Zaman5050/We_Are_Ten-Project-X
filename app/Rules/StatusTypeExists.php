<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class StatusTypeExists implements ValidationRule
{

    protected $type;

    public function __construct($type = 'task')
    {
        $this->type = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!DB::table('statuses')->where('uuid', $value)->where('flag', $this->type)->exists()){
            $fail("Invalid status");
        }
    }
}
