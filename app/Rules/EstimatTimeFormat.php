<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EstimatTimeFormat implements ValidationRule
{
    public $message = '';
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $isValid = true;
        // Trim input value
        $value = trim($value);

        // Define the pattern and maximum values
        $formatPattern = '/^(\d+w\s*)?(\d+d\s*)?(\d+h\s*)?(\d+m\s*)?$/i';
        $maxValues = ['w' => 52, 'd' => 30, 'h' => 24, 'm' => 60];

        // Check if input matches the format
        if (!preg_match($formatPattern, $value)) {
            $this->message = 'Invalid format. Use "2w 3d 4h 30m".';
            $isValid = false;
        }

        // Extract values using regex
        preg_match_all('/(\d+)([wdhm])/i', $value, $matches, PREG_SET_ORDER);

        // Convert matches to key-value pairs for easy validation
        $values = [];
        foreach ($matches as $match) {
            $values[$match[2]] = (int) $match[1];
        }

        // Validate the extracted values against the maximum limits
        foreach ($maxValues as $unit => $max) {
            if (isset($values[$unit]) && $values[$unit] > $max) {
                $this->message = "Value for $unit exceeds maximum ($max) allowed.";
                $isValid = false;
            }
        }

        if (!$isValid) {
            $fail('The :attribute format is invalid. Use like format, e.g., "1d 2h 30m".');
        }
    }
}
