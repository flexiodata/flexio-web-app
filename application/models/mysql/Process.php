<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-25
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Process extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'     => array('type' => 'string', 'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'parent_eid'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'pipe_info'      => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'process_mode'   => array('type' => 'string', 'required' => false, 'default' => ''),
                'task'           => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'input'          => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'output'         => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'triggered_by'   => array('type' => 'string', 'required' => false, 'default' => ''),
                'started_by'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'started'        => array('type' => 'date',   'required' => false, 'default' => null, 'allow_null' => true),
                'finished'       => array('type' => 'date',   'required' => false, 'default' => null, 'allow_null' => true),
                'process_info'   => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'process_status' => array('type' => 'string', 'required' => false, 'default' => ''),
                'cache_used'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'owned_by'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'     => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PROCESS, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

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
        // set the status to deleted
        $params = array('eid_status' => \Model::STATUS_DELETED);
        return $this->set($eid, $params);
    }

    public function purge(string $owner_eid) : bool
    {
        // this function deletes rows for a given owner

        if (!\Flexio\Base\Eid::isValid($owner_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qowner_eid = $db->quote($owner_eid);
            $sql = "delete from tbl_process where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $filter = array('eid' => $eid);
        return $this->update($filter, $params);
    }

    public function summary(array $filter) : array
    {
        // returns the number of processes per pipe per day for a particular owner,
        // along with the average and total times for those processes

        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max', 'parent_eid');
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
            "       from tbl_process ".
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

    public function update(array $filter, array $params) : bool
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'     => array('type' => 'string', 'required' => false),
                'parent_eid'     => array('type' => 'string', 'required' => false),
                'pipe_info'      => array('type' => 'string', 'required' => false),
                'process_mode'   => array('type' => 'string', 'required' => false),
                'task'           => array('type' => 'string', 'required' => false),
                'input'          => array('type' => 'string', 'required' => false),
                'output'         => array('type' => 'string', 'required' => false),
                'triggered_by'   => array('type' => 'string', 'required' => false),
                'started_by'     => array('type' => 'string', 'required' => false),
                'started'        => array('type' => 'date',   'required' => false, 'allow_null' => true),
                'finished'       => array('type' => 'date',   'required' => false, 'allow_null' => true),
                'process_info'   => array('type' => 'string', 'required' => false),
                'process_status' => array('type' => 'string', 'required' => false),
                'cache_used'     => array('type' => 'string', 'required' => false),
                'owned_by'       => array('type' => 'string', 'required' => false),
                'created_by'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        try
        {
            $updates_made = $db->update('tbl_process', $process_arr, $filter_expr);
            return $updates_made;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid');
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
                              'pipe_info'        => $row['pipe_info'],
                              'process_mode'     => $row['process_mode'],
                              'task'             => $row['task'],
                              'input'            => $row['input'],
                              'output'           => $row['output'],
                              'triggered_by'     => $row['triggered_by'],
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

    public function get(string $eid) : array
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $filter = array('eid' => $eid);
        $rows = $this->list($filter);
        if (count($rows) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        return $rows[0];
    }

    public function exists(string $eid) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $result = $this->getDatabase()->fetchOne("select eid from tbl_process where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function setProcessStatus(string $eid, string $status) : bool
    {
        $params['process_status'] = $status;
        return $this->set($eid, $params);
    }

    public function getProcessStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select process_status from tbl_process where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }
}
