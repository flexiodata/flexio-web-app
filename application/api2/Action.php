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
namespace Flexio\Api2;


class Action
{
    public static function test(\Flexio\Api2\Request $request) : array
    {
        $action = $request->track('action.test');
        return $action->get();
    }

    public static function summary(\Flexio\Api2\Request $request) : array
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public static function list(\Flexio\Api2\Request $request) : array
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // TODO: add other query string params?
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'    => array('type' => 'integer', 'required' => false),
                'tail'     => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date', 'required' => false),
                'created_max' => array('type' => 'date', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the actions
        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $actions = \Flexio\Object\Action::list($filter);

        $result = array();
        foreach ($actions as $a)
        {
            $action_info = $a->get();

            $action_info_subset = array();
            $action_info_subset['eid'] = $action_info['eid'];
            $action_info_subset['eid_type'] = $action_info['eid_type'];
            $action_info_subset['action_type'] = $action_info['action_type'];
            $action_info_subset['request_ip'] = $action_info['request_ip'];
            $action_info_subset['request_type'] = $action_info['request_type'];
            $action_info_subset['request_method'] = $action_info['request_method'];
            $action_info_subset['request_route'] = $action_info['request_route'];
            $action_info_subset['request_created_by'] = $action_info['request_created_by'];
            $action_info_subset['request_created'] = $action_info['request_created'];
            $action_info_subset['request_params'] = $action_info['request_params'];
            $action_info_subset['target_eid'] = $action_info['target_eid'];
            $action_info_subset['target_eid_type'] = $action_info['target_eid_type'];
            $action_info_subset['target_owned_by'] = $action_info['target_owned_by'];
            $action_info_subset['response_type'] = $action_info['response_type'];
            $action_info_subset['response_code'] = $action_info['response_code'];
            $action_info_subset['response_params'] = $action_info['response_params'];
            $action_info_subset['response_created'] = $action_info['response_created'];
            $action_info_subset['request_created'] = $action_info['request_created'];
            $action_info_subset['duration'] = $action_info['duration'];

            $result[] = $action_info_subset;
        }

        return $result;
    }
}
