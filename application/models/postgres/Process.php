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
    public function create(array $params = null, bool $primary_process = true) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction(); // needed to make sure eid generation is safe
        try
        {
            // if it's a primary process, create the object base; otherwise, create
            // a table-specific eid to track the subprocess
            $eid = false;
            if ($primary_process === true)
            {
                $eid = $this->getModel()->createObjectBase(\Model::TYPE_PROCESS, $params);
                $params['process_eid'] = $eid;
            }
             else
            {
                $eid = $this->generateChildProcessEid();
            }

            // make sure the process eid doesn't exist as a child process eid
            if ($this->processExists($eid) === true)
                throw new \Exception();

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'parent_eid'     => $params['parent_eid'] ?? '',
                'process_eid'    => $params['process_eid'] ?? '',
                'process_mode'   => $params['process_mode'] ?? '',
                'process_hash'   => $params['process_hash'] ?? '',
                'impl_revision'  => $params['impl_revision'] ?? '',
                'task_type'      => $params['task_type'] ?? '',
                'task_version'   => $params['task_version'] ?? 0,
                'task'           => $params['task'] ?? '[]',
                'input'          => $params['input'] ?? '[]',
                'output'         => $params['output'] ?? '[]',
                'input_params'   => $params['input_params'] ?? '{}',
                'output_params'  => $params['output_params'] ?? '{}',
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
        if (($process_arr = $validator->check($params, array(
                'parent_eid'     => array('type' => 'string',  'required' => false),
                'process_eid'    => array('type' => 'string',  'required' => false),
                'process_mode'   => array('type' => 'string',  'required' => false),
                'process_hash'   => array('type' => 'string',  'required' => false),
                'impl_revision'  => array('type' => 'string',  'required' => false),
                'task_type'      => array('type' => 'string',  'required' => false),
                'task_version'   => array('type' => 'integer', 'required' => false),
                'task'           => array('type' => 'string',  'required' => false),
                'input'          => array('type' => 'string',  'required' => false),
                'output'         => array('type' => 'string',  'required' => false),
                'input_params'   => array('type' => 'string',  'required' => false),
                'output_params'  => array('type' => 'string',  'required' => false),
                'started_by'     => array('type' => 'string',  'required' => false),
                'started'        => array('type' => 'string',  'required' => false),
                'finished'       => array('type' => 'string',  'required' => false),
                'process_info'   => array('type' => 'string',  'required' => false),
                'process_status' => array('type' => 'string',  'required' => false),
                'cache_used'     => array('type' => 'string',  'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // try to set the status; unlike other types of set operations, a valid
            // subprocess eid may exist even if an object eid doesn't exist, so don't
            // return before giving the subprocess properties a chance to be set if
            // the initial setting of properties returns false
            $result = $this->getModel()->setObjectBase($eid, $params);
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
            // note: some sub process records don't have an eid in the main
            // table object; left join so we can get the main process records
            // as well as subprocess records
            $row = $db->fetchRow("select tpr.eid as eid,
                                         tpr.parent_eid as parent_eid,
                                         tpr.process_eid as process_eid,
                                         tpr.process_mode as process_mode,
                                         tpr.impl_revision as impl_revision,
                                         tpr.task as task,
                                         tpr.input as input,
                                         tpr.output as output,
                                         tpr.input_params as input_params,
                                         tpr.output_params as output_params,
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
                                  left outer join tbl_object tob on tpr.eid = tob.eid
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
                     'process_eid'      => $row['process_eid'],
                     'process_mode'     => $row['process_mode'],
                     'impl_revision'    => $row['impl_revision'],
                     'task'             => $row['task'],
                     'input'            => $row['input'],
                     'output'           => $row['output'],
                     'input_params'     => $row['input_params'],
                     'output_params'    => $row['output_params'],
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

    public function getProcessTree(string $eid) // TODO: add return type
    {
        // this function is almost identical to get(), except that it
        // returns both the parent process as well as all the subprocesses
        // using the process_eid as an additional node on the primary
        // process object

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $db = $this->getDatabase();
        $rows = array();
        try
        {
            // note: some sub process records don't have an eid in the main
            // table object; left join so we can get the main process records
            // as well as subprocess records
            $rows = $db->fetchAll("select tpr.eid as eid,
                                          tpr.parent_eid as parent_eid,
                                          tpr.process_eid as process_eid,
                                          tpr.process_mode as process_mode,
                                          tpr.impl_revision as impl_revision,
                                          tpr.task_type as task_type,
                                          tpr.task_version as task_version,
                                          tpr.task as task,
                                          tpr.input as input,
                                          tpr.output as output,
                                          tpr.input_params as input_params,
                                          tpr.output_params as output_params,
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
                                   left outer join tbl_object tob on tpr.eid = tob.eid
                                   where tpr.process_eid = ?
                                   order by tpr.id
                                  ", $eid);
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'              => $row['eid'],
                              'eid_status'       => $row['eid_status'] ?? \Model::STATUS_UNDEFINED,
                              'parent_eid'       => $row['parent_eid'],
                              'process_eid'      => $row['process_eid'],
                              'process_mode'     => $row['process_mode'],
                              'impl_revision'    => $row['impl_revision'],
                              'task_type'        => $row['task_type'],
                              'task_version'     => $row['task_version'],
                              'task'             => $row['task'],
                              'input'            => $row['input'],
                              'output'           => $row['output'],
                              'input_params'     => $row['input_params'],
                              'output_params'    => $row['output_params'],
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

        return $output;
    }

    public function getProcessRunStats() : array
    {
        // TODO: implement
        return array();
    }

    public function getProcessTaskStats() : array
    {
        $db = $this->getDatabase();
        try
        {
            // note: get the process statistics by looking at all the subprocesses
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

    private function processExists(string $eid) : bool
    {
        try
        {
            $db = $this->getDatabase();
            $result = $db->fetchOne("select eid from tbl_process where eid= ?", $eid);
            if ($result !== false)
                return true;

            return false;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private function generateChildProcessEid() : string
    {
        // note: this function generates a unique child process eid; this
        // function is nearly identical to \Model::generateUniqueEid() except
        // that this function checks the tbl_process table for the eid instead
        // of the tbl_object table; a child process eid can coexist with the
        // object eids since child processes are only used as children of a parent
        // process and are not used in any other table; the only danger is
        // that after a child process eid is created that a normal object eid
        // for a process is created, so this is checked in the process object
        // creation

        $eid = \Flexio\Base\Eid::generate();
        $result = $this->getDatabase()->fetchOne("select eid from tbl_process where eid= ?", $eid);

        if ($result === false)
            return $eid;

        return $this->generateSubProcessEid();
    }
}
