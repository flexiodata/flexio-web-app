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


class Teammember extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'member_eid'    => array('type' => 'eid', 'required' => true),
                'member_status' => array('type' => 'string', 'required' => false, 'default' => \Model::TEAM_MEMBER_STATUS_PENDING),
                'rights'        => array('type' => 'string', 'required' => false, 'default' => '[]'),
                'owned_by'      => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'    => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (self::isValidMemberStatus($process_arr['member_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            // create the object base
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            // add the properties
            if ($db->insert('tbl_teammember', $process_arr) === false)
                throw new \Exception();

            // return the eid of the newly added member; follows pattern of
            // returning the eid of the created object, except in this case,
            // the member exists, or was created elsewhere before this function
            // is called
            return $process_arr['member_eid'];
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function delete(string $member_eid, string $owned_by) : bool
    {
        // if the item doesn't exist, return false
        if (!\Flexio\Base\Eid::isValid($member_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($owned_by))
            return false;

        // owners are always members of their own team, so don't allow owners
        // to delete themselves from their own team
        if ($member_eid === $owned_by)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);

        $db = $this->getDatabase();
        try
        {
            $qmember_eid = $db->quote($member_eid);
            $qowned_by = $db->quote($owned_by);

            $sql = "delete from tbl_teammember where member_eid = $qmember_eid and owned_by = $qowned_by";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function purge(string $owned_by) : bool
    {
        // this function deletes rows for a given owner
        if (!\Flexio\Base\Eid::isValid($owned_by))
            return false;

        $db = $this->getDatabase();
        try
        {
            // delete members that are the owner as well as members that are owned
            $qowned_by = $db->quote($owned_by);
            $sql = "delete from tbl_teammember where member_eid = $qowned_by or owned_by = $qowned_by";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }

/*
        // note: in the above implementation, the owners team is deleted and
        // the owner is also removed from all other teams; in the implementation
        // below, the team member remains as a placeholder so they can be
        // reinvited; however, in this situation, a new placeholder user
        // would need to be created with the email address so the user can
        // be reinvited; this may be best implemented outside the model, which
        // is why this implementation is currently not used

        // this function deletes rows for a given owner
        if (!\Flexio\Base\Eid::isValid($owned_by))
            return false;

        $db = $this->getDatabase();
        try
        {
            $rows_affected = 0;
            $qowned_by = $db->quote($owned_by);

            // delete records for the owner
            $sql = "delete from tbl_teammember where owned_by = $qowned_by";
            $count = $db->exec($sql);
            if ($count !== false)
                $rows_affected += $count;

            // if the owner is a member of another project, leave these records and change
            // the member_status flag to the default pending value when the member is first
            // created (\Model::TEAM_MEMBER_STATUS_PENDING) which allows them to be reinvited
            $process_arr = array();
            $process_arr['member_status'] = \Model::TEAM_MEMBER_STATUS_PENDING;
            $count = $db->update('tbl_teammember', $process_arr, "member_eid = $qowned_by");

            // TODO: create a new placeholder user

            if ($count !== false)
                $rows_affected += $count;

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
*/
    }

    public function set(string $member_eid, string $owned_by, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($member_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($owned_by))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'member_status' => array('type' => 'string', 'required' => false),
                'rights'        => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($params['member_status']) && self::isValidMemberStatus($params['member_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $qmember_eid = $db->quote($member_eid);
            $qowned_by= $db->quote($owned_by);
            $row = $db->fetchRow("select member_eid from tbl_teammember where member_eid = $qmember_eid and owned_by = $qowned_by");
            if (!$row)
                return false;

            $db->update('tbl_teammember', $process_arr, "member_eid = $qmember_eid and owned_by = $qowned_by");
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
        $allowed_items = array('owned_by', 'created_by', 'member_eid', 'member_status', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_teammember where $filter_expr order by id $limit_expr";
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
            $output[] = array('member_eid'    => $row['member_eid'],
                              'member_status' => $row['member_status'],
                              'rights'        => $row['rights'],
                              'owned_by'      => $row['owned_by'],
                              'created_by'    => $row['created_by'],
                              'created'       => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'       => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function get(string $member_eid, $owned_by) : array
    {
        if (!\Flexio\Base\Eid::isValid($member_eid) && !\Flexio\Base\Eid::isValid($owned_by))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        try
        {
            $db = $this->getDatabase();
            $qmember_eid = $db->quote($member_eid);
            $qowned_by= $db->quote($owned_by);
            $row = $db->fetchRow("select * from tbl_teammember where member_eid = $qmember_eid and owned_by = $qowned_by");
            if (!$row)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            return array('member_eid'    => $row['member_eid'],
                         'member_status' => $row['member_status'],
                         'rights'        => $row['rights'],
                         'owned_by'      => $row['owned_by'],
                         'created_by'    => $row['created_by'],
                         'created'       => \Flexio\Base\Util::formatDate($row['created']),
                         'updated'       => \Flexio\Base\Util::formatDate($row['updated']));
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
         }
    }

    public function getRights(string $member_eid, string $owned_by) : ?string
    {
        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $qmember_eid = $db->quote($member_eid);
            $qowned_by = $db->quote($owned_by);
            $row = $db->fetchRow("select * from tbl_teammember where member_eid = $qmember_eid and owned_by = $qowned_by");
            if (!$row)
                return null;

            return $row['rights'];
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    private static function isValidMemberStatus(string $status) : bool
    {
        switch ($status)
        {
            case \Model::TEAM_MEMBER_STATUS_UNDEFINED:
            case \Model::TEAM_MEMBER_STATUS_PENDING:
            case \Model::TEAM_MEMBER_STATUS_INACTIVE:
            case \Model::TEAM_MEMBER_STATUS_ACTIVE:
                return true;
        }

        return false;
    }
}
