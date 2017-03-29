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


class Action extends ModelBase
{
    public function record(array $params) : bool
    {
        // TODO: this function is for recording actions; if it fails, no
        // need to fail the whole action; simply return false

        $db = $this->getDatabase();
        try
        {
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'user_eid'       => $params['user_eid'] ?? '',
                'request_method' => $params['request_method'] ?? '',
                'url_params'     => $params['url_params'] ?? '',
                'query_params'   => $params['query_params'] ?? '',
                'created'        => $timestamp,
                'updated'        => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_action', $process_arr) === false)
                throw new \Exception();

            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }
}
