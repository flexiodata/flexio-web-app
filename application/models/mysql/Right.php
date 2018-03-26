<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-07
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Right extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_RIGHT, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'eid_status'    => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'object_eid'    => $params['object_eid'] ?? '',
                'access_type'   => $params['access_type'] ?? '',
                'access_code'   => $params['access_code'] ?? '',
                'actions'       => $params['actions'] ?? '',
                'owned_by'      => $params['owned_by'] ?? '',
                'created_by'    => $params['created_by'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_acl', $process_arr) === false)
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
                'eid_status'  => array('type' => 'string', 'required' => false),
                'object_eid'  => array('type' => 'string', 'required' => false),
                'access_type' => array('type' => 'string', 'required' => false),
                'access_code' => array('type' => 'string', 'required' => false),
                'actions'     => array('type' => 'string', 'required' => false),
                'owned_by'    => array('type' => 'string', 'required' => false),
                'created_by'  => array('type' => 'string', 'required' => false)
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
            $db->update('tbl_acl', $process_arr, 'eid = ' . $db->quote($eid));
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
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'object_eid');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $rows = array();
        try
        {
            $query = "select * from tbl_acl where ($filter_expr) order by id";
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
                              'eid_type'    => \Model::TYPE_RIGHT,
                              'eid_status'  => $row['eid_status'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'actions'     => $row['actions'],
                              'owned_by'    => $row['owned_by'],
                              'created_by'  => $row['created_by'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
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

    public function setStatus(string $eid, string $status) : bool
    {
        return $this->set($eid, array('eid_status' => $status));
    }

    public function getOwner(string $eid) : string
    {
        // TODO: add constant for owner undefined and/or public; use this instead of '' in return result

        if (!\Flexio\Base\Eid::isValid($eid))
            return '';

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_acl where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_acl where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function getInfoFromObjectEid(string $object_eid) : array
    {
        // get the all available authentication information for the object_eid
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tac.eid as eid,
                                      tac.eid_status as eid_status,
                                      tac.object_eid as object_eid,
                                      tac.access_type as access_type,
                                      tac.access_code as access_code,
                                      tac.actions as actions,
                                      tac.owned_by as owned_by,
                                      tac.created_by as created_by,
                                      tac.created as created,
                                      tac.updated as updated
                              from tbl_acl tac
                              where tac.object_eid = ?
                             ", $object_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => \Model::TYPE_RIGHT,
                              'eid_status'  => $row['eid_status'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'actions'     => $row['actions'],
                              'owned_by'    => $row['owned_by'],
                              'created_by'  => $row['created_by'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
