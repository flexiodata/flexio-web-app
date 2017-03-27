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


class Stream extends ModelBase
{
    public function create($params)
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
                'name'                 => $params['name'] ?? '',
                'path'                 => $params['path'] ?? '',
                'size'                 => $size,
                'hash'                 => $params['hash'] ?? '',
                'mime_type'            => $params['mime_type'] ?? '',
                'structure'            => $params['structure'] ?? '[]',
                'file_created'         => $params['file_created'] ?? null,
                'file_modified'        => $params['file_modified'] ?? null,
                'connection_eid'       => $params['connection_eid'] ?? '',
                'cache_path'           => $params['cache_path'] ?? '',
                'cache_connection_eid' => $params['cache_connection_eid'] ?? '',
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

    public function delete($eid)
    {
        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
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

    public function set($eid, $params)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'name'                 => array('type' => 'string',  'required' => false),
                'path'                 => array('type' => 'string',  'required' => false),
                'size'                 => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'hash'                 => array('type' => 'string',  'required' => false),
                'mime_type'            => array('type' => 'string',  'required' => false),
                'structure'            => array('type' => 'string',  'required' => false),
                'file_created'         => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'file_modified'        => array('type' => 'any',     'required' => false), // TODO: workaround null problem; any = allow nulls
                'connection_eid'       => array('type' => 'eid',     'required' => false),
                'cache_path'           => array('type' => 'string',  'required' => false),
                'cache_connection_eid' => array('type' => 'eid',     'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
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

    public function get($eid)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tob.eid as eid,
                                        tob.eid_type as eid_type,
                                        tst.name as name,
                                        tst.path as path,
                                        tst.size as size,
                                        tst.hash as hash,
                                        tst.mime_type as mime_type,
                                        tst.structure as structure,
                                        tst.file_created as file_created,
                                        tst.file_modified as file_modified,
                                        tst.connection_eid as connection_eid,
                                        tst.cache_path as cache_path,
                                        tst.cache_connection_eid as cache_connection_eid,
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
                     'name'                 => $row['name'],
                     'path'                 => $row['path'],
                     'size'                 => $row['size'],
                     'hash'                 => $row['hash'],
                     'mime_type'            => $row['mime_type'],
                     'structure'            => $row['structure'],
                     'file_created'         => $row['file_created'],
                     'file_modified'        => $row['file_modified'],
                     'connection_eid'       => $row['connection_eid'],
                     'cache_path'           => $row['cache_path'],
                     'cache_connection_eid' => $row['cache_connection_eid'],
                     'eid_status'           => $row['eid_status'],
                     'created'              => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'              => \Flexio\Base\Util::formatDate($row['updated']));
    }
}
