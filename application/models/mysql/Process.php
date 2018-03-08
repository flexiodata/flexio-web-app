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
                'eid_status'     => $params['eid_status'] ?? \Model::STATUS_UNDEFINED,
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
                'cache_used'     => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // if an item is deleted, don't allow it to be edited
            $existing_status = $this->getStatus();
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
                                         tpr.eid_status as eid_status,
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
                                         tpr.created as created,
                                         tpr.updated as updated
                                  from tbl_process tpr
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
            $db->update('tbl_process', $process_arr, 'eid = ' . $db->quote($eid));
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

    public function getUserProcessStats() : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function getPipeProcessStats() : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function getProcessTaskStats() : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
