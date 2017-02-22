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


class TokenModel extends ModelBase
{
    public function create($params)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        $db->beginTransaction();
        try
        {
            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_TOKEN, $params);
            if ($eid === false)
                throw new \Exception();

            $timestamp = \System::getTimestamp();
            $process_arr = array(
                'eid'           => $eid,
                'user_eid'      => isset_or($params['user_eid'], ''),
                'access_code'   => isset_or($params['access_code'], ''),
                'secret_code'   => isset_or($params['secret_code'], ''),
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
            return $this->fail(Model::ERROR_CREATE_FAILED, _('Could not add authentication codes'));
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
            return $this->fail(Model::ERROR_DELETE_FAILED, _('Could not delete authentication code'));
        }
    }

    public function get($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tau.user_eid as user_eid,
                                     tau.access_code as access_code,
                                     tau.secret_code as secret_code,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_token tau on tob.eid = tau.eid
                              where tob.eid = ?
                             ", $eid);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => $row['eid_type'],
                     'user_eid'    => $row['user_eid'],
                     'access_code' => $row['access_code'],
                     'secret_code' => $row['secret_code'],
                     'eid_status'  => $row['eid_status'],
                     'created'     => \Util::formatDate($row['created']),
                     'updated'     => \Util::formatDate($row['updated']));
    }

    public function getInfoFromAccessCode($code)
    {
        // get the authentication information from the access code

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tau.user_eid as user_eid,
                                     tau.access_code as access_code,
                                     tau.secret_code as secret_code,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_token tau on tob.eid = tau.eid
                              where tau.access_code = ?
                             ", $code);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'         => $row['eid'],
                     'eid_type'    => $row['eid_type'],
                     'user_eid'    => $row['user_eid'],
                     'access_code' => $row['access_code'],
                     'secret_code' => $row['secret_code'],
                     'eid_status'  => $row['eid_status'],
                     'created'     => \Util::formatDate($row['created']),
                     'updated'     => \Util::formatDate($row['updated']));
    }

    public function getInfoFromUserEid($user_eid)
    {
        // get the all available authentication information for the user_eid

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        $rows = $db->fetchAll("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tau.user_eid as user_eid,
                                     tau.access_code as access_code,
                                     tau.secret_code as secret_code,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              inner join tbl_token tau on tob.eid = tau.eid
                              where tau.user_eid = ?
                              order by tob.created
                             ", $user_eid);

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'         => $row['eid'],
                              'eid_type'    => $row['eid_type'],
                              'user_eid'    => $row['user_eid'],
                              'access_code' => $row['access_code'],
                              'secret_code' => $row['secret_code'],
                              'eid_status'  => $row['eid_status'],
                              'created'     => \Util::formatDate($row['created']),
                              'updated'     => \Util::formatDate($row['updated']));
        }

        return $output;
    }
}
