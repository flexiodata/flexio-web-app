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
        // if an identifier is non-zero-length identifier is specified, make sure
        // it's valid; make sure it's not an eid to disambiguate lookups that rely
        // on both an eid and an alias
        if (isset($params['alias']) && $params['alias'] !== '')
        {
            $alias = $params['alias'];
            if (!is_string($alias))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (\Flexio\Base\Identifier::isValid($alias) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (\Flexio\Base\Eid::isValid($alias) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            if (isset($params['alias']) && $params['alias'] !== '')
            {
                // if an identifier is specified, make sure that it's unique within an owner
                $alias = $params['alias'];
                $ownedby = $params['owned_by'] ?? '';
                $qownedby = $db->quote($ownedby);
                $qalias = $db->quote($alias);
                $existing_item = $db->fetchOne("select eid from tbl_pipe where owned_by = $qownedby and alias = $qalias");
                if ($existing_item !== false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            }

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PIPE, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'             => $eid,
                'eid_status'      => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'alias'           => $params['alias'] ?? '',
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
        // set the status to deleted and clear out any existing alias
        $params = array('eid_status' => \Model::STATUS_DELETED, 'alias' => '');
        return $this->set($eid, $params);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'      => array('type' => 'string', 'required' => false),
                'alias'           => array('type' => 'alias',  'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'input'           => array('type' => 'string', 'required' => false),
                'output'          => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'string', 'required' => false),
                'schedule'        => array('type' => 'string', 'required' => false),
                'schedule_status' => array('type' => 'string', 'required' => false),
                'owned_by'        => array('type' => 'string', 'required' => false),
                'created_by'      => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // if the item doesn't exist, return false; TODO: throw exception instead?
            $existing_status = $this->getStatus($eid);
            if ($existing_status === \Model::STATUS_UNDEFINED)
            {
                $db->rollback();
                return false;
            }

            if (isset($params['alias']) && $params['alias'] !== '')
            {
                // if an identifier is specified, make sure that it's unique within an owner
                $qeid = $db->quote($eid);
                $owner_to_check = $process_arr['owned_by'] ?? false;
                if ($owner_to_check === false) // owner isn't specified; find out what it is
                    $owner_to_check = $db->fetchOne("select owned_by from tbl_pipe where eid = ?", $eid);

                if ($owner_to_check !== false)
                {
                    // we found an owner; see if the alias exists for the owner
                    $alias = $params['alias'];
                    $qownedby = $db->quote($owner_to_check);
                    $qalias = $db->quote($alias);
                    $existing_eid = $db->fetchOne("select eid from tbl_pipe where owned_by = $qownedby and alias = $qalias");

                    // don't allow an alias to be set if it's already used for another eid
                    // (but if the alias is passed for the same eid, it's ok, because it's
                    // just setting it to what it already is)
                    if ($existing_eid !== false && $existing_eid !== $eid)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
                }
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

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max', 'alias');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_pipe where $filter_expr order by id $limit_expr";
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
            $output[] = array('eid'             => $row['eid'],
                              'eid_type'        => \Model::TYPE_PIPE,
                              'eid_status'      => $row['eid_status'],
                              'alias'           => $row['alias'],
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

    public function getEidFromName(string $owner, string $alias) // TODO: add return type
    {
        // eids must correspond to a valid owner and alias (although these fields can
        // be empty strings, only allow lookups for specifically named, owned pipes)
        if (!\Flexio\Base\Eid::isValid($owner))
            return false;
        if (strlen($alias) === 0)
            return false;

        $db = $this->getDatabase();
        $qowner = $db->quote($owner);
        $qalias = $db->quote($alias);
        $result = $this->getDatabase()->fetchOne("select eid from tbl_pipe where owned_by = $qowner and alias = $qalias");
        if ($result === false)
            return false;

        return $result;
    }

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_pipe where eid = ?", $eid);
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

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_pipe where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function getScheduledPipes() : array
    {
        $sql = "select eid, schedule from tbl_pipe where schedule_status = 'A'";
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
