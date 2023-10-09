<?php

namespace App\Utils;

class DateTimeUtil
{

    public static function seconds(string $s): int
    {
        $formatError = 'the format of $s parameter is not valid';

        $accepted = [
            "y" => 360 * 24 * 60 * 60,
            "m" => 30 * 24 * 60 * 60,
            "w" => 7 * 24 * 60 * 60,
            "d" => 24 * 60 * 60,
            "h" => 60 * 60,
            "min" => 60,
            "s" => 1,
            "ms" => 0.001,
            "mics" => 0.000001,
        ];

        if (preg_match("/\d+", $s, $matched) === false) throw new \ValueError($formatError);

        $num = $matched[0];

        $unit = str_replace($num, "", $s);

        if (!in_array($unit, array_keys($accepted))) throw  new \ValueError($formatError);

        return $num * $accepted[$unit];

    }

}
