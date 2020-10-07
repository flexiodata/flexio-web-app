<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
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
    public const TYPE_UNDEFINED              = '';
    public const TYPE_TEST                   = 'action.test';
    public const TYPE_GENERAL                = 'action.general';

    public const TYPE_USER_LOGIN             = 'action.user.login';
    public const TYPE_USER_LOGOUT            = 'action.user.logout';

    public const TYPE_USER_CREATE            = 'action.user.create';
    public const TYPE_USER_UPDATE            = 'action.user.update';
    public const TYPE_USER_DELETE            = 'action.user.delete';
    public const TYPE_USER_READ              = 'action.user.read';
    public const TYPE_USER_CREDENTIAL_UPDATE = 'action.user.credential.update';
    public const TYPE_USER_CREDENTIAL_RESET  = 'action.user.credential.reset';

    public const TYPE_USER_AUTHKEY_CREATE    = 'action.user.authkey.create';
    public const TYPE_USER_AUTHKEY_DELETE    = 'action.user.authkey.delete';
    public const TYPE_USER_AUTHKEY_READ      = 'action.user.authkey.read';

    public const TYPE_TEAM_CREATE            = 'action.team.create';
    public const TYPE_TEAM_UPDATE            = 'action.team.update';
    public const TYPE_TEAM_DELETE            = 'action.team.delete';
    public const TYPE_TEAM_READ              = 'action.team.read';

    public const TYPE_TEAMMEMBER_CREATE         = 'action.teammember.create';
    public const TYPE_TEAMMEMBER_UPDATE         = 'action.teammember.update';
    public const TYPE_TEAMMEMBER_DELETE         = 'action.teammember.delete';
    public const TYPE_TEAMMEMBER_READ           = 'action.teammember.read';
    public const TYPE_TEAMMEMBER_SENDINVITATION = 'action.teammember.sendinvitation';
    public const TYPE_TEAMMEMBER_JOINTEAM       = 'action.teammember.jointeam';
    public const TYPE_TEAMMEMBER_LEAVETEAM      = 'action.teammember.leaveteam';

    public const TYPE_PIPE_CREATE            = 'action.pipe.create';
    public const TYPE_PIPE_UPDATE            = 'action.pipe.update';
    public const TYPE_PIPE_DELETE            = 'action.pipe.delete';
    public const TYPE_PIPE_READ              = 'action.pipe.read';

    public const TYPE_CONNECTION_CREATE      = 'action.connection.create';
    public const TYPE_CONNECTION_UPDATE      = 'action.connection.update';
    public const TYPE_CONNECTION_DELETE      = 'action.connection.delete';
    public const TYPE_CONNECTION_READ        = 'action.connection.read';
    public const TYPE_CONNECTION_CONNECT     = 'action.connection.connect';
    public const TYPE_CONNECTION_DISCONNECT  = 'action.connection.disconnect';
    public const TYPE_CONNECTION_SYNC        = 'action.connection.sync';

    public const TYPE_PROCESS_CREATE         = 'action.process.create';
    public const TYPE_PROCESS_UPDATE         = 'action.process.update';
    public const TYPE_PROCESS_DELETE         = 'action.process.delete';
    public const TYPE_PROCESS_READ           = 'action.process.read';

    public const TYPE_STREAM_CREATE          = 'action.stream.create';
    public const TYPE_STREAM_UPDATE          = 'action.stream.update';
    public const TYPE_STREAM_DELETE          = 'action.stream.delete';
    public const TYPE_STREAM_READ            = 'action.stream.read';

    public const TYPE_SYSTEM_READ            = 'action.system.read';

    // user interface actions
    public const TYPE_UI_SEARCH              = 'action.ui.search';
    public const TYPE_UI_BUTTONCLICK         = 'action.ui.buttonclick';


    public static function test(\Flexio\Api\Request $request) : void
    {
        $request->track(\Flexio\Api\Action::TYPE_TEST);
        sleep(1);
        $action = $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp())->track();

        $result = $action->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function create(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'action_type'        => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // extract out the action type and save the original post params without
        // the action type; note: actions types should be the ones in the ones
        // specified at the top of this file; however, to allow new action types
        // to be easily added in the UI without requiring a system update, pass
        // through anything action_type that's passed without any other validation
        // other than requiring it to be a string
        $action_type = $validated_post_params['action_type'] ?? \Flexio\Api\Action::TYPE_GENERAL;
        $action_params = $post_params;
        unset($action_params['action_type']);

        $request->track($action_type);
        $request->setRequestParams($action_params);

        // track the request
        //$request->setResponseParams($result);
        $action = $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();

        // echo back the post params
        $result = $post_params;
        \Flexio\Api\Response::sendContent($result);
    }

    public static function list(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

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
        \Flexio\Api\Response::sendContent($result);
    }
}
