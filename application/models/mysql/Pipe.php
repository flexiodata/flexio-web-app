<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
 *
 * @package flexio
 * @subpackage Model
 */


class Pipe extends ModelBase
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
            $eid = $this->getModel()->createObjectBase(Model::TYPE_PIPE, $params);
            if ($eid === false)
                throw new \Exception();

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'             => $eid,
                'name'            => isset_or($params['name'], ''),
                'description'     => isset_or($params['description'], ''),
                'display_icon'    => isset_or($params['display_icon'], ''),
                'input'           => isset_or($params['input'], '[]'),
                'output'          => isset_or($params['output'], '[]'),
                'task'            => isset_or($params['task'], '[]'),
                'schedule'        => isset_or($params['schedule'], ''),
                'schedule_status' => isset_or($params['schedule_status'], \Model::PIPE_STATUS_INACTIVE),
                'created'         => $timestamp,
                'updated'         => $timestamp
            );

            // make sure the schedule status is an 'A' or an 'I';
            // if it isn't, set it to an 'I'
            if ($process_arr['schedule_status'] != \Model::PIPE_STATUS_ACTIVE && $process_arr['schedule_status'] != \Model::PIPE_STATUS_INACTIVE)
                $process_arr['schedule_status'] = \Model::PIPE_STATUS_INACTIVE;

            // add the properties
            if ($db->insert('tbl_pipe', $process_arr) === false)
                throw new \Exception();

            $db->commit();
            return $eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_CREATE_FAILED, _('Could not create pipe'));
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
            return $this->fail(\Model::ERROR_DELETE_FAILED, _('Could not delete pipe'));
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
                'name'            => array('type' => 'string',  'required' => false),
                'description'     => array('type' => 'string',  'required' => false),
                'display_icon'    => array('type' => 'string',  'required' => false),
                'input'           => array('type' => 'string',  'required' => false),
                'output'          => array('type' => 'string',  'required' => false),
                'task'            => array('type' => 'string',  'required' => false),
                'schedule'        => array('type' => 'string',  'required' => false),
                'schedule_status' => array('type' => 'string',  'required' => false)
            ))) === false)
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update pipe'));
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

            // make sure the schedule status is an 'A' or an 'I';
            // if it isn't, set it to an 'I'
            if (isset($process_arr['schedule_status']))
            {
                if ($process_arr['schedule_status'] != \Model::PIPE_STATUS_ACTIVE && $process_arr['schedule_status'] != \Model::PIPE_STATUS_INACTIVE)
                    $process_arr['schedule_status'] = \Model::PIPE_STATUS_INACTIVE;
            }

            // set the properties
            $db->update('tbl_pipe', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update pipe'));
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
                                     tob.ename as ename,
                                     tpi.name as name,
                                     tpi.description as description,
                                     tpi.display_icon as display_icon,
                                     tpi.input as input,
                                     tpi.output as output,
                                     tpi.task as task,
                                     tpi.schedule as schedule,
                                     tpi.schedule_status as schedule_status,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_pipe tpi on tob.eid = tpi.eid
                              where tob.eid = ?
                             ", $eid);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'             => $row['eid'],
                     'eid_type'        => $row['eid_type'],
                     'ename'           => $row['ename'],
                     'name'            => $row['name'],
                     'description'     => $row['description'],
                     'display_icon'    => $row['display_icon'],
                     'input'           => $row['input'],
                     'output'          => $row['output'],
                     'task'            => $row['task'],
                     'schedule'        => $row['schedule'],
                     'schedule_status' => $row['schedule_status'],
                     'eid_status'      => $row['eid_status'],
                     'created'         => \Flexio\System\Util::formatDate($row['created']),
                     'updated'         => \Flexio\System\Util::formatDate($row['updated']));
    }

    public function getScheduledPipes()
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        $sql = "select tpi.eid as eid, ".
               "       tpi.schedule as schedule ".
               "from tbl_pipe tpi " .
               "where tpi.schedule_status = 'A'";

        $res = $db->query($sql);
        if (!$res)
            return array();

        return $res->fetchAll();
    }

    public function getLastSchedulerUpdateTime()
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // see if we need to refresh our schedule table
        $sql = "select max(updated) as dt from tbl_pipe where length(schedule) > 0";
        $result = $db->query($sql);

        if (!$result)
            return false;

        $row = $result->fetch();
        return isset($row['dt']) ? $row['dt'] : false;
    }
}
