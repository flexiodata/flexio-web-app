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

        // maek sure we have an 'action' query parameter
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'action'        => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        \Flexio\Object\Action::trackTest($requesting_user_eid, $params);
        return array();
    }
}
