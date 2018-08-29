<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
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

    public static function isValid($properties) : bool
    {
        if (\Flexio\Base\ValidatorSchema::check($properties, self::SCHEDULE_SCHEMA)->hasErrors())
            return false;

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
                            "maximum": 24
                        },
                        "minute": {
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 60
                        }
                    }
                }
            }
        }
    }
EOD;
}
