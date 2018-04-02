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
    const TYPE_UNDEFINED              = '';
    const TYPE_TEST                   = 'action.test';

    const TYPE_USER_LOGIN             = 'action.user.login';
    const TYPE_USER_LOGOUT            = 'action.user.logout';

    const TYPE_USER_CREATE            = 'action.user.create';
    const TYPE_USER_UPDATE            = 'action.user.update';
    const TYPE_USER_DELETE            = 'action.user.delete';
    const TYPE_USER_CREDENTIAL_UPDATE = 'action.user.credential.update';
    const TYPE_USER_CREDENTIAL_RESET  = 'action.user.credential.reset';

    const TYPE_PIPE_CREATE           = 'action.pipe.create';
    const TYPE_PIPE_UPDATE           = 'action.pipe.update';
    const TYPE_PIPE_DELETE           = 'action.pipe.delete';

    const TYPE_CONNECTION_CREATE     = 'action.connection.create';
    const TYPE_CONNECTION_UPDATE     = 'action.connection.update';
    const TYPE_CONNECTION_DELETE     = 'action.connection.delete';
    const TYPE_CONNECTION_CONNECT    = 'action.connection.connect';
    const TYPE_CONNECTION_DISCONNECT = 'action.connection.disconnect';

    // TODO: add actions for following API endpoints:

    // * creating folder/file; TODO: what about delete?
    //   const TYPE_STORE_FOLDER_CREATE   = 'action.store.folder.create';
    //   const TYPE_STORE_FILE_CREATE     = 'action.store.file.create';

    public static function test(\Flexio\Api2\Request $request)
    {
        $request->track(\Flexio\Api2\Action::TYPE_TEST);
        sleep(1);
        $action = $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp())->track();

        $result = $action->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function summary(\Flexio\Api2\Request $request)
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public static function list(\Flexio\Api2\Request $request)
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

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api2\Response::sendContent($result);
    }
}
