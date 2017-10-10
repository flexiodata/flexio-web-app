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


declare(strict_types=1);


class Connection extends ModelBase
{
    public function create(array $params = null) : string
    {
        // if the connection_status parameter is set, make sure the status is set
        // to a valid value
        if (isset($params['connection_status']))
        {
            $status = $params['connection_status'];
            switch ($status)
            {
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

                case \Model::CONNECTION_STATUS_INVALID:
                case \Model::CONNECTION_STATUS_UNAVAILABLE:
                case \Model::CONNECTION_STATUS_AVAILABLE:
                case \Model::CONNECTION_STATUS_ERROR:
                    break;
            }
        }

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_CONNECTION, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'               => $eid,
                'name'              => $params['name'] ?? '',
                'description'       => $params['description'] ?? '',
                'display_icon'      => $params['display_icon'] ?? '',
                'connection_type'   => $params['connection_type'] ?? '',
                'connection_status' => $params['connection_status'] ?? \Model::CONNECTION_STATUS_UNAVAILABLE,
                'connection_info'   => $params['connection_info'] ?? '',
                'expires'           => $params['expires'] ?? '',
                'created'           => $timestamp,
                'updated'           => $timestamp
            );

            // encrypt the connection info
            $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);

            // add the properties
            if ($db->insert('tbl_connection', $process_arr) === false)
                throw new \Exception();

            $db->commit();
            return $eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function delete(string $eid) : bool
    {
        $db = $this->getDatabase();
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'display_icon'      => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'   => array('type' => 'string',  'required' => false),
                'expires'           => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        // encrypt the connection info
        if (isset($process_arr['connection_info']))
            $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);

        // if the connection_status parameter is set, make sure the status is set
        // to a valid value
        if (isset($params['connection_status']))
        {
            $status = $process_arr['connection_status'];
            switch ($status)
            {
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

                case \Model::CONNECTION_STATUS_INVALID:
                case \Model::CONNECTION_STATUS_UNAVAILABLE:
                case \Model::CONNECTION_STATUS_AVAILABLE:
                case \Model::CONNECTION_STATUS_ERROR:
                    break;
            }
        }

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // set the base object properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            if ($result === false)
            {
                // object doesn't exist or is deleted
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tob.eid as eid,
                                        tob.eid_type as eid_type,
                                        tob.ename as ename,
                                        tco.name as name,
                                        tco.description as description,
                                        tco.display_icon as display_icon,
                                        tco.connection_type as connection_type,
                                        tco.connection_status as connection_status,
                                        tco.connection_info as connection_info,
                                        tco.expires as expires,
                                        tob.eid_status as eid_status,
                                        tob.created as created,
                                        tob.updated as updated
                                from tbl_object tob
                                inner join tbl_connection tco on tob.eid = tco.eid
                                where tob.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row['connection_info'] = \Flexio\Base\Util::decrypt($row['connection_info'], $GLOBALS['g_store']->connection_enckey);

        return array('eid'               => $row['eid'],
                     'eid_type'          => $row['eid_type'],
                     'ename'             => $row['ename'],
                     'name'              => $row['name'],
                     'description'       => $row['description'],
                     'display_icon'      => $row['display_icon'],
                     'connection_type'   => $row['connection_type'],
                     'connection_status' => $row['connection_status'],
                     'connection_info'   => $row['connection_info'],
                     'expires'           => $row['expires'],
                     'eid_status'        => $row['eid_status'],
                     'created'           => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'           => \Flexio\Base\Util::formatDate($row['updated']));
    }
}
