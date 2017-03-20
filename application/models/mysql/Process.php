<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Gold Prairie Website
 * Author:   Aaron L. Williams
 * Created:  2016-03-25
 *
 * @package gp
 * @subpackage Model
 */


class Process extends ModelBase
{
    public function create($params, $primary_process = true)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        $db->beginTransaction(); // needed to make sure eid generation is safe
        try
        {
            // if it's a primary process, create the object base; otherwise, create
            // a table-specific eid to track the subprocess
            if ($primary_process === true)
            {
                $eid = $this->getModel()->createObjectBase(Model::TYPE_PROCESS, $params);
                $params['process_eid'] = $eid;
            }
             else
            {
                $eid = $this->generateChildProcessEid();
            }

            if ($eid === false)
                throw new \Exception();

            // make sure the process eid doesn't exist as a child process eid
            if ($this->processExists($eid))
                throw new \Exception();

            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'parent_eid'     => isset_or($params['parent_eid'], ''),
                'process_eid'    => isset_or($params['process_eid'], ''),
                'process_mode'   => isset_or($params['process_mode'], ''),
                'process_hash'   => isset_or($params['process_hash'], ''),
                'impl_revision'  => isset_or($params['impl_revision'], ''),
                'task_type'      => isset_or($params['task_type'], ''),
                'task_version'   => isset_or($params['task_version'], 0),
                'task'           => isset_or($params['task'], '[]'),
                'input'          => isset_or($params['input'], '[]'),
                'output'         => isset_or($params['output'], '[]'),
                'input_params'   => isset_or($params['input_params'], '{}'),
                'output_params'  => isset_or($params['output_params'], '{}'),
                'started_by'     => isset_or($params['started_by'], ''),
                'started'        => isset_or($params['started'], null),
                'finished'       => isset_or($params['finished'], null),
                'process_info'   => isset_or($params['process_info'], '{}'),
                'process_status' => isset_or($params['process_status'], \Model::PROCESS_STATUS_PENDING),
                'cache_used'     => isset_or($params['cache_used'], ''),
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
            return $this->fail(\Flexio\Base\Error::CREATE_FAILED, _('Could not create process'));
        }
    }

    public function delete($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // delete the object
            $result = $this->getModel()->deleteObjectBase($eid);
            if ($result === false)
                throw new \Exception();

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(\Flexio\Base\Error::DELETE_FAILED, _('Could not delete process'));
        }
    }

    public function set($eid, $params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
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
            ))) === false)
            return $this->fail(\Flexio\Base\Error::WRITE_FAILED, _('Could not update process'));
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db->beginTransaction();
        try
        {
            // try to set the status; if the eid doesn't exist (subprocess, then
            // the following will fail silently, and additional properties will
            // have a chance to be set, which is what we want); after trying the
            // status, set the other properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            $db->update('tbl_process', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(\Flexio\Base\Error::WRITE_FAILED, _('Could not update process'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

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
                                  where tpr.eid = ?
                                 ", $eid);
         }
         catch (\Exception $e)
         {
             return $this->fail(\Flexio\Base\Error::READ_FAILED, _('Could not get the process'));
         }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'              => $row['eid'],
                     'eid_status'       => isset_or($row['eid_status'], \Model::STATUS_UNDEFINED),
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
                     'duration'         => \Flexio\Base\Util::formateDateDiff($row['started'], $row['finished']),
                     'process_info'     => $row['process_info'],
                     'process_status'   => $row['process_status'],
                     'cache_used'       => $row['cache_used'],
                     'created'          => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'          => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getProcessTree($eid)
    {
        // this function is almost identical to get(), except that it
        // returns both the parent process as well as all the subprocesses
        // using the process_eid as an additional node on the primary
        // process object

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Flexio\Base\Error::NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

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
             return $this->fail(\Flexio\Base\Error::READ_FAILED, _('Could not get the process'));
         }

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'              => $row['eid'],
                              'eid_status'       => isset_or($row['eid_status'], \Model::STATUS_UNDEFINED),
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
                              'duration'         => \Flexio\Base\Util::formateDateDiff($row['started'], $row['finished']),
                              'process_info'     => $row['process_info'],
                              'process_status'   => $row['process_status'],
                              'cache_used'       => $row['cache_used'],
                              'created'          => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'          => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function setProcessStatus($eid, $status)
    {
        $params['process_status'] = $status;
        return $this->set($eid, $params);
    }

    public function getProcessStatus($eid)
    {
        $process_info = $this->get($eid);
        if ($process_info === false)
            return \Model::PROCESS_STATUS_UNDEFINED;

        return $process_info['process_status'];
    }

    public function getOutputByHash($hash)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return false;

        try
        {
            // see if a process output exists for the existing hash
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
            return true;
         }
         catch (\Exception $e)
         {
             return false;
         }
    }

    private function processExists($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return false; // internal function, so don't flag an error

        $result = $db->fetchOne("select eid from tbl_process where eid= ?", $eid);
        if ($result !== false)
            return true;

        return false;
    }

    private function generateChildProcessEid()
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

        $db = $this->getDatabase();
        if ($db === false)
            return false; // internal function, so don't flag an error

        $eid = \Flexio\Base\Eid::generate();
        $result = $db->fetchOne("select eid from tbl_process where eid= ?", $eid);

        if ($result === false)
            return $eid;

        return $this->generateSubProcessEid();
    }
}
