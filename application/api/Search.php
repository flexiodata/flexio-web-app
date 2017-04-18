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


declare(strict_types=1);
namespace Flexio\Api;


class Search
{
    public static function search(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'owner'    => array('type' => 'string', 'required' => false),
                'name'     => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (!isset($requesting_user_eid))
            return array();

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
            if ($pipe->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
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
