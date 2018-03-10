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
        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_TOKEN, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'eid_status'    => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'user_eid'      => $params['user_eid'] ?? '',
                'access_code'   => $params['access_code'] ?? '',
                'owned_by'      => $params['owned_by'] ?? '',
                'created_by'    => $params['created_by'] ?? '',
                'created'       => $timestamp,
                'updated'       => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_token', $process_arr) === false)
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
        return $this->setStatus($eid, \Model::STATUS_DELETED);
    }

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tau.eid as eid,
                                         '".\Model::TYPE_TOKEN."' as eid_type,
                                         tau.eid_status as eid_status,
                                         tau.user_eid as user_eid,
                                         tau.access_code as access_code,
                                         tau.secret_code as secret_code,
                                         tau.owned_by as owned_by,
                                         tau.created_by as created_by,
                                         tau.created as created,
                                         tau.updated as updated
                                from tbl_token tau
                                where tau.eid = ?
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
                     'eid_status'  => $row['eid_status'],
                     'user_eid'    => $row['user_eid'],
                     'access_code' => $row['access_code'],
                     'owned_by'    => $row['owned_by'],
                     'created_by'  => $row['created_by'],
                     'created'     => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function setStatus(string $eid, string $status) : bool
    {
        // note: it's possible to set the status through the \Model::set()
        // function on the model, but this provides a lightweight alternative
        // that isn't restricted (right now, changes through \Model::set() are
        // only applied for items that aren't deleted)

        // make sure the status is set to a valid value
        if (!\Model::isValidStatus($status))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // basic check to avoid needless work
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            // set the updated timestamp so it'll stay in sync with whatever
            // object is being edited
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid_status'    => $status,
                'updated'       => $timestamp
            );
            $db->update('tbl_token', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return true; // established object exists, which is enough for returning true
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
                                     '".\Model::TYPE_TOKEN."' as eid_type,
                                     tau.eid_status as eid_status,
                                     tau.user_eid as user_eid,
                                     tau.access_code as access_code,
                                     tau.secret_code as secret_code,
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
                     'eid_type'    => $row['eid_type'],
                     'eid_status'  => $row['eid_status'],
                     'user_eid'    => $row['user_eid'],
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
                                      '".\Model::TYPE_TOKEN."' as eid_type,
                                      tau.eid_status as eid_status,
                                      tau.user_eid as user_eid,
                                      tau.access_code as access_code,
                                      tau.secret_code as secret_code,
                                      tau.owned_by as owned_by,
                                      tau.created_by as created_by,
                                      tau.created as created,
                                      tau.updated as updated
                              from tbl_token tau
                              where tau.user_eid = ?
                              order by tau.created
                             ", $user_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'eid_status'  => $row['eid_status'],
                              'user_eid'    => $row['user_eid'],
                              'access_code' => $row['access_code'],
                              'owned_by'    => $row['owned_by'],
                              'created_by'  => $row['created_by'],
                              'created'     => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'     => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
