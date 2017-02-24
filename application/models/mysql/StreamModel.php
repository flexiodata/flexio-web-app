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


class StreamModel extends ModelBase
{
    public function create($params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // create the stream object base
            $stream_eid = $this->getModel()->createObjectBase(Model::TYPE_STREAM, $params);
            if ($stream_eid === false)
                throw new \Exception();

            // add the stream properties
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'                  => $stream_eid,
                'name'                 => isset_or($params['name'], ''),
                'path'                 => isset_or($params['path'], ''),
                'size'                 => isset_or($params['size'], null),
                'hash'                 => isset_or($params['hash'], ''),
                'mime_type'            => isset_or($params['mime_type'], ''),
                'structure'            => isset_or($params['structure'], '[]'),
                'file_created'         => isset_or($params['file_created'], null),
                'file_modified'        => isset_or($params['file_modified'], null),
                'connection_eid'       => isset_or($params['connection_eid'], ''),
                'cache_path'           => isset_or($params['cache_path'], ''),
                'cache_connection_eid' => isset_or($params['cache_connection_eid'], ''),
                'created'              => $timestamp,
                'updated'              => $timestamp
            );

            if ($db->insert('tbl_stream', $process_arr) === false) // insert stream info
                throw new \Exception();

            $db->commit();
            return $stream_eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_CREATE_FAILED, _('Could not create stream'));
        }
    }

    public function delete($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

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
            return $this->fail(\Model::ERROR_DELETE_FAILED, _('Could not delete stream'));
        }
    }

    public function set($eid, $params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\System\Eid::isValid($eid))
            return false;

        if (($process_arr = \Model::check($params, array(
                'name'                 => array('type' => 'string',  'required' => false),
                'path'                 => array('type' => 'string',  'required' => false),
                'size'                 => array('type' => 'number',  'required' => false),
                'hash'                 => array('type' => 'string',  'required' => false),
                'mime_type'            => array('type' => 'string',  'required' => false),
                'structure'            => array('type' => 'string',  'required' => false),
                'file_created'         => array('type' => 'string',  'required' => false),
                'file_modified'        => array('type' => 'string',  'required' => false),
                'connection_eid'       => array('type' => 'eid',     'required' => false),
                'cache_path'           => array('type' => 'string',  'required' => false),
                'cache_connection_eid' => array('type' => 'eid',     'required' => false)
            ))) === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _('Could not update stream'));
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db->beginTransaction();
        try
        {
            // set the base object properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            if ($result === false)
            {
                // simply return false; no exception
                $db->commit();
                return false;
            }

            // update the stream info
            $db->update('tbl_stream', $process_arr, 'eid = ' . $db->quote($eid));

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not update stream'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!\Flexio\System\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

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
                     'created'              => \Flexio\System\Util::formatDate($row['created']),
                     'updated'              => \Flexio\System\Util::formatDate($row['updated']));
    }
}
