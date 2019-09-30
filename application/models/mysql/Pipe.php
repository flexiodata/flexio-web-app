<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
    public function create(array $params) : string
    {
        $default_name = \Flexio\Base\Util::generateRandomName('func-');

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'      => array('type' => 'string', 'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'parent_eid'      => array('type' => 'string', 'required' => false, 'default' => ''),
                'name'            => array('type' => 'identifier', 'required' => false, 'default' => $default_name),
                'title'           => array('type' => 'string', 'required' => false),
                'icon'            => array('type' => 'string', 'required' => false, 'default' => ''),
                'description'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'examples'        => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'params'          => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'notes'           => array('type' => 'string', 'required' => false, 'default' => ''),
                'task'            => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'schedule'        => array('type' => 'string', 'required' => false, 'default' => ''),
                'deploy_mode'     => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_MODE_BUILD),
                'deploy_schedule' => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'deploy_email'    => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'deploy_api'      => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'owned_by'        => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'      => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($process_arr['deploy_mode'] != \Model::PIPE_DEPLOY_MODE_BUILD && $process_arr['deploy_mode'] != \Model::PIPE_DEPLOY_MODE_RUN)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($process_arr['deploy_schedule'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_schedule'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($process_arr['deploy_email'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_email'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($process_arr['deploy_api'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_api'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // make sure a name is unique within an owner and object type
            $qownedby = $db->quote($process_arr['owned_by']);
            $qname = $db->quote($process_arr['name']);
            $existing_item = $db->fetchOne("select eid from tbl_pipe where owned_by = $qownedby and name = $qname");
            if ($existing_item !== false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_PIPE, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

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
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $filter = array('eid' => $eid);
        $params = array('eid_status' => \Model::STATUS_DELETED);
        return $this->update($filter, $params);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $filter = array('eid' => $eid);
        return $this->update($filter, $params);
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_pipe where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function update(array $filter, array $params) : bool
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid', 'name');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        // names need to be unique within for an owner and object type; if the name
        // is being set, make sure an eid is specified in the filter params, which
        // will guarantee we're not doing a bulk update on name; we'll still need
        // an additional check to make sure the name doesn't exist later
        if (isset($params['name']) && !isset($filter['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'      => array('type' => 'string', 'required' => false),
                'parent_eid'      => array('type' => 'string', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'title'           => array('type' => 'string', 'required' => false),
                'icon'            => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'examples'        => array('type' => 'string', 'required' => false),
                'params'          => array('type' => 'string', 'required' => false),
                'notes'           => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'string', 'required' => false),
                'schedule'        => array('type' => 'string', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false),
                'deploy_email'    => array('type' => 'string', 'required' => false),
                'deploy_api'      => array('type' => 'string', 'required' => false),
                'owned_by'        => array('type' => 'string', 'required' => false),
                'created_by'      => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if we're deleting pipes, clear out the name
        if (isset($process_arr['eid_status']) && $process_arr['eid_status'] === \Model::STATUS_DELETED)
        {
            $process_arr['name'] = '';
        }

        $db->beginTransaction();
        try
        {
            if (isset($params['name']))
            {
                // if an identifier is specified, make sure that it's unique within an owner and object type
                $eid = $filter['eid'];
                $owner_to_check = $process_arr['owned_by'] ?? false;
                if ($owner_to_check === false) // owner isn't specified; find out what it is
                    $owner_to_check = $db->fetchOne("select owned_by from tbl_pipe where eid = ?", $eid);

                if ($owner_to_check !== false)
                {
                    // we found an owner; see if the name exists for the owner and object type
                    $name = $params['name'];
                    $qownedby = $db->quote($owner_to_check);
                    $qname = $db->quote($name);
                    $existing_eid = $db->fetchOne("select eid from tbl_pipe where owned_by = $qownedby and name = $qname");

                    // don't allow a name to be set if it's already used for another eid
                    // (but if the name is passed for the same eid, it's ok, because it's
                    // just setting it to what it already is)
                    if ($existing_eid !== false && $existing_eid !== $eid)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
                }
            }

            // make sure the deploy mode is valid
            if (isset($process_arr['deploy_mode']))
            {
                if ($process_arr['deploy_mode'] != \Model::PIPE_DEPLOY_MODE_BUILD && $process_arr['deploy_mode'] != \Model::PIPE_DEPLOY_MODE_RUN)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            // make sure the deploy status is an 'A' or an 'I'
            if (isset($process_arr['deploy_schedule']))
            {
                if ($process_arr['deploy_schedule'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_schedule'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }
            if (isset($process_arr['deploy_email']))
            {
                if ($process_arr['deploy_email'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_email'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }
            if (isset($process_arr['deploy_api']))
            {
                if ($process_arr['deploy_api'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_api'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            // set the properties
            $updates_made = $db->update('tbl_pipe', $process_arr, $filter_expr);
            $db->commit();
            return $updates_made;
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
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid', 'name');
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
                              'parent_eid'      => $row['parent_eid'],
                              'name'            => $row['name'],
                              'title'           => $row['title'],
                              'icon'            => $row['icon'],
                              'description'     => $row['description'],
                              'examples'        => $row['examples'] ?? '{}',
                              'params'          => $row['params'] ?? '{}',
                              'notes'           => $row['notes'],
                              'task'            => $row['task'],
                              'schedule'        => $row['schedule'],
                              'deploy_mode'     => $row['deploy_mode'],
                              'deploy_schedule' => $row['deploy_schedule'],
                              'deploy_email'    => $row['deploy_email'],
                              'deploy_api'      => $row['deploy_api'],
                              'owned_by'        => $row['owned_by'],
                              'created_by'      => $row['created_by'],
                              'created'         => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'         => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
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
            $sql = "delete from tbl_pipe where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function getEidFromName(string $owner, string $name) // TODO: add return type
    {
        // eids must correspond to a valid owner and name
        if (!\Flexio\Base\Eid::isValid($owner))
            return false;
        if (strlen($name) === 0)
            return false;

        $db = $this->getDatabase();
        $qowner = $db->quote($owner);
        $qname = $db->quote($name);
        $result = $this->getDatabase()->fetchOne("select eid from tbl_pipe where owned_by = $qowner and name = $qname");
        if ($result === false)
            return false;

        return $result;
    }

    public function getScheduledPipes() : array
    {
        $sql = "select eid, schedule from tbl_pipe where deploy_mode = '".\Model::PIPE_DEPLOY_MODE_RUN."' and deploy_schedule = '".\Model::PIPE_DEPLOY_STATUS_ACTIVE."'";
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
