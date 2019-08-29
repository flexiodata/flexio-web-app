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
                'description'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'syntax'          => array('type' => 'string', 'required' => false, 'default' => ''),
                'ui'              => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'task'            => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'schedule'        => array('type' => 'string', 'required' => false, 'default' => ''),
                'deploy_mode'     => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_MODE_BUILD),
                'deploy_schedule' => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'deploy_email'    => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'deploy_api'      => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
                'deploy_ui'       => array('type' => 'string', 'required' => false, 'default' => \Model::PIPE_DEPLOY_STATUS_INACTIVE),
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

        if ($process_arr['deploy_ui'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_ui'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
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
        // if the item doesn't exist, return false
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;
        if ($this->exists($eid) === false)
            return false;

        // set the status to deleted and clear out any existing name
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            $process_arr = array('eid_status' => \Model::STATUS_DELETED, 'name' => '');
            $db->update('tbl_pipe', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, (IS_DEBUG() ? $e->getMessage() : null));
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'      => array('type' => 'string', 'required' => false),
                'parent_eid'      => array('type' => 'string', 'required' => false),
                'name'            => array('type' => 'string', 'required' => false),
                'title'           => array('type' => 'string', 'required' => false),
                'description'     => array('type' => 'string', 'required' => false),
                'syntax'          => array('type' => 'string', 'required' => false),
                'ui'              => array('type' => 'string', 'required' => false),
                'task'            => array('type' => 'string', 'required' => false),
                'schedule'        => array('type' => 'string', 'required' => false),
                'deploy_mode'     => array('type' => 'string', 'required' => false),
                'deploy_schedule' => array('type' => 'string', 'required' => false),
                'deploy_email'    => array('type' => 'string', 'required' => false),
                'deploy_api'      => array('type' => 'string', 'required' => false),
                'deploy_ui'       => array('type' => 'string', 'required' => false),
                'owned_by'        => array('type' => 'string', 'required' => false),
                'created_by'      => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if the item doesn't exist, return false
        if ($this->exists($eid) === false)
            return false;

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            if (isset($params['name']))
            {
                // if an identifier is specified, make sure that it's unique within an owner and object type
                $qeid = $db->quote($eid);
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
            if (isset($process_arr['deploy_ui']))
            {
                if ($process_arr['deploy_ui'] != \Model::PIPE_DEPLOY_STATUS_ACTIVE && $process_arr['deploy_ui'] != \Model::PIPE_DEPLOY_STATUS_INACTIVE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
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
        return false;
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
                              'description'     => $row['description'],
                              'syntax'          => $row['syntax'],
                              'ui'              => $row['ui'],
                              'task'            => $row['task'],
                              'schedule'        => $row['schedule'],
                              'deploy_mode'     => $row['deploy_mode'],
                              'deploy_schedule' => $row['deploy_schedule'],
                              'deploy_email'    => $row['deploy_email'],
                              'deploy_api'      => $row['deploy_api'],
                              'deploy_ui'       => $row['deploy_ui'],
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
