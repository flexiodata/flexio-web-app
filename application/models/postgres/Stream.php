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
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        try
        {
            // make sure the size is an integer or null
            $size = null;
            if (isset($params['size']))
            {
                if (!is_numeric($params['size']))
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

                $size = $params['size'];
            }

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_STREAM, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'                  => $eid,
                'eid_status'           => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'parent_eid'           => $params['parent_eid'] ?? '',
                'stream_type'          => $params['stream_type'] ?? '',
                'name'                 => $params['name'] ?? '',
                'path'                 => $params['path'] ?? '',
                'size'                 => $size,
                'hash'                 => $params['hash'] ?? '',
                'mime_type'            => $params['mime_type'] ?? '',
                'structure'            => $params['structure'] ?? '[]',
                'file_created'         => $params['file_created'] ?? null,
                'file_modified'        => $params['file_modified'] ?? null,
                'connection_eid'       => $params['connection_eid'] ?? '',
                'expires'              => $params['expires'] ?? null,
                'owned_by'             => $params['owned_by'] ?? '',
                'created_by'           => $params['created_by'] ?? '',
                'created'              => $timestamp,
                'updated'              => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_stream', $process_arr) === false) // insert stream info
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

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'           => array('type' => 'string',  'required' => false),
                'parent_eid'           => array('type' => 'eid',     'required' => false),
                'stream_type'          => array('type' => 'string',  'required' => false),
                'name'                 => array('type' => 'string',  'required' => false),
                'path'                 => array('type' => 'string',  'required' => false),
                'size'                 => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'hash'                 => array('type' => 'string',  'required' => false),
                'mime_type'            => array('type' => 'string',  'required' => false),
                'structure'            => array('type' => 'string',  'required' => false),
                'file_created'         => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'file_modified'        => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'connection_eid'       => array('type' => 'eid',     'required' => false),
                'expires'              => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'owned_by'             => array('type' => 'string',  'required' => false),
                'created_by'           => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tst.eid as eid,
                                         '".\Model::TYPE_STREAM."' as eid_type,
                                         tst.eid_status as eid_status,
                                         tst.parent_eid as parent_eid,
                                         tst.stream_type as stream_type,
                                         tst.name as name,
                                         tst.path as path,
                                         tst.size as size,
                                         tst.hash as hash,
                                         tst.mime_type as mime_type,
                                         tst.structure as structure,
                                         tst.file_created as file_created,
                                         tst.file_modified as file_modified,
                                         tst.connection_eid as connection_eid,
                                         tst.expires as expires,
                                         tst.owned_by as owned_by,
                                         tst.created_by as created_by,
                                         tst.created as created,
                                         tst.updated as updated
                                from tbl_stream tst
                                where tst.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'                  => $row['eid'],
                     'eid_type'             => $row['eid_type'],
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

    public function setStatus(string $eid, string $status) : bool
    {
        return $this->set($eid, array('eid_status' => $status));
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

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_stream where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function queryStreams(array $conditions) : array
    {
        $row = false;
        $db = $this->getDatabase();
        try
        {
            $where = '';
            $where_arr = [];
            if (isset($conditions['parent_eid']))
            {
                $where .= (strlen($where) > 0 ? ' and ' : ' ') . 'tst.parent_eid = ?';
                $where_arr[] = $conditions['parent_eid'];
                unset($conditions['parent_eid']);
            }
            if (isset($conditions['name']))
            {
                $where .= (strlen($where) > 0 ? ' and ' : ' ') . 'tst.name = ?';
                $where_arr[] = $conditions['name'];
                unset($conditions['name']);
            }
            if (isset($conditions['stream_type']))
            {
                $where .= (strlen($where) > 0 ? ' and ' : ' ') . 'tst.stream_type = ?';
                $where_arr[] = $conditions['stream_type'];
                unset($conditions['stream_type']);
            }

            if (count($where_arr) == 0 || count($conditions) > 0)
            {
                // refuse to query with no conditions;
                // also refuse to query if unknown conditions are specified
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            }

            $rows = $db->fetchAll("select tst.eid as eid,
                                          '".\Model::TYPE_STREAM."' as eid_type,
                                          tst.eid_status as eid_status,
                                          tst.parent_eid as parent_eid,
                                          tst.stream_type as stream_type,
                                          tst.name as name,
                                          tst.path as path,
                                          tst.size as size,
                                          tst.hash as hash,
                                          tst.mime_type as mime_type,
                                          tst.structure as structure,
                                          tst.file_created as file_created,
                                          tst.file_modified as file_modified,
                                          tst.connection_eid as connection_eid,
                                          tst.expires as expires,
                                          tst.owned_by as owned_by,
                                          tst.created_by as created_by,
                                          tst.created as created,
                                          tst.updated as updated
                                from tbl_stream tst
                                where $where
                                ", $where_arr);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$rows)
            return array(); // don't flag an error, but acknowledge that object doesn't exist

        $output = array();
        foreach ($rows as $row)
        {
            $output[] =  array('eid'                  => $row['eid'],
                               'eid_type'             => $row['eid_type'],
                               'eid_status'           => $row['eid_status'],
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
                               'owned_by'             => $row['owned_by'],
                               'created_by'           => $row['created_by'],
                               'created'              => \Flexio\Base\Util::formatDate($row['created']),
                               'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
