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
namespace Flexio\Api2;


class Statistics
{
    public static function getUserProcessStats(\Flexio\Api2\Request $request) : array
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // only return stats for the user that's making the request
        // make sure we have a valid user; otherwise, it's a public request, so don't allow it

        if ($requesting_user_eid === \Flexio\Object\User::MEMBER_PUBLIC)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        if ($owner_user_eid !== $requesting_user_eid)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $stats = \Flexio\System\System::getModel()->process->summary($owner_user_eid);

        $result = array();
        foreach ($stats as $s)
        {
            $pipe = array(
                'eid' => $s['pipe_eid'],
                'eid_type' => strlen($s['pipe_eid']) > 0 ? \Model::TYPE_PIPE : ''
            );

            $item['pipe'] = $pipe;
            $item['process_created'] = $s['created'];
            $item['total_count'] = $s['total_count'];
            $item['total_time'] = $s['total_time'];
            $item['average_time'] = $s['average_time'];

            $result[] = $item;
        }

        return $result;
    }
}
