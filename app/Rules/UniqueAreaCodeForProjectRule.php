<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueAreaCodeForProjectRule implements ValidationRule
{
    protected $projectId;
    protected $projectAreaUuid;

    /**
     * Create a new rule instance.
     *
     * @param  int  $projectId
     * @param  string|null  $projectAreaUuid
     * @return void
     */
    public function __construct(string $projectId, ?string $projectAreaUuid = null)
    {
        $this->projectId = $projectId;
        $this->projectAreaUuid = $projectAreaUuid;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('project_areas')
                    ->where('project_uuid', $this->projectId)
                    ->where('area_code', $value)
                    ->whereNull('deleted_at');

        if ($this->projectAreaUuid) {
            $query->where('uuid', '<>', $this->projectAreaUuid);
        }

        if ($query->exists()) {
            $fail('The area code must be unique for the specified project.');
        }
    }
}
