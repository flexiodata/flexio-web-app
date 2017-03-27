<?php
/**
 *
 * Copyright (c) 2011-2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams; Benjamin I. Williams
 * Created:  2013-05-20
 *
 * @package flexio
 * @subpackage Model
 */


class Comment extends ModelBase
{
    public function create($params) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_COMMENT, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'comment'       => $params['comment'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_comment', $process_arr) === false)
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

    public function delete($eid) : bool
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

    public function set($eid, $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'comment' => array('type' => 'string', 'required' => false)
            ))) === false)
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
            $db->update('tbl_comment', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function get($eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tob.eid as eid,
                                        tob.eid_type as eid_type,
                                        tco.comment as comment,
                                        tob.eid_status as eid_status,
                                        tob.created as created,
                                        tob.updated as updated
                                from tbl_object tob
                                inner join tbl_comment tco on tob.eid = tco.eid
                                where tob.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'        => $row['eid'],
                     'eid_type'   => $row['eid_type'],
                     'comment'    => $row['comment'],
                     'eid_status' => $row['eid_status'],
                     'created'    => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'    => \Flexio\Base\Util::formatDate($row['updated']));
    }
}
