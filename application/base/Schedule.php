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

    public static function getNextScheduledTime(string $start_time, $schedule) : string
    {
        // returns the next scheduled time using the start time as a starting point

        if (self::isValid($schedule) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // TODO: for now, only frequency-based offsets from the start time are
        // supported add support for explicit times
        if (count($schedule['days']) > 0 || count($schedule['times']) > 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // TODO: for now, only UTC is supported
        if (strlen($schedule['timezone']) > 0 && $schedule['timezone'] !== 'UTC')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $next_scheduled_time = false;
        $frequency = $schedule['frequency'];

        switch ($frequency)
        {
            // cases currently not handled
            default:
            case self::FREQ_MONTHLY:
                // for now, fall through
                break;

            case self::FREQ_ONE_MINUTE:
                $next_scheduled_time = strtotime($start_time . ' + 1 minute');
                break;

            case self::FREQ_FIVE_MINUTES:
                $next_scheduled_time = strtotime($start_time . ' + 5 minutes');
                break;

            case self::FREQ_FIFTEEN_MINUTES:
                $next_scheduled_time = strtotime($start_time . ' + 15 minutes');
                break;

            case self::FREQ_THIRTY_MINUTES:
                $next_scheduled_time = strtotime($start_time . ' + 30 minutes');
                break;

            case self::FREQ_HOURLY:
                $next_scheduled_time = strtotime($start_time . ' + 1 hour');
                break;

            case self::FREQ_DAILY:
                $next_scheduled_time = strtotime($start_time . ' + 1 day');
                break;

            case self::FREQ_WEEKLY:
                $next_scheduled_time = strtotime($start_time . ' + 7 day');
                break;
        }

        // unable to determine next time; issue with start time formatting
        if ($next_scheduled_time === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $next_scheduled_time = date('Y-m-d H:i:s', $next_scheduled_time);
        return $next_scheduled_time;
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
