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
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_TOKEN, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'eid_status'    => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'access_code'   => $params['access_code'] ?? '',
                'owned_by'      => $params['owned_by'] ?? '',
                'created_by'    => $params['created_by'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'   => array('type' => 'string', 'required' => false),
                'access_code'  => array('type' => 'string', 'required' => false),
                'owned_by'     => array('type' => 'string', 'required' => false),
                'created_by'   => array('type' => 'string', 'required' => false)
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

        // build the filter
        $filter_expr = 'true';
        if (isset($filter['eid']))
            $filter_expr .= (' and eid = ' . $db->quote($filter['eid']));
        if (isset($filter['eid_status']))
            $filter_expr .= (' and eid_status = ' . $db->quote($filter['eid_status']));
        if (isset($filter['access_code']))
            $filter_expr .= (' and access_code = ' . $db->quote($filter['access_code']));
        if (isset($filter['owned_by']))
            $filter_expr .= (' and owned_by = ' . $db->quote($filter['owned_by']));

        $rows = array();
        try
        {
            $query = "select * from tbl_token where $filter_expr order by created";
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

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_token where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_token where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function getInfoFromAccessCode(string $code) // TODO: add return type
    {
        // get the authentication information from the access code
        $db = $this->getDatabase();
        $row = $db->fetchRow("select tau.eid as eid,
                                     tau.eid_status as eid_status,
                                     tau.access_code as access_code,
                                     tau.owned_by as owned_by,
                                     tau.created_by as created_by,
                                     tau.created as created,
                                     tau.updated as updated
                              from tbl_token tau
                              where tau.access_code = ?
                             ", $code);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => \Model::TYPE_TOKEN,
                     'eid_status'  => $row['eid_status'],
                     'access_code' => $row['access_code'],
                     'owned_by'    => $row['owned_by'],
                     'created_by'  => $row['created_by'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getInfoFromUserEid(string $user_eid) : array
    {
        // get the all available authentication information for the user_eid
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tau.eid as eid,
                                      tau.eid_status as eid_status,
                                      tau.access_code as access_code,
                                      tau.owned_by as owned_by,
                                      tau.created_by as created_by,
                                      tau.created as created,
                                      tau.updated as updated
                              from tbl_token tau
                              where tau.owned_by = ?
                              order by tau.created
                             ", $user_eid);

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
}
