<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-25
 *
 * @package gp
 * @subpackage Model
 */


declare(strict_types=1);


class Process extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction(); // needed to make sure eid generation is safe
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PROCESS, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'parent_eid'     => $params['parent_eid'] ?? '',
                'process_mode'   => $params['process_mode'] ?? '',
                'process_hash'   => $params['process_hash'] ?? '',
                'impl_revision'  => $params['impl_revision'] ?? '',
                'task'           => $params['task'] ?? '[]',
                'input'          => $params['input'] ?? '{}',
                'output'         => $params['output'] ?? '{}',
                'started_by'     => $params['started_by'] ?? '',
                'started'        => $params['started'] ?? null,
                'finished'       => $params['finished'] ?? null,
                'process_info'   => $params['process_info'] ?? '{}',
                'process_status' => $params['process_status'] ?? \Model::PROCESS_STATUS_PENDING,
                'cache_used'     => $params['cache_used'] ?? '',
                'created'        => $timestamp,
                'updated'        => $timestamp
            );

            if ($db->insert('tbl_process', $process_arr) === false)
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
                'parent_eid'     => array('type' => 'string',  'required' => false),
                'process_mode'   => array('type' => 'string',  'required' => false),
                'process_hash'   => array('type' => 'string',  'required' => false),
                'impl_revision'  => array('type' => 'string',  'required' => false),
                'task'           => array('type' => 'string',  'required' => false),
                'input'          => array('type' => 'string',  'required' => false),
                'output'         => array('type' => 'string',  'required' => false),
                'started_by'     => array('type' => 'string',  'required' => false),
                'started'        => array('type' => 'string',  'required' => false),
                'finished'       => array('type' => 'string',  'required' => false),
                'process_info'   => array('type' => 'string',  'required' => false),
                'process_status' => array('type' => 'string',  'required' => false),
                'cache_used'     => array('type' => 'string',  'required' => false)
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
                // object doesn't exist
                $db->commit();
                return false;
            }

            // set the properties
            $db->update('tbl_process', $process_arr, 'eid = ' . $db->quote($eid));
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
            $row = $db->fetchRow("select tpr.eid as eid,
                                         tpr.parent_eid as parent_eid,
                                         tpr.process_mode as process_mode,
                                         tpr.impl_revision as impl_revision,
                                         tpr.task as task,
                                         tpr.input as input,
                                         tpr.output as output,
                                         tpr.started_by as started_by,
                                         tpr.started as started,
                                         tpr.finished as finished,
                                         tpr.process_info as process_info,
                                         tpr.process_status as process_status,
                                         tpr.cache_used as cache_used,
                                         tob.eid_status as eid_status,
                                         tpr.created as created,
                                         tpr.updated as updated
                                  from tbl_process tpr
                                  inner join tbl_object tob on tpr.eid = tob.eid
                                  where tpr.eid = ?
                                 ", $eid);
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'              => $row['eid'],
                     'eid_status'       => $row['eid_status'] ?? \Model::STATUS_UNDEFINED,
                     'parent_eid'       => $row['parent_eid'],
                     'process_mode'     => $row['process_mode'],
                     'impl_revision'    => $row['impl_revision'],
                     'task'             => $row['task'],
                     'input'            => $row['input'],
                     'output'           => $row['output'],
                     'started_by'       => $row['started_by'],
                     'started'          => $row['started'],
                     'finished'         => $row['finished'],
                     'duration'         => \Flexio\Base\Util::formatDateDiff($row['started'], $row['finished']),
                     'process_info'     => $row['process_info'],
                     'process_status'   => $row['process_status'],
                     'cache_used'       => $row['cache_used'],
                     'created'          => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'          => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getProcessUserStats() : array
    {
        $db = $this->getDatabase();
        try
        {
            $rows = $db->fetchAll("select tas.target_eid as target_eid,
                                          tpr.parent_eid as parent_eid,
                                          tpr.created::DATE as created,
                                          avg(extract(epoch from (tpr.finished - tpr.started))) as average_time,
                                          sum(extract(epoch from (tpr.finished - tpr.started))) as total_time,
                                          count(*) as total_count
                                   from tbl_process tpr
                                   inner join tbl_association tas on tpr.eid = tas.source_eid
                                   where tpr.parent_eid != '' and tas.association_type = 'CRB'
                                   group by tas.target_eid, tpr.parent_eid, tpr.created::DATE
                                   order by created, parent_eid
                                 ");
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('user_eid'     => $row['target_eid'], // target for the 'created by' association
                              'pipe_eid'     => $row['parent_eid'],
                              'created'      => $row['created'],
                              'total_count'  => $row['total_count'],
                              'total_time'   => $row['total_time'],
                              'average_time' => $row['average_time']);
        }

        return $output;
    }

    public function getProcessCreationStats() : array
    {
        $db = $this->getDatabase();
        try
        {
            $rows = $db->fetchAll("select tas.target_eid as target_eid,
                                          tpr.created::DATE as created,
                                          avg(extract(epoch from (tpr.finished - tpr.started))) as average_time,
                                          sum(extract(epoch from (tpr.finished - tpr.started))) as total_time,
                                          count(*) as total_count
                                   from tbl_process tpr
                                   inner join tbl_association tas on tpr.eid = tas.source_eid
                                   where tas.association_type = 'PRO'
                                   group by tas.target_eid, tpr.created::DATE
                                   order by created, target_eid
                                 ");
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('pipe_eid'       => $row['target_eid'], // target_eid is what the process was created from
                              'created'        => $row['created'],
                              'total_count'    => $row['total_count'],
                              'total_time'     => $row['total_time'],
                              'average_time'   => $row['average_time']);
        }

        return $output;
    }

    public function getProcessTaskStats() : array
    {

        // DEPRECATED: process table no longer stores individual subprocesses
/*
        $db = $this->getDatabase();
        try
        {
            $rows = $db->fetchAll("select tpr.task_type as task_type,
                                         count(case when tpr.process_status = ''  then 1 end) as undefined,
                                         count(case when tpr.process_status = 'S' then 1 end) as pending,
                                         count(case when tpr.process_status = 'W' then 1 end) as waiting,
                                         count(case when tpr.process_status = 'R' then 1 end) as running,
                                         count(case when tpr.process_status = 'X' then 1 end) as cancelled,
                                         count(case when tpr.process_status = 'P' then 1 end) as paused,
                                         count(case when tpr.process_status = 'F' then 1 end) as failed,
                                         count(case when tpr.process_status = 'C' then 1 end) as completed,
                                         avg(extract(epoch from (tpr.finished - tpr.started))) as average_time,
                                         sum(extract(epoch from (tpr.finished - tpr.started))) as total_time,
                                         count(*) as total_count
                                   from tbl_process tpr
                                   where tpr.process_eid != tpr.eid
                                   group by task_type
                                   order by total_count desc, task_type
                                 ");
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('task_type'    => $row['task_type'],
                              'undefined'    => $row['undefined'],
                              'pending'      => $row['pending'],
                              'waiting'      => $row['waiting'],
                              'running'      => $row['running'],
                              'cancelled'    => $row['cancelled'],
                              'paused'       => $row['paused'],
                              'failed'       => $row['failed'],
                              'completed'    => $row['completed'],
                              'total_count'  => $row['total_count'],
                              'total_time'   => $row['total_time'],
                              'average_time' => $row['average_time']);
        }

        return $output;
*/
    }

    public function setProcessStatus(string $eid, string $status) : bool
    {
        $params['process_status'] = $status;
        return $this->set($eid, $params);
    }

    public function getProcessStatus(string $eid) : string
    {
        $process_info = $this->get($eid);
        if ($process_info === false)
            return \Model::PROCESS_STATUS_UNDEFINED;

        return $process_info['process_status'];
    }

    public function getOutputByHash(string $hash) // TODO: add return type
    {
        // don't pull results for empty string
        if (strlen($hash) === 0)
            return false;

        try
        {
            // see if a process output exists for the existing hash
            $db = $this->getDatabase();
            $rows = $db->fetchAll("select tpr.eid as eid,
                                          tpr.output as output
                                   from tbl_process tpr
                                   where tpr.process_hash = ?
                                  ", $hash);

            // if we don't have any rows, return false
            if (!$rows)
                return false;

            // use the first row; TODO: if we do have multiple hashes that
            // are the same, there should only be one original from which the
            // others are based; we may want to track this and explicitly check
            // that we only have one original and return false otherise in order
            // to prevent the very unlikely case of where we might find a cached
            // result that's based on some other input and task
            $output = $rows[0]['output'];
            return $output;
         }
         catch (\Exception $e)
         {
             return false;
         }
    }
}
