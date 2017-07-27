<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-21
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Action extends ModelBase
{
    public function record(array $params) : bool
    {
        $db = $this->getDatabase();
        try
        {
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'user_eid' => $params['user_eid'],
                'subject_eid' => $params['subject_eid'],
                'object_eid' => $params['object_eid'],
                'action' => $params['action'],
                'params' => json_encode($params),
                'created' => $timestamp,
                'updated' => $timestamp,
            );

            // add the properties
            if ($db->insert('tbl_action', $process_arr) === false)
                throw new \Exception();

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}
