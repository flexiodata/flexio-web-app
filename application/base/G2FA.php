<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-02-10
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class G2FA
{
    public static function isValidCode($secret, $code) : bool
    {
        $time = intval(time() / 30);
        $time = pack('N*', 0) . pack('N*', $time);

        return (($code == self::getCode($secret, $time - 1) ||
                 $code == self::getCode($secret, $time) ||
                 $code == self::getCode($secret, $time + 1)) ? true : false);
    }

    public static function getCode($secret, $time = 0) : int
    {
        $key = self::base32_decode($secret);

        if ($time == 0)
        {
            $time = intval(time() / 30);
            $time = pack('N*', 0) . pack('N*', $time);
        }

        $hash = hash_hmac('sha1', $time, $key, true);

        $offset = ord(substr($hash, -1)) & 0x0F;

        $subhash = substr($hash, $offset, 4);

        $val = ((ord($hash[$offset]) & 0x7F) << 24) |
                (ord($hash[$offset+1]) << 16) |
                (ord($hash[$offset+2]) << 8) |
                 ord($hash[$offset+3]);

        $val %= 1000000;

        return $val;
    }





    private static $decode = array(
        'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,  'E' => 4,  'F' => 5,  'G' => 6,
        'H' => 7,  'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13,
        'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20,
        'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27,
        '4' => 28, '5' => 29, '6' => 30, '7' => 31, '=' => 32
    );

    private static function base32_decode($code)
    {
        if (strlen($code) == 0)
            return '';

        $code = strtoupper($code);

        $code = preg_replace('/[^A-Z2-7=]/', '', $code);


        $res = '';
        $len = strlen($code);

        $bits = 0;
        $outch = 0;

        for ($i = 0; $i < $len; ++$i)
        {
            if ($code[$i] == '=')
                break;

            // put the bits in our character
            $outch <<= 5;
            $outch |= self::$decode[$code[$i]];
            $bits += 5;

            if ($bits >= 8)
            {
                // a byte is ready
                $bits -= 8;

                // trim off left 8 bits
                $left = ($outch >> $bits) & 0xFF;

                // get remaining bits
                $outch &= (0xFF >> (8 - $bits));

                $res .= chr($left);
            }
        }

        return $res;
    }
}
