<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2013-10-30
 *
 * @package flexio
 * @subpackage Model
 */


class ProjectModel extends ModelBase
{
    public function create($params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(Model::TYPE_PROJECT, $params);
            if ($eid === false)
                throw new \Exception();

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'name'           => isset_or($params['name'], ''),
                'description'    => isset_or($params['description'], ''),
                'display_icon'   => isset_or($params['display_icon'], ''),
                'created'        => $timestamp,
                'updated'        => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_project', $process_arr) === false)
                throw new \Exception();

            $db->commit();
            return $eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_CREATE_FAILED, _('Could not create project'));
        }
    }

    public function delete($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

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
            return $this->fail(Model::ERROR_DELETE_FAILED, _('Could not delete project'));
        }
    }

    public function set($eid, $params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!\Flexio\System\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'name'          => array('type' => 'string', 'required' => false),
                'description'   => array('type' => 'string', 'required' => false),
                'display_icon'  => array('type' => 'string', 'required' => false)
            ))) === false)
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update project'));
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
            $db->update('tbl_project', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update project'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!\Flexio\System\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tob.ename as ename,
                                     tpr.name as name,
                                     tpr.description as description,
                                     tpr.display_icon as display_icon,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_project tpr on tob.eid = tpr.eid
                              where tob.eid = ?
                             ", $eid);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'          => $row['eid'],
                     'eid_type'     => $row['eid_type'],
                     'ename'        => $row['ename'],
                     'name'         => $row['name'],
                     'description'  => $row['description'],
                     'display_icon' => $row['display_icon'],
                     'eid_status'   => $row['eid_status'],
                     'created'      => \Flexio\System\Util::formatDate($row['created']),
                     'updated'      => \Flexio\System\Util::formatDate($row['updated']));
    }
}
