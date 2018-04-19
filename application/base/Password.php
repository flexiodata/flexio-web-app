<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-13
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Password
{
    public static function generate() : string
    {
        $pw = \Flexio\Base\Util::generateRandomString(10);
        $pw[2] = ''.random_int(0, 9);
        $pw[6] = ''.random_int(0, 9);
        $i = random_int(0, 9);
        if ($i == 2 || $i == 6)
            $i--;
        $pw[$i] = strtoupper($pw[$i]);

        return $pw;
    }

    public static function isValid($password) : bool
    {
        // make sure a password is a string that's a minimum
        // of 8 characters
        if (!is_string($password))
            return false;

        if (strlen($password) < 8)
            return false;

        // make sure we have at least one number
        $regex = "/[0-9]/";
        if (preg_match($regex, $password) === 0)
            return false;

        return true;
    }
}
