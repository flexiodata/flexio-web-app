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
    public function create($params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_COMMENT, $params);
            if ($eid === false)
                throw new \Exception();

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'comment'       => isset_or($params['comment'], ''),
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
            return $this->fail(\Model::ERROR_CREATE_FAILED, _('Could not create comment'));
        }
    }

    public function delete($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

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
            return $this->fail(\Model::ERROR_DELETE_FAILED, _('Could not delete comment'));
        }
    }

    public function set($eid, $params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'comment' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _('Could not update comment'));
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

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
            $db->update('tbl_comment', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update comment'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

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
