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
        $db->beginTransaction();
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
                'created'              => $timestamp,
                'updated'              => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_stream', $process_arr) === false) // insert stream info
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
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            $db->delete('tbl_stream', 'eid = ' . $db->quote($eid));
            
            // delete the object
            $result = $this->getModel()->deleteObjectBase($eid);
            $db->commit();
            return $result;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
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
                'expires'              => array('type' => 'any',     'required' => false)  // TODO: workaround null problem; any = allow nulls
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // set the base object properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            if ($result === false)
            {
                // object doesn't exist or is deleted
                $db->commit();
                return false;
            }

            // set the properties
            $db->update('tbl_stream', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
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
            $row = $db->fetchRow("select tob.eid as eid,
                                        tob.eid_type as eid_type,
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
                                        tob.eid_status as eid_status,
                                        tob.created as created,
                                        tob.updated as updated
                                from tbl_object tob
                                inner join tbl_stream tst on tob.eid = tst.eid
                                where tob.eid = ?
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
                     'created'              => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
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

            $rows = $db->fetchAll("select tob.eid as eid,
                                          tob.eid_type as eid_type,
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
                                          tob.eid_status as eid_status,
                                          tob.created as created,
                                          tob.updated as updated
                                from tbl_object tob
                                inner join tbl_stream tst on tob.eid = tst.eid
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
                               'created'              => \Flexio\Base\Util::formatDate($row['created']),
                               'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }




}
