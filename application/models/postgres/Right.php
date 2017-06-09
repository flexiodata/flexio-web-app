<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-30
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Right extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_RIGHT, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'object_eid'    => $params['object_eid'] ?? '',
                'access_type'   => $params['access_type'] ?? '',
                'access_code'   => $params['access_code'] ?? '',
                'actions'       => $params['actions'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_acl', $process_arr) === false)
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
        if (($process_arr = $validator->check($params, array(
                'object_eid' => array('type' => 'string',  'required' => false),
                'access_type' => array('type' => 'string',  'required' => false),
                'access_code' => array('type' => 'string',  'required' => false),
                'actions' => array('type' => 'string',  'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

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
            $db->update('tbl_acl', $process_arr, 'eid = ' . $db->quote($eid));
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
                                         tac.object_eid as object_eid,
                                         tac.access_type as access_type,
                                         tac.access_code as access_code,
                                         tac.actions as actions,
                                         tob.eid_status as eid_status,
                                         tob.created as created,
                                         tob.updated as updated
                                from tbl_object tob
                                inner join tbl_acl tac on tob.eid = tac.eid
                                where tob.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => $row['eid_type'],
                     'object_eid'  => $row['object_eid'],
                     'access_type' => $row['access_type'],
                     'access_code' => $row['access_code'],
                     'actions'     => $row['actions'],
                     'eid_status'  => $row['eid_status'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getInfoFromAccessCode(string $code) : array
    {
        // get the rights information from the access code
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tac.object_eid as object_eid,
                                     tac.access_type as access_type,
                                     tac.access_code as access_code,
                                     tac.actions as actions,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_acl tac on tob.eid = tac.eid
                              where tac.access_code = ?
                             ", $code);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'actions'     => $row['actions'],
                              'eid_status'  => $row['eid_status'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function getInfoFromObjectEid(string $object_eid) : array
    {
        // get the all available authentication information for the object_eid
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tob.eid as eid,
                                      tob.eid_type as eid_type,
                                      tac.object_eid as object_eid,
                                      tac.access_type as access_type,
                                      tac.access_code as access_code,
                                      tac.actions as actions,
                                      tob.eid_status as eid_status,
                                      tob.created as created,
                                      tob.updated as updated
                              from tbl_object tob
                              inner join tbl_acl tac on tob.eid = tac.eid
                              where tac.object_eid = ?
                              order by tob.created
                             ", $object_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'actions'     => $row['actions'],
                              'eid_status'  => $row['eid_status'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
