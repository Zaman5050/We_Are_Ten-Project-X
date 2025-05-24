<?php

use App\Models\ProjectStage;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Carbon\Carbon;

if (! function_exists('currentUser')) {
    function currentUser()
    {
        return Auth::check() ? Auth::user() : null;
    }
}

if (! function_exists('currentUserId')) {
    function currentUserId()
    {
        return Auth::id();
    }
}


if (! function_exists('currentAdmin')) {
    function currentAdmin()
    {
        $admin = Auth::check() ? Auth::user() : null;

        if ($admin) {
            $admin->loadMissing("company.teams");
        }
        return $admin;
    }
}


if (! function_exists('tenant_route')) {
    function tenant_route($url, $params = [])
    {
        // Adding missing parameter: subdomain in tenant routes
        $params += ['subdomain' => request()->subdomain];
        return route('tenant.' . $url, $params);
    }
}

if (! function_exists('tenant_to_route')) {
    function tenant_to_route($name)
    {
        // Adding missing parameter: subdomain in tenant routes
        return to_route('tenant.' . $name, request()->subdomain);
    }
}

if (! function_exists('highilight_tenant_route')) {
    function highilight_tenant_route($prefix = '', $names = [], $extras = [])
    {

        $names = array_map(function ($name) use ($prefix) {
            return $prefix . $name;
        }, $names);

        $names = array_merge($names, $extras);
        return request()->routeIs($names) ? 'active' : '';
    }
}


if (! function_exists('token_decoded')) {
    function token_decoded($token = '')
    {
        return json_decode(base64_decode($token), true);
    }
}

if (! function_exists('getAllStatus')) {
    function getAllStatus(string $flag)
    {
        return Status::where('flag', $flag)->get();
    }
}

if (! function_exists('getStatus')) {
    function getStatus(string $uuid)
    {
        return Status::where('uuid', $uuid)->first();
    }
}

if (! function_exists('getStage')) {
    function getStage(string $uuid)
    {
        return ProjectStage::where('uuid', $uuid)->first();
    }
}

if (! function_exists('getMembers')) {
    function getMembers($uuids)
    {
        $uuids = is_array($uuids) ? $uuids : [$uuids];
        return $uuids ? User::whereIn('uuid', $uuids)->get() : collect();
    }
}

if (!function_exists('randomColor')) {
    function randomColor()
    {
        return sprintf('#%02X%02X%02X', mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
        // return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}

if (!function_exists('replaceDashWithNDash')) {
    function replaceDashWithNDash($str)
    {
        return preg_replace('/-(\d+)/',  '–$1', $str);
    }
}

if (!function_exists('getProjectName')) {
    function getProjectName($projectUuid)
    {
        return Project::whereUuid($projectUuid)->first()->name ?? 'Project Name';
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        return Carbon::parse($date)->format('d F');
    }
}

if (!function_exists('getYearFromDate')) {
    function getYearFromDate($date)
    {
        return Carbon::parse($date)->year;
    }
}

if (!function_exists('getDateDifference')) {
    function getDateDifference($startDate, $endDate)
    {
        $date1 = $startDate ? Carbon::parse($startDate) : now();
        $date2 = Carbon::parse($endDate);

        $diffInDays = $date1->diffInDays($date2, false); // Use false to get negative values if overdue

        return $diffInDays > 0 ? $diffInDays : abs($diffInDays);
    }
}

if (!function_exists('isFirstDateLessThanSecondDate')) {
    function isFirstDateLessThanSecondDate($startDate, $endDate)
    {
        $date1 = $startDate ? Carbon::parse($startDate) : now();
        $date2 = Carbon::parse($endDate);

        return $date2->isPast(); // Check if due date has passed
    }
}

if (!function_exists('convertToMinutes')) {
    function convertToMinutes($timeString = '')
    {

        $timeString = strval($timeString);
        if (!$timeString || is_null($timeString) || !is_string($timeString)) return '';

        // Define conversion rates
        $weekToDays = 7;
        $dayToHours = 24;
        $hourToMinutes = 60;

        // Initialize total minutes
        $totalMinutes = 0;

        // Define regex pattern to extract values
        $pattern = '/(\d+)\s*(w|d|h|m)/i';

        // Use preg_match_all to find all matches
        preg_match_all($pattern, $timeString, $matches, PREG_SET_ORDER);

        // Process each match
        foreach ($matches as $match) {
            $value = (int) $match[1];
            $unit = strtolower($match[2]);

            switch ($unit) {
                case 'w':
                    $totalMinutes += $value * $weekToDays * $dayToHours * $hourToMinutes;
                    break;
                case 'd':
                    $totalMinutes += $value * $dayToHours * $hourToMinutes;
                    break;
                case 'h':
                    $totalMinutes += $value * $hourToMinutes;
                    break;
                case 'm':
                    $totalMinutes += $value;
                    break;
            }
        }

        return $totalMinutes;
    }
}

if (!function_exists('convertFromMinutes')) {
    function convertFromMinutes($minutes = 0, $skipMinutes = false)
    {
        // Ensure $minutes is an integer and not null
        $totalMinutes = floatval($minutes);

        // Check if totalMinutes is less than or equal to zero
        if ($totalMinutes <= 0) {
            return '0s'; // Return '0s' for zero or invalid input
        }

        if (!$skipMinutes) {
            // Convert the minutes to total seconds only if skipMinutes is false
            $totalSeconds = floor($totalMinutes * 60);
        } else {
            $totalSeconds = $totalMinutes;
        }

        // Calculate hours, minutes, and remaining seconds
        $hours = floor($totalSeconds / 3600);
        $remainingMinutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        // Construct the human-readable format
        $readableTime = '';

        if ($hours > 0) {
            $readableTime .= $hours . 'h ';
        }
        if ($remainingMinutes > 0) {
            $readableTime .= $remainingMinutes . 'm ';
        }
        if ($seconds > 0 || $readableTime === '') {
            $readableTime .= $seconds . 's';
        }

        return trim($readableTime); // Remove any trailing spaces
    }
}

if (!function_exists('formatDateForDatabase')) {
    function formatDateForDatabase($value, $fromFormat = 'd/m/Y', $toFormat = 'Y-m-d')
    {
        if (!$value) return;
        try {
            return Carbon::createFromFormat($fromFormat, $value)->format($toFormat);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format: $value");
        }
    }
}

if (!function_exists('formatSecondsToTimeString')) {
    function formatSecondsToTimeString($timeInSeconds)
    {
        try {

            $hours = floor($timeInSeconds / 60);
            $minutes = $timeInSeconds % 60;
            $timeLoggegString =  $hours . ' hour' . ($hours != 1 ? 's' : '') . ' and ' . $minutes . ' minute' . ($minutes != 1 ? 's' : '');
            return $timeLoggegString;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Exception in formatSecondsToTimeString method in helper.php : $timeInSeconds");
        }
    }
}
