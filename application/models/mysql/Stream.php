<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
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
                'stream_type'    => array('type' => 'string',  'required' => false, 'default' => ''),
                'name'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'path'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'size'           => array('type' => 'integer', 'required' => false, 'default' => null, 'allow_null' => true),
                'hash'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'mime_type'      => array('type' => 'string',  'required' => false, 'default' => ''),
                'structure'      => array('type' => 'string',  'required' => false, 'default' => '[]'),
                'file_created'   => array('type' => 'date',    'required' => false, 'default' => null, 'allow_null' => true),
                'file_modified'  => array('type' => 'date',    'required' => false, 'default' => null, 'allow_null' => true),
                'connection_eid' => array('type' => 'string',  'required' => false, 'default' => ''), // validate with string instead of eid because of default
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
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

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'     => array('type' => 'string',  'required' => false),
                'parent_eid'     => array('type' => 'eid',     'required' => false),
                'stream_type'    => array('type' => 'string',  'required' => false),
                'name'           => array('type' => 'string',  'required' => false),
                'path'           => array('type' => 'string',  'required' => false),
                'size'           => array('type' => 'integer', 'required' => false, 'allow_null' => true),
                'hash'           => array('type' => 'string',  'required' => false),
                'mime_type'      => array('type' => 'string',  'required' => false),
                'structure'      => array('type' => 'string',  'required' => false),
                'file_created'   => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'file_modified'  => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'connection_eid' => array('type' => 'eid',     'required' => false),
                'expires'        => array('type' => 'date',    'required' => false, 'allow_null' => true),
                'owned_by'       => array('type' => 'string',  'required' => false),
                'created_by'     => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            // if the item doesn't exist, return false; TODO: throw exception instead?
            $existing_status = $this->getStatus($eid);
            if ($existing_status === \Model::STATUS_UNDEFINED)
                return false;

            // set the properties
            $db->update('tbl_stream', $process_arr, 'eid = ' . $db->quote($eid));
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
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max', 'parent_eid', 'connection_eid', 'name', 'stream_type');
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
                              'stream_type'          => $row['stream_type'],
                              'name'                 => $row['name'],
                              'path'                 => $row['path'],
                              'size'                 => $row['size'],
                              'hash'                 => $row['hash'],
                              'mime_type'            => $row['mime_type'],
                              'structure'            => $row['structure'],
                              'file_created'         => $row['file_created'],
                              'file_modified'        => $row['file_modified'],
                              'connection_eid'       => $row['connection_eid'],
                              'expires'              => $row['expires'],
                              'eid_status'           => $row['eid_status'],
                              'owned_by'             => $row['owned_by'],
                              'created_by'           => $row['created_by'],
                              'created'              => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
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

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_stream where eid = ?", $eid);
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

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_stream where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }
}
