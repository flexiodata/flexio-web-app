<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-21
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Action extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_ACTION, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'                 => $eid,
                'eid_status'          => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'action_type'         => $params['action_type'] ?? '',
                'request_ip'          => $params['request_ip'] ?? '',
                'request_user_agent'  => $params['request_user_agent'] ?? '',
                'request_type'        => $params['request_type'] ?? '',
                'request_method'      => $params['request_method'] ?? '',
                'request_route'       => $params['request_route'] ?? '',
                'request_created_by'  => $params['request_created_by'] ?? '',
                'request_created'     => $params['request_created'] ?? null,
                'request_access_code' => $params['request_access_code'] ?? '',
                'request_params'      => $params['request_params'] ?? '{}',
                'target_eid'          => $params['target_eid'] ?? '',
                'target_eid_type'     => $params['target_eid_type'] ?? '',
                'target_owned_by'     => $params['target_owned_by'] ?? '',
                'response_type'       => $params['response_type'] ?? '',
                'response_code'       => $params['response_code'] ?? '',
                'response_params'     => $params['response_params'] ?? '{}',
                'response_created'    => $params['response_created'] ?? null,
                'owned_by'            => $params['owned_by'] ?? '',
                'created_by'          => $params['created_by'] ?? '',
                'created'             => $timestamp,
                'updated'             => $timestamp
            );

            if ($db->insert('tbl_action', $process_arr) === false)
                throw new \Exception();

            return $eid;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'          => array('type' => 'string', 'required' => false),
                'action_type'         => array('type' => 'string', 'required' => false),
                'request_ip'          => array('type' => 'string', 'required' => false),
                'request_user_agent'  => array('type' => 'string', 'required' => false),
                'request_type'        => array('type' => 'string', 'required' => false),
                'request_method'      => array('type' => 'string', 'required' => false),
                'request_route'       => array('type' => 'string', 'required' => false),
                'request_created_by'  => array('type' => 'string', 'required' => false),
                'request_created'     => array('type' => 'date',   'required' => false, 'allow_null' => true),
                'request_access_code' => array('type' => 'string', 'required' => false),
                'request_params'      => array('type' => 'string', 'required' => false),
                'target_eid'          => array('type' => 'string', 'required' => false),
                'target_eid_type'     => array('type' => 'string', 'required' => false),
                'target_owned_by'     => array('type' => 'string', 'required' => false),
                'response_type'       => array('type' => 'string', 'required' => false),
                'response_code'       => array('type' => 'string', 'required' => false),
                'response_params'     => array('type' => 'string', 'required' => false),
                'response_created'    => array('type' => 'date',   'required' => false, 'allow_null' => true),
                'owned_by'            => array('type' => 'string', 'required' => false),
                'created_by'          => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $row = $db->fetchRow("select eid as eid from tbl_action where eid = ?", $eid);
            if (!$row)
                return false;

            // set the properties
            $db->update('tbl_action', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function delete(string $eid) : bool
    {
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_action where $filter_expr order by id $limit_expr";
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
            $output[] = array('eid'                 => $row['eid'],
                              'eid_type'            => \Model::TYPE_ACTION,
                              'eid_status'          => $row['eid_status'],
                              'action_type'         => $row['action_type'],
                              'request_ip'          => $row['request_ip'],
                              'request_user_agent'  => $row['request_user_agent'],
                              'request_type'        => $row['request_type'],
                              'request_method'      => $row['request_method'],
                              'request_route'       => $row['request_route'],
                              'request_created_by'  => $row['request_created_by'],
                              'request_created'     => $row['request_created'],
                              'request_access_code' => $row['request_access_code'],
                              'request_params'      => $row['request_params'],
                              'target_eid'          => $row['target_eid'],
                              'target_eid_type'     => $row['target_eid_type'],
                              'target_owned_by'     => $row['target_owned_by'],
                              'response_type'       => $row['response_type'],
                              'response_code'       => $row['response_code'],
                              'response_params'     => $row['response_params'],
                              'response_created'    => $row['response_created'],
                              'duration'            => \Flexio\Base\Util::formatDateDiff($row['request_created'], $row['response_created']),
                              'owned_by'            => $row['owned_by'],
                              'created_by'          => $row['created_by'],
                              'created'             => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'             => \Flexio\Base\Util::formatDate($row['updated']));
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

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_action where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }
}
