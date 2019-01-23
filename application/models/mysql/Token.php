<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-24
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Token extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'  => array('type' => 'string', 'required' => false, 'default' =>  \Model::STATUS_AVAILABLE),
                'access_code' => array('type' => 'string', 'required' => true), // needs to be unique
                'owned_by'    => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'  => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_TOKEN, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            if ($db->insert('tbl_token', $process_arr) === false)
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
            $sql = "delete from tbl_token where owned_by = $qowner_eid";
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
                'eid_status'  => array('type' => 'string', 'required' => false),
                'access_code' => array('type' => 'string', 'required' => false),
                'owned_by'    => array('type' => 'string', 'required' => false),
                'created_by'  => array('type' => 'string', 'required' => false)
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
        try
        {
            $db->update('tbl_token', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max', 'access_code');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_token where $filter_expr order by id $limit_expr";
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
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => \Model::TYPE_TOKEN,
                              'eid_status'  => $row['eid_status'],
                              'access_code' => $row['access_code'],
                              'owned_by'    => $row['owned_by'],
                              'created_by'  => $row['created_by'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_token where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function getFromAccessCode(string $code) : array
    {
        if (strlen($code) <= 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $filter = array('access_code' => $code);
        $rows = $this->list($filter);
        if (count($rows) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        return $rows[0];
    }
}
