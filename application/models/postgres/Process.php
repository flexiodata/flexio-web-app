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
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PROCESS, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'eid_status'     => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'parent_eid'     => $params['parent_eid'] ?? '',
                'process_mode'   => $params['process_mode'] ?? '',
                'process_hash'   => $params['process_hash'] ?? '',
                'impl_revision'  => $params['impl_revision'] ?? '',
                'task'           => $params['task'] ?? '{}',
                'input'          => $params['input'] ?? '{}',
                'output'         => $params['output'] ?? '{}',
                'started_by'     => $params['started_by'] ?? '',
                'started'        => $params['started'] ?? null,
                'finished'       => $params['finished'] ?? null,
                'process_info'   => $params['process_info'] ?? '{}',
                'process_status' => $params['process_status'] ?? '',
                'cache_used'     => $params['cache_used'] ?? '',
                'owned_by'       => $params['owned_by'] ?? '',
                'created_by'     => $params['created_by'] ?? '',
                'created'        => $timestamp,
                'updated'        => $timestamp
            );

            if ($db->insert('tbl_process', $process_arr) === false)
                throw new \Exception();

            return $eid;
        }
        catch (\Exception $e)
        {
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
                'eid_status'     => array('type' => 'string',  'required' => false),
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
                'cache_used'     => array('type' => 'string',  'required' => false),
                'owned_by'       => array('type' => 'string',  'required' => false),
                'created_by'     => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        try
        {
            // if the item doesn't exist, return false; TODO: throw exception instead?
            $existing_status = $this->getStatus($eid);
            if ($existing_status === \Model::STATUS_UNDEFINED)
                return false;

            // set the properties
            $db->update('tbl_process', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function summary(array $filter) : array
    {
        // returns the number of processes per pipe per day for a particular owner,
        // along with the average and total times for those processes

        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'parent_eid', 'owned_by', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        try
        {
            $sql = "select owned_by as owned_by, ".
            "              parent_eid as parent_eid, ".
            "              created::DATE as created, ".
            "              avg(extract(epoch from (finished - started))) as average_time, ".
            "              sum(extract(epoch from (finished - started))) as total_time, ".
            "              count(*) as total_count ".
            "       from tbl_process tpr ".
            "       where $filter_expr ".
            "       group by owned_by, parent_eid, created::DATE ".
            "       order by created, parent_eid $limit_expr";
            $rows = $db->fetchAll($sql);
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        $output = array();
        foreach ($rows as $row)
        {
            $parent_eid = '';
            if (\Flexio\Base\Eid::isValid($row['parent_eid']))
                $parent_eid = $row['parent_eid'];

            $output[] = array('user_eid'     => $row['owned_by'],
                              'pipe_eid'     => $parent_eid,
                              'created'      => $row['created'],
                              'total_count'  => $row['total_count'],
                              'total_time'   => $row['total_time'],
                              'average_time' => $row['average_time']);
        }

        return $output;
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'parent_eid', 'owned_by', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_process where $filter_expr order by id $limit_expr";
            $rows = $db->fetchAll($query);
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
                              'eid_type'         => \Model::TYPE_PROCESS,
                              'eid_status'       => $row['eid_status'],
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
                              'owned_by'         => $row['owned_by'],
                              'created_by'       => $row['created_by'],
                              'created'          => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'          => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $filter = array('eid' => $eid);
        $rows = $this->list($filter);
        if (count($rows) === 0)
            return false;

        return $rows[0];
    }

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_process where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }

    public function setStatus(string $eid, string $status) : bool
    {
        return $this->set($eid, array('eid_status' => $status));
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_process where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function log(string $eid = null, string $process_eid, array $params) : string
    {
        // TODO: we may want to split this out into it's own model with create, get, set, etc,
        // like other model implementations; however use is isolated right now, so this is
        // convenient

        // make sure we have a process
        if ($this->processExists($process_eid) === false)
            return false;

        if (!isset($eid))
        {
            // if an eid isn't specified, generate one and create a new record
            $db = $this->getDatabase();
            $db->beginTransaction();
            try
            {
                $eid = $this->generateProcessLogEid();
                $timestamp = \Flexio\System\System::getTimestamp();
                $process_arr = array(
                    'eid'          => $eid,
                    'eid_status'   => \Model::STATUS_AVAILABLE, // only allow active items now
                    'process_eid'  => $process_eid,
                    'task_op'      => $params['task_op'] ?? '',
                    'task_version' => $params['task_version'] ?? 0,
                    'task'         => $params['task'] ?? '{}',
                    'input'        => $params['input'] ?? '{}',
                    'output'       => $params['output'] ?? '{}',
                    'started'      => $params['started'] ?? null,
                    'finished'     => $params['finished'] ?? null,
                    'log_type'     => $params['log_type'] ?? '',
                    'message'      => $params['message'] ?? '',
                    'created'      => $timestamp,
                    'updated'      => $timestamp
                );

                if ($db->insert('tbl_processlog', $process_arr) === false)
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
        else
        {
            // if an eid is specified, update the existing record
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($params, array(
                    'process_eid'  => array('type' => 'string',  'required' => false),
                    'task_op'      => array('type' => 'string',  'required' => false),
                    'task_version' => array('type' => 'integer', 'required' => false),
                    'task'         => array('type' => 'string',  'required' => false),
                    'input'        => array('type' => 'string',  'required' => false),
                    'output'       => array('type' => 'string',  'required' => false),
                    'started'      => array('type' => 'string',  'required' => false),
                    'finished'     => array('type' => 'string',  'required' => false),
                    'log_type'     => array('type' => 'string',  'required' => false),
                    'message'      => array('type' => 'string',  'required' => false)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $db = $this->getDatabase();
            $db->beginTransaction();
            try
            {
                $process_arr = $validator->getParams();
                $process_arr['updated'] = \Flexio\System\System::getTimestamp();
                $db->update('tbl_processlog', $process_arr, 'eid = ' . $db->quote($eid));
                $db->commit();
                return $eid;
            }
            catch (\Exception $e)
            {
                $db->rollback();
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            }
        }
    }

    public function getProcessLogEntries(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $db = $this->getDatabase();
        $rows = array();
        try
        {
            $rows = $db->fetchAll("select tpl.eid as eid,
                                          tpl.eid_status as eid_status,
                                          tpl.process_eid as process_eid,
                                          tpl.task_op as task_op,
                                          tpl.task_version as task_version,
                                          tpl.task as task,
                                          tpl.input as input,
                                          tpl.output as output,
                                          tpl.started as started,
                                          tpl.finished as finished,
                                          tpl.log_type as log_type,
                                          tpl.message as message,
                                          tpl.created as created,
                                          tpl.updated as updated
                                   from tbl_processlog tpl
                                   where tpl.process_eid = ?
                                   order by tpl.id
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
                              'eid_status'       => $row['eid_status'],
                              'process_eid'      => $row['process_eid'],
                              'task_op'          => $row['task_op'],
                              'task_version'     => $row['task_version'],
                              'task'             => $row['task'],
                              'input'            => $row['input'],
                              'output'           => $row['output'],
                              'started'          => $row['started'],
                              'finished'         => $row['finished'],
                              'duration'         => \Flexio\Base\Util::formatDateDiff($row['started'], $row['finished']),
                              'log_type'         => $row['log_type'],
                              'message'          => $row['message'],
                              'created'          => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'          => \Flexio\Base\Util::formatDate($row['updated']));
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
            return '';

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
            $rows = $db->fetchAll("select eid as eid,
                                          output as output
                                   from tbl_process tpr
                                   where process_hash = ?
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

    private function generateProcessLogEid() : string
    {
        // note: this function generates a unique log process eid; this
        // function is nearly identical to \Model::generateUniqueEid() except
        // that this function checks the tbl_processlog table for the eid instead
        // of the tbl_object table; a log process eid can coexist with the
        // object eids since log processes are only used in the log table as
        // a handle for getting/setting log entries and are not used in any
        // other table

        $eid = \Flexio\Base\Eid::generate();
        $result1 = $this->getDatabase()->fetchOne("select eid from tbl_processlog where eid = ?", $eid);
        $result2 = $this->getDatabase()->fetchOne("select eid from tbl_object where eid = ?", $eid);

        if ($result1 === false && $result2 === false)
            return $eid;

        return $this->generateProcessLogEid();
    }
}
