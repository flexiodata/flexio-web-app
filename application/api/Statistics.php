<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-13
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Statistics
{
    public static function getUserProcessStats(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // TODO: following limits access; do we need to be more explicit?
        // only return stats for the user that's making the request
        // make sure we have a valid user; otherwise, it's a public request, so don't allow it

        if ($requesting_user_eid === \Flexio\Object\User::MEMBER_PUBLIC)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $stats = \Flexio\System\System::getModel()->process->getUserProcessStats($requesting_user_eid);

        $result = array();
        foreach ($stats as $s)
        {
            $pipe = \Flexio\Object\Pipe::load($s['pipe_eid']);
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                continue;

            $pipe_info = $pipe->get();

            $item = array();

            $item['pipe'] = array();
            $item['pipe']['eid'] = $pipe_info['eid'] ?? '';
            $item['pipe']['eid_type'] = $pipe_info['eid_type'] ?? '';
            $item['pipe']['name'] = $pipe_info['name'] ?? 'Anonymous';
            $item['pipe']['description'] = $pipe_info['description'] ?? 'Anonymous Process';

            $item['process_created'] = $s['created'];
            $item['total_count'] = $s['total_count'];
            $item['total_time'] = $s['total_time'];
            $item['average_time'] = $s['average_time'];

            $result[] = $item;
        }

        return $result;
    }
}
