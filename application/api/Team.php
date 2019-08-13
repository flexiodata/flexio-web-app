<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-18
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Team
{
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

        // check the rights on the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_TEAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the teams (items where the owner is a member of a team)
        $result = array();

        $filter = array('member_eid' => $owner_user_eid, 'member_status' => \Model::TEAM_MEMBER_STATUS_ACTIVE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed owner/status
        $teams = \Flexio\System\System::getModel()->team->list($filter);

        foreach ($teams as $t)
        {
            // TODO: check permissions for each item?
            $result[] = self::formatProperties($t);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    private static function formatProperties(array $properties) : array
    {
        // sanity check: if the data record is missing, then owned_by (eid is
        // normally used) will be null
        if (!isset($properties['owned_by']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // get the team info
        $team_info = array();
        $team_info['eid'] = $properties['owned_by'];
        $team_info['eid_type'] = \Model::TYPE_USER;

        try
        {
            $user = \Flexio\Object\User::load($properties['owned_by']);
            $user_info = $user->get();
            $team_info['eid_status'] = $user_info['eid_status'];
            $team_info['username'] = $user_info['username'];
            $team_info['first_name'] = $user_info['first_name'];
            $team_info['last_name'] = $user_info['last_name'];
            $team_info['email'] = $user_info['email'];
            $team_info['email_hash'] = $user_info['email_hash'];
            $team_info['created'] = $user_info['created'];
            $team_info['updated'] = $user_info['updated'];
        }
        catch (\Exception $e)
        {
        }

        // return the info
        return $team_info;
    }
}
