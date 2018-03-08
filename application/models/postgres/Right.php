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
                'eid_status'    => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'  => array('type' => 'string', 'required' => false),
                'object_eid'  => array('type' => 'string', 'required' => false),
                'access_type' => array('type' => 'string', 'required' => false),
                'access_code' => array('type' => 'string', 'required' => false),
                'actions'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // if an item is deleted, don't allow it to be edited
            $existing_status = $this->getStatus($eid);
            if ($existing_status === false || $existing_status == \Model::STATUS_DELETED)
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
                                         tac.eid_status as eid_status,
                                         tac.object_eid as object_eid,
                                         tac.access_type as access_type,
                                         tac.access_code as access_code,
                                         tac.actions as actions,
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
                     'eid_status'  => $row['eid_status'],
                     'object_eid'  => $row['object_eid'],
                     'access_type' => $row['access_type'],
                     'access_code' => $row['access_code'],
                     'actions'     => $row['actions'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
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
            $db->update('tbl_acl', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return true; // established object exists, which is enough for returning true
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_acl where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function getInfoFromObjectEid(string $object_eid) : array
    {
        // get the all available authentication information for the object_eid
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tob.eid as eid,
                                      tob.eid_type as eid_type,
                                      tac.eid_status as eid_status,
                                      tac.object_eid as object_eid,
                                      tac.access_type as access_type,
                                      tac.access_code as access_code,
                                      tac.actions as actions,
                                      tob.created as created,
                                      tob.updated as updated
                              from tbl_object tob
                              inner join tbl_acl tac on tob.eid = tac.eid
                              where tac.object_eid = ?
                             ", $object_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'eid_status'  => $row['eid_status'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'actions'     => $row['actions'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
