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
    public static function create(string $action, string $user_eid, string $subject_eid, string $object_eid = null, array $params = null) : bool
    {
        $action_params = array(
            'action' => $action,
            'user_eid' => $user_eid,
            'subject_eid' => $subject_eid,
            'object_eid' => $object_eid,
            'params' => json_encode($params)
        );
        $result = \Flexio\System\System::getModel()->action->record($action_params);
        return $result;
    }

    public static function identifyTest(array $params)
    {
        // note: for testing use; does not contain production check
        self::identify_internal($params);
    }

    public static function trackTest(array $params)
    {
        // note: for testing use; does not contain production check
        self::track_internal($params);
    }

    public static function identify(array $params)
    {
        if (!IS_PRODSITE())
            return;

        self::identify_internal($params);
    }

    public static function track(array $params)
    {
        if (!IS_PRODSITE())
            return;

        self::track_internal($params);
    }

    private static function identify_internal(array $params)
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
    }

    private static function track_internal(array $params)
    {
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
