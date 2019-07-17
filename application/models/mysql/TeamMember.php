<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-16
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class TeamMember extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'team_eid'           => array('type' => 'string', 'required' => false, 'default' => ''),
                'member_eid'         => array('type' => 'string', 'required' => false, 'default' => ''),
                'member_status'      => array('type' => 'string', 'required' => false, 'default' => 'A'),
                'member_permissions' => array('type' => 'string', 'required' => false, 'default' => '{}'),
                'created_by' => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        $db = $this->getDatabase();
        try
        {
            // create the object base
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            // add the properties
            if ($db->insert('tbl_team', $process_arr) === false)
                throw new \Exception();

            return $eid;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function delete(string $team_eid, string $member_eid) : bool
    {
        // if the item doesn't exist, return false
        if (!\Flexio\Base\Eid::isValid($team_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($member_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qteam_eid = $db->quote($team_eid);
            $qmember_eid = $db->quote($member_eid);

            $sql = "delete from tbl_team where team_eid = $qteam_eid and member_eid = $qmember_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
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
            $sql = "delete from tbl_team where team_eid = $qowner_eid or member_eid = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set(string $team_eid, string $member_eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'member_status'       => array('type' => 'string', 'required' => false),
                'member_permissions'  => array('type' => 'string', 'required' => false),
                'created_by'          => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $qteam_eid = $db->quote($team_eid);
            $qmember_eid = $db->quote($member_eid);
            $row = $db->fetchRow("select team_eid, member_eid from tbl_team where team_eid = $qteam_eid and member_eid = $qmember_eid");
            if (!$row)
                return false;

            $db->update('tbl_team', $process_arr, "team_eid = $qteam_eid and member_eid = $qmember_eid");
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
        $allowed_items = array('team_eid', 'member_eid', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_team where $filter_expr order by id $limit_expr";
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
            $output[] = array('team_eid'           => $row['team_eid'],
                              'member_eid'         => $row['member_eid'],
                              'member_status'      => $row['member_status'],
                              'member_permissions' => $row['member_permissions'],
                              'created'    => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'    => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function get(string $team_eid, string $member_eid) : array
    {
        if (!\Flexio\Base\Eid::isValid($team_eid) && !\Flexio\Base\Eid::isValid($member_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $filter = array('team_eid' => $eid, 'member_eid' => $eid);
        $rows = $this->list($filter);
        if (count($rows) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        return $rows[0];
    }

    public function getPermissions(string $team_eid, string $member_eid) : ?string
    {
        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $qteam_eid = $db->quote($team_eid);
            $qmember_eid = $db->quote($member_eid);
            $row = $db->fetchRow("select * from tbl_team where team_eid = $qteam_eid and member_eid = $qmember_eid");
            if (!$row)
                return null;

            // if the member status is inactive, return null
            $member_status = $row['member_status'];
            if ($member_status !== 'A')
                return null;

            return $row['member_permissions'];
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}
