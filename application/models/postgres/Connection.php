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
                'eid_status'        => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'name'              => $params['name'] ?? '',
                'description'       => $params['description'] ?? '',
                'connection_type'   => $params['connection_type'] ?? '',
                'connection_status' => $params['connection_status'] ?? \Model::CONNECTION_STATUS_UNAVAILABLE,
                'connection_info'   => $params['connection_info'] ?? '',
                'expires'           => $params['expires'] ?? null,
                'owned_by'          => $params['owned_by'] ?? '',
                'created_by'        => $params['created_by'] ?? '',
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string',  'required' => false),
                'name'              => array('type' => 'string',  'required' => false),
                'description'       => array('type' => 'string',  'required' => false),
                'connection_type'   => array('type' => 'string',  'required' => false),
                'connection_status' => array('type' => 'string',  'required' => false),
                'connection_info'   => array('type' => 'string',  'required' => false),
                'expires'           => array('type' => 'any',     'required' => false),    // TODO: workaround null problem; any = allow nulls
                'owned_by'          => array('type' => 'string',  'required' => false),
                'created_by'        => array('type' => 'string',  'required' => false)
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
            // if the item doesn't exist, return false; TODO: throw exception instead?
            $existing_status = $this->getStatus($eid);
            if ($existing_status === \Model::STATUS_UNDEFINED)
            {
                $db->commit();
                return false;
            }

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, (IS_DEBUG() ? $e->getMessage() : null));
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
            $row = $db->fetchRow("select tco.eid as eid,
                                         '".\Model::TYPE_CONNECTION."' as eid_type,
                                         tco.eid_status as eid_status,
                                         tob.ename as ename,
                                         tco.name as name,
                                         tco.description as description,
                                         tco.connection_type as connection_type,
                                         tco.connection_status as connection_status,
                                         tco.connection_info as connection_info,
                                         tco.expires as expires,
                                         tco.owned_by as owned_by,
                                         tco.created_by as created_by,
                                         tco.created as created,
                                         tco.updated as updated
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
                     'eid_status'        => $row['eid_status'],
                     'ename'             => $row['ename'],
                     'name'              => $row['name'],
                     'description'       => $row['description'],
                     'connection_type'   => $row['connection_type'],
                     'connection_status' => $row['connection_status'],
                     'connection_info'   => $row['connection_info'],
                     'expires'           => $row['expires'],
                     'owned_by'          => $row['owned_by'],
                     'created_by'        => $row['created_by'],
                     'created'           => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'           => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function setStatus(string $eid, string $status) : bool
    {
        // note: it's possible to set the status through the \Model::set()
        // function on the model, but this provides a lightweight alternative
        // that isn't restricted (right now, changes through \Model::set() are
        // only applied for items that aren't deleted)

        // make sure the status is set to a valid value
        if (!\Model::isValidStatus($status))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // basic check to avoid needless work
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            // set the updated timestamp so it'll stay in sync with whatever
            // object is being edited
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid_status'    => $status,
                'updated'       => $timestamp
            );
            $db->update('tbl_connection', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return true; // established object exists, which is enough for returning true
    }

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_connection where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_connection where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }
}
