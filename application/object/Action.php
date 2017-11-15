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

    public static function track(array $params)
    {
        if (!IS_PRODSITE())
            return;

        self::track_internal($params);
    }

    public static function trackTest(array $params)
    {
        // note: for testing use; does not contain production check
        self::track_internal($params);
    }

    private static function track_internal(array $params)
    {
        // TODO: implement
    }
}
