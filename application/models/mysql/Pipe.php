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
                'eid_status'      => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'name'            => $params['name'] ?? '',
                'description'     => $params['description'] ?? '',
                'input'           => $params['input'] ?? '{}',
                'output'          => $params['output'] ?? '{}',
                'task'            => $params['task'] ?? '{}',
                'schedule'        => $params['schedule'] ?? '',
                'schedule_status' => $params['schedule_status'] ?? \Model::PIPE_STATUS_INACTIVE,
                'owned_by'        => $params['owned_by'] ?? '',
                'created_by'      => $params['created_by'] ?? '',
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'      => array('type' => 'string',  'required' => false),
                'name'            => array('type' => 'string',  'required' => false),
                'description'     => array('type' => 'string',  'required' => false),
                'input'           => array('type' => 'string',  'required' => false),
                'output'          => array('type' => 'string',  'required' => false),
                'task'            => array('type' => 'string',  'required' => false),
                'schedule'        => array('type' => 'string',  'required' => false),
                'schedule_status' => array('type' => 'string',  'required' => false),
                'owned_by'        => array('type' => 'string',  'required' => false),
                'created_by'      => array('type' => 'string',  'required' => false)
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
            $row = $db->fetchRow("select tpi.eid as eid,
                                         '".\Model::TYPE_PIPE."' as eid_type,
                                         tpi.eid_status as eid_status,
                                         tob.ename as ename,
                                         tpi.name as name,
                                         tpi.description as description,
                                         tpi.input as input,
                                         tpi.output as output,
                                         tpi.task as task,
                                         tpi.schedule as schedule,
                                         tpi.schedule_status as schedule_status,
                                         tpi.owned_by as owned_by,
                                         tpi.created_by as created_by,
                                         tpi.created as created,
                                         tpi.updated as updated
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
                     'eid_status'      => $row['eid_status'],
                     'ename'           => $row['ename'],
                     'name'            => $row['name'],
                     'description'     => $row['description'],
                     'input'           => $row['input'],
                     'output'          => $row['output'],
                     'task'            => $row['task'],
                     'schedule'        => $row['schedule'],
                     'schedule_status' => $row['schedule_status'],
                     'owned_by'        => $row['owned_by'],
                     'created_by'      => $row['created_by'],
                     'created'         => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'         => \Flexio\Base\Util::formatDate($row['updated']));
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
            $db->update('tbl_pipe', $process_arr, 'eid = ' . $db->quote($eid));
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

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_pipe where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
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
