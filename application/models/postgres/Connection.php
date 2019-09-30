<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2014-07-09
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Connection extends ModelBase
{
    public function create(array $params) : string
    {
        $default_name = \Flexio\Base\Util::generateRandomName('connection-');

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string', 'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'name'              => array('type' => 'identifier', 'required' => false, 'default' => $default_name),
                'title'             => array('type' => 'string', 'required' => false),
                'icon'              => array('type' => 'string', 'required' => false, 'default' => ''),
                'description'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'connection_type'   => array('type' => 'string', 'required' => false, 'default' => ''),
                'connection_mode'   => array('type' => 'string', 'required' => false, 'default' => \Model::CONNECTION_MODE_RESOURCE),
                'connection_status' => array('type' => 'string', 'required' => false, 'default' => \Model::CONNECTION_STATUS_UNAVAILABLE),
                'connection_info'   => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'setup_template'    => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'setup_config'      => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'expires'           => array('type' => 'date',   'required' => false, 'default' => null, 'allow_null' => true),
                'owned_by'          => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'        => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (self::isValidConnectionStatus($process_arr['connection_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // encrypt the connection and setup info
        $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);
        $process_arr['setup_template'] = \Flexio\Base\Util::encrypt($process_arr['setup_template'], $GLOBALS['g_store']->connection_enckey);
        $process_arr['setup_config'] = \Flexio\Base\Util::encrypt($process_arr['setup_config'], $GLOBALS['g_store']->connection_enckey);

        $db = $this->getDatabase();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_CONNECTION, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            // add the properties
            if ($db->insert('tbl_connection', $process_arr) === false)
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_connection where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function update(array $filter, array $params) : bool
    {
        // note: non-empty names are unique within owner and object type; this constraint
        // is enforced in the database schema

        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'name');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string', 'required' => false),
                'name'              => array('type' => 'string', 'required' => false),
                'title'             => array('type' => 'string', 'required' => false),
                'icon'              => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_mode'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'string', 'required' => false),
                'setup_template'    => array('type' => 'string', 'required' => false),
                'setup_config'      => array('type' => 'string', 'required' => false),
                'expires'           => array('type' => 'date',   'required' => false, 'allow_null' => true),
                'owned_by'          => array('type' => 'string', 'required' => false),
                'created_by'        => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (isset($params['connection_status']) && self::isValidConnectionStatus($params['connection_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if we're deleting the connection, clear out the name, connection_info, and setup_config and
        // set the connection status to unavailable; note: name is cleared out to free up the name for
        // use by another connection; connection_info and setup_config are cleared out to remove any
        // credentials as an extra precaution
        if (isset($process_arr['eid_status']) && $process_arr['eid_status'] === \Model::STATUS_DELETED)
        {
            $process_arr['name'] = '';
            $process_arr['connection_status'] = \Model::CONNECTION_STATUS_UNAVAILABLE;
            $process_arr['connection_info'] = '{}';
            $process_arr['setup_config'] = '{}';
        }

        // encrypt the connection and setup info
        if (isset($process_arr['connection_info']))
            $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);
        if (isset($process_arr['setup_template']))
            $process_arr['setup_template'] = \Flexio\Base\Util::encrypt($process_arr['setup_template'], $GLOBALS['g_store']->connection_enckey);
        if (isset($process_arr['setup_config']))
            $process_arr['setup_config'] = \Flexio\Base\Util::encrypt($process_arr['setup_config'], $GLOBALS['g_store']->connection_enckey);

        try
        {
            // set the properties
            $updates_made = $db->update('tbl_connection', $process_arr, $filter_expr);
            return $updates_made;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, (IS_DEBUG() ? $e->getMessage() : null));
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'name');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_connection where $filter_expr order by id $limit_expr";
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
            $connection_info = \Flexio\Base\Util::decrypt($row['connection_info'], $GLOBALS['g_store']->connection_enckey);
            $setup_template = \Flexio\Base\Util::decrypt($row['setup_template'], $GLOBALS['g_store']->connection_enckey);
            $setup_config = \Flexio\Base\Util::decrypt($row['setup_config'], $GLOBALS['g_store']->connection_enckey);

            // if we can't decrypt the info return empty param info
            $row['connection_info'] = $connection_info ?? '{}';
            $row['setup_template'] = $setup_template ?? '{}';
            $row['setup_config'] = $setup_config ?? '{}';

            $output[] = array('eid'               => $row['eid'],
                              'eid_type'          => \Model::TYPE_CONNECTION,
                              'eid_status'        => $row['eid_status'],
                              'name'              => $row['name'],
                              'title'             => $row['title'],
                              'icon'              => $row['icon'],
                              'description'       => $row['description'],
                              'connection_type'   => $row['connection_type'],
                              'connection_mode'   => $row['connection_mode'],
                              'connection_status' => $row['connection_status'],
                              'connection_info'   => $row['connection_info'],
                              'setup_template'    => $row['setup_template'],
                              'setup_config'      => $row['setup_config'],
                              'expires'           => $row['expires'],
                              'owned_by'          => $row['owned_by'],
                              'created_by'        => $row['created_by'],
                              'created'           => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'           => \Flexio\Base\Util::formatDate($row['updated']));
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
            $sql = "delete from tbl_connection where owned_by = $qowner_eid";
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
        $result = $this->getDatabase()->fetchOne("select eid from tbl_connection where owned_by = $qowner and name = $qname");

        if ($result === false)
            return false;

        return $result;
    }

    private static function isValidConnectionStatus(string $status) : bool
    {
        switch ($status)
        {
            case \Model::CONNECTION_STATUS_UNAVAILABLE:
            case \Model::CONNECTION_STATUS_AVAILABLE:
            case \Model::CONNECTION_STATUS_ERROR:
                return true;
        }

        return false;
    }
}
