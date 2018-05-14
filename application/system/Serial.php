<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2011-07-11
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class Serial
{
    public static function jumble(array &$arr, int $check, int $encode) : void
    {
        $key = array(6,7,9,5,1,4,5,4,2,8,1,8,7,2,3,4,1,7,5,6,9,2,7,7);
        $check = $check % count($key);
        for ($i = 0; $i < count($arr); ++$i)
            $arr[$i] = ($arr[$i] + ($key[($i+$check) % count($key)]*$encode) + 10000) % 10;
    }

    public static function getDaysSince1970() : float
    {
        $t1 = new \DateTime('1970-01-01');
        $t2 = new \DateTime("now");
        return floor(($t2->getTimestamp() - $t1->getTimestamp()) / 86400); // returns float
    }

    public static function isValidSerial(string $str) : bool
    {
        $str = str_replace(array(' ','-'), '', $str);
        if (strlen($str) != 12)
            return false;

        $check1 = (int)substr($str, 0, 1);
        $check2 = (int)substr($str, -2);
        $str = substr($str, 1, strlen($str)-3);

        $arr = array();
        for ($i = 0; $i < strlen($str); ++$i)
            $arr[] = (int)substr($str, $i, 1);

        self::jumble($arr, $check2, -1);
        $num = (int)implode('', $arr);

        $calc_check1 = (($arr[1]*2) + ($arr[2]*7) + ($arr[3]*6) + ($arr[4]*5) + ($arr[5]*4) + ($arr[6]*3) + ($arr[7]*2) + $arr[8]) % 7;
        $calc_check2 = round(fmod(($num*100)+12, 97));

        return ($calc_check1 == $check1) && ($calc_check2 == $check2);
    }

    public static function formatSerial(string $str) : string
    {
        $str = str_replace(array(' ','-'), '', $str);
        if (strlen($str) != 12)
            return $str;
        return substr($str, 0,4) . '-' . substr($str, 4,4) . '-' . substr($str, 8,4);
    }
}
