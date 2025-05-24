<?php

namespace App\Traits;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;

trait Timezone
{

    public function timezone_list(): array
    {

        $timezones = array();
      
        $regions = DateTimeZone::listIdentifiers(DateTimeZone::EUROPE);
        foreach ($regions as $key => $timezone) {
            $dateTime = new DateTime('now', new DateTimeZone($timezone));
            $offset = $dateTime->getOffset();
            $abbreviation = $dateTime->format('T');

            $hours = gmdate("h:m", abs($offset));
            $prefix = ($offset < 0 ? '-' : '+');
            $gmtOffset = Str::title($timezone)." UTC " . $prefix . $hours .' - '. $abbreviation;

            $timezones[$key]['value'] = $timezone;
            $timezones[$key]['label'] = $gmtOffset;
        }
        
        return $timezones;
    }
}
