<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-17
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Action
{
    public static function trackTest(string $user_eid, array $params)
    {
        // note: for testing use; does not contain production check
        self::track_internal($user_eid, $params);
    }

    public static function track(string $user_eid, array $params)
    {
        // note: for actual use in Api functions; contains production check
        if (!IS_PRODSITE())
            return;

        self::track_internal($user_eid, $params);
    }

    private static function track_internal(string $user_eid, array $params)
    {
        /*
        POST https://api.segment.io/v1/identify

        {
        "userId": "019mr8mf4r",
        "traits": {
            "email": "pgibbons@initech.com",
            "name": "Peter Gibbons",
            "industry": "Technology"
        },
        "context": {
            "ip": "24.5.68.47"
        },
        "timestamp": "2012-12-02T00:30:08.276Z"
        }
        */

        /*
        POST https://api.segment.io/v1/track

        {
        "userId": "019mr8mf4r",
        "event": "Item Purchased",
        "properties": {
            "name": "Leap to Conclusions Mat",
            "revenue": 14.99
        },
        "context": {
            "ip": "24.5.68.47"
        },
        "timestamp": "2012-12-02T00:30:12.984Z"
        }
        */
    }
}
