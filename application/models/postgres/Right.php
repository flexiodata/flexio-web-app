<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-30
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
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_TOKEN, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'object_eid'    => $params['object_eid'] ?? '',
                'access_type'   => $params['access_type'] ?? '',
                'access_code'   => $params['access_code'] ?? '',
                'action'        => $params['action'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_acl', $process_arr) === false)
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
            // TODO: zero out any access code?

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
                                         tac.object_eid as object_eid,
                                         tac.access_type as access_type,
                                         tac.access_code as access_code,
                                         tac.action as action,
                                         tob.eid_status as eid_status,
                                         tob.created as created,
                                         tob.updated as updated
                                from tbl_object tob
                                inner join tbl_acl tac on tob.eid = tac.eid
                                where tob.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => $row['eid_type'],
                     'object_eid'  => $row['object_eid'],
                     'access_type' => $row['access_type'],
                     'access_code' => $row['access_code'],
                     'action'      => $row['action'],
                     'eid_status'  => $row['eid_status'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getInfoFromAccessCode(string $code) // TODO: add return type
    {
        // get the rights information from the access code
        $db = $this->getDatabase();
        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tac.object_eid as object_eid,
                                     tac.access_type as access_type,
                                     tac.access_code as access_code,
                                     tac.action as action,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_acl tac on tob.eid = tac.eid
                              where tac.access_code = ?
                             ", $code);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => $row['eid_type'],
                     'object_eid'  => $row['object_eid'],
                     'access_type' => $row['access_type'],
                     'access_code' => $row['access_code'],
                     'action'      => $row['action'],
                     'eid_status'  => $row['eid_status'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getInfoFromObjectEid(string $object_eid) : array
    {
        // get the all available authentication information for the user_eid
        $db = $this->getDatabase();
        $rows = $db->fetchAll("select tob.eid as eid,
                                      tob.eid_type as eid_type,
                                      tac.object_eid as object_eid,
                                      tac.access_type as access_type,
                                      tac.access_code as access_code,
                                      tac.action as action,
                                      tob.eid_status as eid_status,
                                      tob.created as created,
                                      tob.updated as updated
                              from tbl_object tob
                              inner join tbl_acl tac on tob.eid = tac.eid
                              where tac.user_eid = ?
                              order by tob.created
                             ", $user_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'object_eid'  => $row['object_eid'],
                              'access_type' => $row['access_type'],
                              'access_code' => $row['access_code'],
                              'action'      => $row['action'],
                              'eid_status'  => $row['eid_status'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function addRights(string $eid, array $rights) : bool
    {
        // make sure the eid is valid
        if (!\Flexio\Base\Eid::isValid($eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure the rights are in the correct format
        foreach ($rights as $r)
        {
            if (!isset($r['access_type']) || !is_string($r['access_type']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!isset($r['access_code']) || !is_string($r['access_code']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!isset($r['action']) || !is_string($r['action']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // get the existing rights and find out which rights aren't added
            $existing_rights = $this->getRights($eid);
            if ($existing_rights === false)
                $existing_rights = array();

            $arr_comparison_keys = array('access_type', 'access_code', 'action');
            $new_rights = \Flexio\Base\Util::array_new_items($rights, $existing_rights, $arr_comparison_keys);

            // add on the new rights
            foreach ($new_rights as $r)
            {
                $timestamp = \Flexio\System\System::getTimestamp();
                $process_arr = array(
                    'object_eid'    => $eid,
                    'access_type'   => $r['access_type'],
                    'access_code'   => $r['access_code'],
                    'action'        => $r['action'],
                    'created'       => $timestamp,
                    'updated'       => $timestamp
                );

                $this->getDatabase()->insert('tbl_acl', $process_arr);
            }

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function deleteRights(string $eid, array $rights) : bool
    {
        // make sure the eid is valid
        if (!\Flexio\Base\Eid::isValid($eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure the rights are in the correct format
        foreach ($rights as $r)
        {
            if (!isset($r['access_type']) || !is_string($r['access_type']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!isset($r['access_code']) || !is_string($r['access_code']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (!isset($r['action']) || !is_string($r['action']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // delete the rights that match
            foreach ($rights as $r)
            {
                $qeid = $db->quote($eid);
                $qaccess_type = $db->quote($r['access_type']);
                $qaccess_code = $db->quote($r['access_code']);
                $qaction = $db->quote($r['action']);

                $rows = $db->exec("delete from tbl_acl where
                                     object_eid = $qeid and
                                     access_type = $qaccess_type and
                                     access_code = $qaccess_code and
                                     action = $qaction
                                   ");
            }

            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function getRights(string $eid) // TODO: add return type
    {
        // behavior for get is to return the object rights if the eid exists
        // and false if the eid doesn't exist
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        // get the existing rights for the object
        $rows = $this->getDatabase()->fetchAll("select
                                                  tac.object_eid as object_eid,
                                                  tac.access_type as access_type,
                                                  tac.access_code as access_code,
                                                  tac.action as action
                                            from tbl_acl tac
                                            where tac.object_eid = ?
                                            order by tac.id
                                            ", $eid);

        if ($rows === false)
            return false;

        return $rows;
    }
}
