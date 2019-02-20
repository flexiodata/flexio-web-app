<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-03-16
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Eid
{
    public static function generate() : string
    {
        // note: valid eids are 12 character alphanumeric strings
        // without vowels; if this definition changes, make sure
        // to update \Flexio\Base\Eid::isValid()

        list($usec, $sec) = explode(' ', microtime());
        $full = sprintf("%06d",random_int(0,999999)) . $sec . substr($usec, 2, 6);

        $value = sha1($full);

        /*
        // note: produces 8 digit eid
        // take 12 digits and convert it to binary
        $value = base_convert(substr($value,0,12), 16, 2);

        // take 39 bits and convert it to base 31
        $value = base_convert(substr($value,0,39), 2, 31);

        // translate letters to take out vowels (prevents random nasty words)
        $value = strtr($value, "abcdefghijklmnopqrstu", "bcdfghjklmnpqrstvwxyz");

        return str_pad($value, 8, '0', STR_PAD_LEFT);
        */

        // take 16 digits and convert it to binary
        $value = base_convert(substr($value,0,16), 16, 2);

        // take 59 bits and convert it to base 31
        // (59 bits will yield a maximum of a 12 digit string in base 31)
        $value = base_convert(substr($value,0,59), 2, 31);

        // translate letters to take out vowels (prevents random nasty words)
        $value = strtr($value, 'abcdefghijklmnopqrstu', 'bcdfghjklmnpqrstvwxyz');

        // make sure the string is 12 digits
        $value = str_pad($value, 12, '0', STR_PAD_LEFT);

        // make sure the first digit is not a number; this guarantees that
        // the eid can be used for identifiers in database and other systems
        if ($value[0] >= '0' && $value[0] <= '9')
        {
            $str = 'bcdfghjklmnpqrstvwxyz';
            $value[0] = $str[$value[0]];
        }

        return $value;
    }

    public static function isValid($eid) : bool
    {
        // eids are 12 digit character strings
        $eid_length = 12;

        // quick eid length check
        if (!is_string($eid) || strlen($eid) != $eid_length)
            return false;

        // note: valid eids are 12 character alphanumeric strings
        // without vowels; this definition is based on the logic
        // in \Flexio\Base\Eid::generate(); if the logic changes in this
        // generation function, it will need to be changed here
        // as well
        if (!preg_match('/^[bcdfghjklmnpqrstvwxyz0123456789]{'.$eid_length.'}$/', $eid))
            return false;

        return true;
    }
}
