<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-22
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Action
{
    public static function trackTest(\Flexio\Api\Request $request)
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        \Flexio\Object\Action::trackTest($requesting_user_eid, $params);
        return array();
    }
}
