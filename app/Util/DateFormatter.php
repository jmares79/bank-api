<?php

namespace App\Util;

class DateFormatter
{
    public function getDateForStoring($from = 'yesterday')
    {
        $date = new \DateTime();

        $date->add(\DateInterval::createFromDateString($from));

        return [$date->format('Y-n-d').' 00:00:00', $date->format('Y-n-d').' 23:59:59'];
    }
}
