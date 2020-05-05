<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-19
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Stream extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'     => array('type' => 'string',  'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'parent_eid'     => array('type' => 'string',  'required' => false, 'default' => ''), // validate with string instead of eid because of default
                'connection_eid' => array('type' => 'string',  'required' => false, 'default' => ''), // validate with string instead of eid because of default
                'stream_type'    => array('type' => 'string',  'required' => false, 'default' => ''),
                'name'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'path'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'size'           => array('type' => 'integer', 'required' => false, 'default' => null, 'allow_null' => true),
                'hash'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'mime_type'      => array('type' => 'string',  'required' => false, 'default' => ''),
                'structure'      => array('type' => 'string',  'required' => false, 'default' => '[]'),
                'file_created'   => array('type' => 'date',    'required' => false, 'default' => null, 'allow_null' => true),
                'file_modified'  => array('type' => 'date',    'required' => false, 'default' => null, 'allow_null' => true),
                'expires'        => array('type' => 'date',    'required' => false, 'default' => null, 'allow_null' => true),
                'owned_by'       => array('type' => 'string',  'required' => false, 'default' => ''),
                'created_by'     => array('type' => 'string',  'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_STREAM, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            if ($db->insert('tbl_stream', $process_arr) === false)
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_stream where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function update(array $filter, array $params) : bool
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid', 'connection_eid', 'name', 'stream_type', 'hash');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'     => array('type' => 'string',  'required' => false),
                'parent_eid'     => array('type' => 'eid',     'required' => false),
                'connection_eid' => array('type' => 'eid',     'required' => false),
                'stream_type'    => array('type' => 'string',  'required' => false),
                'name'           => array('type' => 'string',  'required' => false),
                'path'           => array('type' => 'string',  'required' => false),
                'size'           => array('type' => 'integer', 'required' => false, 'allow_null' => true),
                'hash'           => array('type' => 'string',  'required' => false),
                'mime_type'      => array('type' => 'string',  'required' => false),
                'structure'      => array('type' => 'string',  'required' => false),
                'file_created'   => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'file_modified'  => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'expires'        => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'owned_by'       => array('type' => 'string',  'required' => false),
                'created_by'     => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        try
        {
            $udpates_made = $db->update('tbl_stream', $process_arr, $filter_expr);
            return $udpates_made;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'parent_eid', 'connection_eid', 'name', 'stream_type', 'hash');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_stream where $filter_expr order by id $limit_expr";
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
            $output[] = array('eid'                  => $row['eid'],
                              'eid_type'             => \Model::TYPE_STREAM,
                              'parent_eid'           => $row['parent_eid'],
                              'connection_eid'       => $row['connection_eid'],
                              'stream_type'          => $row['stream_type'],
                              'name'                 => $row['name'],
                              'path'                 => $row['path'],
                              'size'                 => $row['size'],
                              'hash'                 => $row['hash'],
                              'mime_type'            => $row['mime_type'],
                              'structure'            => $row['structure'],
                              'file_created'         => $row['file_created'],
                              'file_modified'        => $row['file_modified'],
                              'expires'              => $row['expires'],
                              'eid_status'           => $row['eid_status'],
                              'owned_by'             => $row['owned_by'],
                              'created_by'           => $row['created_by'],
                              'created'              => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
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
            $sql = "delete from tbl_stream where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }
}
