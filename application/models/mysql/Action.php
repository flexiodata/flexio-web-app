<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'          => array('type' => 'string', 'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'action_type'         => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_ip'          => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_user_agent'  => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_source'      => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_type'        => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_method'      => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_route'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_created_by'  => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_created'     => array('type' => 'date',   'required' => false, 'default' => null, 'allow_null' => true),
                'request_access_code' => array('type' => 'string', 'required' => false, 'default' => ''),
                'request_params'      => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'target_eid'          => array('type' => 'string', 'required' => false, 'default' => ''),
                'target_eid_type'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'target_owned_by'     => array('type' => 'string', 'required' => false, 'default' => ''),
                'response_type'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'response_code'       => array('type' => 'string', 'required' => false, 'default' => ''),
                'response_params'     => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'response_created'    => array('type' => 'date',   'required' => false, 'default' => null, 'allow_null' => true),
                'owned_by'            => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'          => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        $db = $this->getDatabase();
        try
        {
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_ACTION, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            if ($db->insert('tbl_action', $process_arr) === false)
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_action where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function update(array $filter, array $params) : bool
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'          => array('type' => 'string', 'required' => false),
                'action_type'         => array('type' => 'string', 'required' => false),
                'request_ip'          => array('type' => 'string', 'required' => false),
                'request_user_agent'  => array('type' => 'string', 'required' => false),
                'request_source'      => array('type' => 'string', 'required' => false),
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        try
        {
            $updates_made = $db->update('tbl_action', $process_arr, $filter_expr);
            return $updates_made;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max');
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
                              'request_source'      => $row['request_source'],
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

    public function purge(string $owner_eid) : bool
    {
        // this function deletes rows for a given owner
        if (!\Flexio\Base\Eid::isValid($owner_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qowner_eid = $db->quote($owner_eid);
            $sql = "delete from tbl_action where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

}
