<?php


namespace App\Util;


class Statistics
{
    /**
     *
     *
     * @param array $arr
     * @return float
     */
    static function quartiles(array $arr): array
    {
        sort($arr);
        $second = Statistics::median($arr);
        $tmp = array();

        foreach ($arr as $key => $val) {
            if ($val > $second) {
                $tmp['third'][] = $val;
            } else if ($val < $second) {
                $tmp['first'][] = $val;
            }
        }

        $quartiles = [
            'first' => Statistics::median($tmp['first']),
            'second' => $second,
            'third' => Statistics::median($tmp['third']),
        ];

        return $quartiles;
    }

    /**
     *
     * Courtesy of Jared Eckersley (http://blog.jaredeckersley.com/php-quartile-function/)
     *
     * @param array $arr
     * @return float
     */
    static function median(array $arr): float
    {
        $count = count($arr);
        $middleValue = floor(($count - 1) / 2); // find the middle value, or the lowest middle value

        if ($count % 2) { // odd number, middle is the median
            $median = $arr[$middleValue];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleValue];
            $high = $arr[$middleValue + 1];
            $median = (($low + $high) / 2);
        }

        return number_format((float) $median, 2, '.', '');
    }
}