<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-19
 *
 * @package flexio
 * @subpackage Api
 */


namespace Flexio\Api;


class Search
{
    public static function search($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'owner'    => array('type' => 'string', 'required' => false),
                'name'     => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $requesting_user_eid = $request->getRequestingUser();


        // TODO: right now; pipes aren't public; so if we're looking for pipes other
        // than those owned by the requesting user, return an empty array

        $search_path = "$requesting_user_eid->(".\Model::EDGE_OWNS.",".\Model::EDGE_FOLLOWING.")->("
                                                .\Model::TYPE_PROJECT.")->(".\Model::EDGE_HAS_MEMBER.")->("
                                                .\Model::TYPE_PIPE.")";
        $pipes = \Flexio\Object\Search::exec($search_path);

        $res = array();
        foreach ($pipes as $p)
        {
            $pipe_eid = $p;

            // load the pipe
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($pipe === false)
                continue;

            if ($pipe->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            // check the rights on the pipe
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
                continue;

            $pipe_properties = $pipe->get();

            if (isset($params['name']))
            {
                if ($pipe_properties['name'] != $params['name'])
                    continue;
            }

            if (isset($params['owner']))
            {
                if ($pipe->getOwner() != $params['owner'])
                    continue;
            }

            $res[] = $pipe_properties;
        }

        return $res;
    }
}
