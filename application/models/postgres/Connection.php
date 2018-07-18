<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie, Inc.  All rights reserved.
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
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string', 'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'alias'             => array('type' => 'alias',  'required' => false, 'default' => ''),
                'name'              => array('type' => 'string', 'required' => false, 'default' => ''),
                'description'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'connection_type'   => array('type' => 'string', 'required' => false, 'default' => ''),
                'connection_status' => array('type' => 'string', 'required' => false, 'default' => \Model::CONNECTION_STATUS_UNAVAILABLE),
                'connection_info'   => array('type' => 'string', 'required' => false, 'default' => '{}'),
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

        // encrypt the connection info
        $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            if ($process_arr['alias'] !== '')
            {
                // if an identifier is specified, make sure that it's unique within an owner
                $qownedby = $db->quote($process_arr['owned_by']);
                $qalias = $db->quote($process_arr['alias']);
                $existing_item = $db->fetchOne("select eid from tbl_connection where owned_by = $qownedby and alias = $qalias");
                if ($existing_item !== false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_CONNECTION, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            // add the properties
            if ($db->insert('tbl_connection', $process_arr) === false)
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

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string', 'required' => false),
                'alias'             => array('type' => 'alias',  'required' => false),
                'name'              => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'connection_type'   => array('type' => 'string', 'required' => false),
                'connection_status' => array('type' => 'string', 'required' => false),
                'connection_info'   => array('type' => 'string', 'required' => false),
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

        // encrypt the connection info
        if (isset($process_arr['connection_info']))
            $process_arr['connection_info'] = \Flexio\Base\Util::encrypt($process_arr['connection_info'], $GLOBALS['g_store']->connection_enckey);

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
                    $owner_to_check = $db->fetchOne("select owned_by from tbl_connection where eid = ?", $eid);

                if ($owner_to_check !== false)
                {
                    // we found an owner; see if the alias exists for the owner
                    $alias = $params['alias'];
                    $qownedby = $db->quote($owner_to_check);
                    $qalias = $db->quote($alias);
                    $existing_eid = $db->fetchOne("select eid from tbl_connection where owned_by = $qownedby and alias = $qalias");

                    // don't allow an alias to be set if it's already used for another eid
                    // (but if the alias is passed for the same eid, it's ok, because it's
                    // just setting it to what it already is)
                    if ($existing_eid !== false && $existing_eid !== $eid)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
                }
            }

            // set the properties
            $db->update('tbl_connection', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, (IS_DEBUG() ? $e->getMessage() : null));
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
            $row['connection_info'] = \Flexio\Base\Util::decrypt($row['connection_info'], $GLOBALS['g_store']->connection_enckey);

            $output[] = array('eid'               => $row['eid'],
                              'eid_type'          => \Model::TYPE_CONNECTION,
                              'eid_status'        => $row['eid_status'],
                              'alias'             => $row['alias'],
                              'name'              => $row['name'],
                              'description'       => $row['description'],
                              'connection_type'   => $row['connection_type'],
                              'connection_status' => $row['connection_status'],
                              'connection_info'   => $row['connection_info'],
                              'expires'           => $row['expires'],
                              'owned_by'          => $row['owned_by'],
                              'created_by'        => $row['created_by'],
                              'created'           => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'           => \Flexio\Base\Util::formatDate($row['updated']));
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
        $result = $this->getDatabase()->fetchOne("select eid from tbl_connection where owned_by = $qowner and alias = $qalias");

        if ($result === false)
            return false;

        return $result;
    }

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_connection where eid = ?", $eid);
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

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_connection where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

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
