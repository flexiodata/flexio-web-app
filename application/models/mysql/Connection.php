<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2014-07-09
 *
 * @package flexio
 * @subpackage Model
 */


class Connection extends ModelBase
{
    public function create($params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        // if the connection_status parameter is set, make sure the status is set
        // to a valid value
        if (isset($params['connection_status']))
        {
            $status = $params['connection_status'];
            if ($status != \Model::CONNECTION_STATUS_INVALID && $status != \Model::CONNECTION_STATUS_UNAVAILABLE &&
                $status != \Model::CONNECTION_STATUS_AVAILABLE && $status != \Model::CONNECTION_STATUS_ERROR)
            {
                $params['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;  // default status
            }
        }

        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_CONNECTION, $params);
            if ($eid === false)
                throw new \Exception();

            $default_database = '';
            if ($default_database == '%eid%')
                $default_database = $eid;

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'               => $eid,
                'name'              => isset_or($params['name'], ''),
                'description'       => isset_or($params['description'], ''),
                'display_icon'      => isset_or($params['display_icon'], ''),
                'host'              => isset_or($params['host'], ''),
                'port'              => isset_or($params['port'], 0),
                'username'          => isset_or($params['username'], ''),
                'password'          => isset_or($params['password'], ''),
                'token'             => isset_or($params['token'], ''),
                'refresh_token'     => isset_or($params['refresh_token'], ''),
                'token_expires'     => isset_or($params['token_expires'], ''),
                'database'          => isset_or($params['database'], $default_database),
                'connection_type'   => isset_or($params['connection_type'], ''),
                'connection_status' => isset_or($params['connection_status'], \Model::CONNECTION_STATUS_UNAVAILABLE),
                'created'           => $timestamp,
                'updated'           => $timestamp
            );

            $process_arr['username'] = \Flexio\Base\Util::encrypt($process_arr['username'], $GLOBALS['g_store']->connection_enckey);
            $process_arr['password'] = \Flexio\Base\Util::encrypt($process_arr['password'], $GLOBALS['g_store']->connection_enckey);
            $process_arr['token'] = \Flexio\Base\Util::encrypt($process_arr['token'], $GLOBALS['g_store']->connection_enckey);
            $process_arr['refresh_token'] = \Flexio\Base\Util::encrypt($process_arr['refresh_token'], $GLOBALS['g_store']->connection_enckey);

            // add the properties
            if ($db->insert('tbl_connection', $process_arr) === false)
                throw new \Exception();

            $db->commit();
            return $eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(\Flexio\Base\Error::CREATE_FAILED, _('Could not create connection'));
        }
    }

    public function delete($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // delete the object
            $result = $this->getModel()->deleteObjectBase($eid);
            $db->commit();
            return $result;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(\Flexio\Base\Error::DELETE_FAILED, _('Could not delete connection'));
        }
    }

    public function set($eid, $params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'display_icon'      => array('type' => 'string',  'required' => false),
                'host'              => array('type' => 'string',  'required' => false),
                'port'              => array('type' => 'integer', 'required' => false),
                'username'          => array('type' => 'string',  'required' => false),
                'password'          => array('type' => 'string',  'required' => false),
                'token'             => array('type' => 'string',  'required' => false),
                'refresh_token'     => array('type' => 'string',  'required' => false),
                'token_expires'     => array('type' => 'string',  'required' => false),
                'database'          => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false)
            ))) === false)
            return $this->fail(\Flexio\Base\Error::WRITE_FAILED, _('Could not update connection'));
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['username'])) $process_arr['username'] = \Flexio\Base\Util::encrypt($process_arr['username'], $GLOBALS['g_store']->connection_enckey);
        if (isset($process_arr['password'])) $process_arr['password'] = \Flexio\Base\Util::encrypt($process_arr['password'], $GLOBALS['g_store']->connection_enckey);
        if (isset($process_arr['token'])) $process_arr['token'] = \Flexio\Base\Util::encrypt($process_arr['token'], $GLOBALS['g_store']->connection_enckey);
        if (isset($process_arr['refresh_token'])) $process_arr['refresh_token'] = \Flexio\Base\Util::encrypt($process_arr['refresh_token'], $GLOBALS['g_store']->connection_enckey);

        // if the connection_status parameter is set, make sure the status is set
        // to a valid value
        if (isset($process_arr['connection_status']))
        {
            $status = $process_arr['connection_status'];
            if ($status != \Model::CONNECTION_STATUS_INVALID && $status != \Model::CONNECTION_STATUS_UNAVAILABLE &&
                $status != \Model::CONNECTION_STATUS_AVAILABLE && $status != \Model::CONNECTION_STATUS_ERROR)
            {
                $process_arr['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;  // default status
            }
        }

        $db->beginTransaction();
        try
        {
            // set the base object properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            if ($result === false)
            {
                // simply return false; no exception
                $db->commit();
                return false;
            }

            // set the properties
            $db->update('tbl_connection', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(\Flexio\Base\Error::WRITE_FAILED, _('Could not update connection'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tob.ename as ename,
                                     tco.name as name,
                                     tco.description as description,
                                     tco.display_icon as display_icon,
                                     tco.host as host,
                                     tco.port as port,
                                     tco.username as username,
                                     tco.password as password,
                                     tco.token as token,
                                     tco.refresh_token as refresh_token,
                                     tco.token_expires as token_expires,
                                     tco.database as database,
                                     tco.connection_type as connection_type,
                                     tco.connection_status as connection_status,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_connection tco on tob.eid = tco.eid
                              where tob.eid = ?
                             ", $eid);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row['username'] = \Flexio\Base\Util::decrypt($row['username'], $GLOBALS['g_store']->connection_enckey);
        $row['password'] = \Flexio\Base\Util::decrypt($row['password'], $GLOBALS['g_store']->connection_enckey);
        $row['token'] = \Flexio\Base\Util::decrypt($row['token'], $GLOBALS['g_store']->connection_enckey);
        $row['refresh_token'] = \Flexio\Base\Util::decrypt($row['refresh_token'], $GLOBALS['g_store']->connection_enckey);


        return array('eid'               => $row['eid'],
                     'eid_type'          => $row['eid_type'],
                     'ename'             => $row['ename'],
                     'name'              => $row['name'],
                     'description'       => $row['description'],
                     'display_icon'      => $row['display_icon'],
                     'host'              => $row['host'],
                     'port'              => (int)$row['port'],
                     'username'          => $row['username'],
                     'password'          => $row['password'],
                     'token'             => $row['token'],
                     'refresh_token'     => $row['refresh_token'],
                     'token_expires'     => $row['token_expires'],
                     'database'          => $row['database'],
                     'connection_type'   => $row['connection_type'],
                     'connection_status' => $row['connection_status'],
                     'eid_status'        => $row['eid_status'],
                     'created'           => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'           => \Flexio\Base\Util::formatDate($row['updated']));
    }
}
