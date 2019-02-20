<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-29
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Schedule
{
/*
// DESCRIPTION:
{
    "frequency": "",
    "timezone": "",
    "days": [],
    "times": [
        {
            "hour": 0,
            "minute": 0
        }
    ]
}
*/
    public const FREQ_ONE_MINUTE      = 'one-minute';
    public const FREQ_FIVE_MINUTES    = 'five-minutes';
    public const FREQ_FIFTEEN_MINUTES = 'fifteen-minutes';
    public const FREQ_THIRTY_MINUTES  = 'thirty-minutes';
    public const FREQ_HOURLY          = 'hourly';
    public const FREQ_DAILY           = 'daily';
    public const FREQ_WEEKLY          = 'weekly';
    public const FREQ_MONTHLY         = 'monthly';

    public const DAY_MONDAY    = 'mon';
    public const DAY_TUESDAY   = 'tue';
    public const DAY_WEDNESDAY = 'wed';
    public const DAY_THURSDAY  = 'thu';
    public const DAY_FRIDAY    = 'fri';
    public const DAY_SATURDAY  = 'sat';
    public const DAY_SUNDAY    = 'sun';

    public const MONTH_FIRST     = 1;
    public const MONTH_FIFTEENTH = 15;
    public const MONTH_LAST      = 'last';

    public static function isValid($properties) : bool
    {
        if (!is_array($properties))
            return false;

        if (\Flexio\Base\ValidatorSchema::check($properties, self::SCHEDULE_SCHEMA)->hasErrors())
            return false;

        // make sure timezone is a valid
        $timezone = $properties['timezone'] ;
        if (!self::isValidTimeZone($timezone))
            return false;

        return true;
    }

    public static function isValidTimeZone($timezone) : bool
    {
        if (!is_string($timezone))
            return false;

        // allow empty timezone for purposes of default
        if (strlen($timezone) === 0)
            return true;

        try
        {
            $timezone = new \DateTimeZone($timezone);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    private const SCHEDULE_SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["frequency","timezone","days","times"],
        "properties": {
            "frequency": {
                "type": "string",
                "enum": ["", "one-minute","five-minutes","fifteen-minutes","thirty-minutes","hourly","daily","weekly","monthly"]
            },
            "timezone": {
                "type": "string"
            },
            "days": {
                "type": "array",
                "items": {
                    "type": ["number","string"],
                    "enum": ["mon","tue","wed","thu","fri","sat","sun","last",1,15]
                }
            },
            "times": {
                "type": "array",
                "items": {
                    "type": "object",
                    "required": ["hour","minute"],
                    "properties": {
                        "hour": {
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 23
                        },
                        "minute": {
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 59
                        }
                    }
                }
            }
        }
    }
EOD;
}
