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


declare(strict_types=1);


class Pipe extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PIPE, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'             => $eid,
                'name'            => $params['name'] ?? '',
                'description'     => $params['description'] ?? '',
                'display_icon'    => $params['display_icon'] ?? '',
                'input'           => $params['input'] ?? '{}',
                'output'          => $params['output'] ?? '{}',
                'task'            => $params['task'] ?? '{}',
                'schedule'        => $params['schedule'] ?? '',
                'schedule_status' => $params['schedule_status'] ?? \Model::PIPE_STATUS_INACTIVE,
                'created'         => $timestamp,
                'updated'         => $timestamp
            );

            // make sure the schedule status is an 'A' or an 'I'
            if ($process_arr['schedule_status'] != \Model::PIPE_STATUS_ACTIVE && $process_arr['schedule_status'] != \Model::PIPE_STATUS_INACTIVE)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // add the properties
            if ($db->insert('tbl_pipe', $process_arr) === false)
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
                'name'            => array('type' => 'string',  'required' => false),
                'description'     => array('type' => 'string',  'required' => false),
                'display_icon'    => array('type' => 'string',  'required' => false),
                'input'           => array('type' => 'string',  'required' => false),
                'output'          => array('type' => 'string',  'required' => false),
                'task'            => array('type' => 'string',  'required' => false),
                'schedule'        => array('type' => 'string',  'required' => false),
                'schedule_status' => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
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

            // make sure the schedule status is an 'A' or an 'I'
            if (isset($process_arr['schedule_status']))
            {
                if ($process_arr['schedule_status'] != \Model::PIPE_STATUS_ACTIVE && $process_arr['schedule_status'] != \Model::PIPE_STATUS_INACTIVE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            }

            // set the properties
            $db->update('tbl_pipe', $process_arr, 'eid = ' . $db->quote($eid));
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
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

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
                     'created'         => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'         => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getScheduledPipes() : array
    {
        $sql = "select tpi.eid as eid, ".
               "       tpi.schedule as schedule ".
               "from tbl_pipe tpi " .
               "where tpi.schedule_status = 'A'";

        $res = $this->getDatabase()->query($sql);
        if (!$res)
            return array();

        return $res->fetchAll();
    }

    public function getLastSchedulerUpdateTime() // TODO: add return type
    {
        // see if we need to refresh our schedule table
        $sql = "select max(updated) as dt from tbl_pipe where length(schedule) > 0";
        $result = $this->getDatabase()->query($sql);

        if (!$result)
            return false;

        $row = $result->fetch();
        return isset($row['dt']) ? $row['dt'] : false;
    }
}
