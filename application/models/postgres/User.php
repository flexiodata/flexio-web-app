<?php
/**
 *
 * Copyright (c) 2012, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-01-12
 *
 * @package flexio
 * @subpackage Model
 */


class User extends ModelBase
{
    public function create($params)
    {
        if (!isset($params['user_name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);
        if (!isset($params['email']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        // convert username and email to lowercase
        $params['user_name'] = strtolower($params['user_name']);
        $params['email'] = strtolower($params['email']);

        if (!\Flexio\Base\Identifier::isValid($params['user_name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (!\Flexio\Services\Email::isValid($params['email']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // encode the password
        if (isset($params['password']) && strlen($params['password']) > 0)
            $params['password'] = \Model::encodePassword($params['password']);

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // make sure the user doesn't already exist, based on
            // user_name and email
            $qusername = $db->quote($params['user_name']);
            $qemail = $db->quote($params['email']);

            $eid = $db->fetchOne("select eid from tbl_user where user_name = $qusername or email = $qemail");
            if ($eid !== false)
                throw new \Exception();

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_USER, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'                    => $eid,
                'user_name'              => $params['user_name'] ?? '',
                'description'            => $params['description'] ?? '',
                'full_name'              => $params['full_name'] ?? '',
                'first_name'             => $params['first_name'] ?? '',
                'last_name'              => $params['last_name'] ?? '',
                'email'                  => $params['email'] ?? '',
                'phone'                  => $params['phone'] ?? '',
                'location_city'          => $params['location_city'] ?? '',
                'location_state'         => $params['location_state'] ?? '',
                'location_country'       => $params['location_country'] ?? '',
                'company_name'           => $params['company_name'] ?? '',
                'company_url'            => $params['company_url'] ?? '',
                'locale_language'        => $params['locale_language'] ?? 'en_US',
                'locale_decimal'         => $params['locale_decimal'] ?? '.',
                'locale_thousands'       => $params['locale_thousands'] ?? ',',
                'locale_dateformat'      => $params['locale_dateformat'] ?? 'm/d/Y',
                'timezone'               => $params['timezone'] ?? 'UTC',
                'password'               => $params['password'] ?? '',
                'verify_code'            => $params['verify_code'] ?? '',
                'config'                 => $params['config'] ?? '{}',
                'created'                => $timestamp,
                'updated'                => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_user', $process_arr) === false)
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

    public function delete($eid)
    {
        $db = $this->getDatabase();
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set($eid, $params)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        // encode the password
        if (isset($params['password']) && strlen($params['password']) > 0)
            $params['password'] = \Model::encodePassword($params['password']);

        // convert username and email to lowercase
        if (isset($params['user_name']))
            $params['user_name'] = strtolower($params['user_name']);
        if (isset($params['email']))
            $params['email'] = strtolower($params['email']);

        // if user_name or email is specified, make sure it's not set to null
        if (is_array($params) && array_key_exists('user_name', $params) && !\Flexio\Base\Identifier::isValid($params['user_name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (is_array($params) && array_key_exists('email', $params) && !\Flexio\Services\Email::isValid($params['email']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure the properties that are being updated are the correct type
        if (($process_arr = \Model::check($params, array(
                'user_name'              => array('type' => 'string',  'required' => false),
                'description'            => array('type' => 'string',  'required' => false),
                'full_name'              => array('type' => 'string',  'required' => false),
                'first_name'             => array('type' => 'string',  'required' => false),
                'last_name'              => array('type' => 'string',  'required' => false),
                'email'                  => array('type' => 'string',  'required' => false),
                'phone'                  => array('type' => 'string',  'required' => false),
                'location_city'          => array('type' => 'string',  'required' => false),
                'location_state'         => array('type' => 'string',  'required' => false),
                'location_country'       => array('type' => 'string',  'required' => false),
                'company_name'           => array('type' => 'string',  'required' => false),
                'company_url'            => array('type' => 'string',  'required' => false),
                'locale_language'        => array('type' => 'string',  'required' => false),
                'locale_decimal'         => array('type' => 'string',  'required' => false),
                'locale_thousands'       => array('type' => 'string',  'required' => false),
                'locale_dateformat'      => array('type' => 'string',  'required' => false),
                'timezone'               => array('type' => 'string',  'required' => false),
                'password'               => array('type' => 'string',  'required' => false),
                'verify_code'            => array('type' => 'string',  'required' => false),
                'config'                 => array('type' => 'string',  'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // TODO: make sure we're not changing the user name or email to
            // another one that already exists

            // set the base object properties
            $result = $this->getModel()->setObjectBase($eid, $params);
            if ($result === false)
            {
                // object doesn't exist or is deleted
                $db->commit();
                return false;
            }

            // set the properties
            $db->update('tbl_user', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function get($eid)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tob.eid as eid,
                                        tob.eid_type as eid_type,
                                        tob.ename as ename,
                                        tus.user_name as user_name,
                                        tus.description as description,
                                        tus.full_name as full_name,
                                        tus.first_name as first_name,
                                        tus.last_name as last_name,
                                        tus.email as email,
                                        tus.phone as phone,
                                        tus.location_city as location_city,
                                        tus.location_state as location_state,
                                        tus.location_country as location_country,
                                        tus.company_name as company_name,
                                        tus.company_url as company_url,
                                        tus.locale_language as locale_language,
                                        tus.locale_decimal as locale_decimal,
                                        tus.locale_thousands as locale_thousands,
                                        tus.locale_dateformat as locale_dateformat,
                                        tus.timezone as timezone,
                                        tus.verify_code as verify_code,
                                        tus.config as config,
                                        tob.eid_status as eid_status,
                                        tob.created as created,
                                        tob.updated as updated
                                from tbl_object tob
                                inner join tbl_user tus on tob.eid = tus.eid
                                where tob.eid = ?
                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'                    => $row['eid'],
                     'eid_type'               => $row['eid_type'],
                     'ename'                  => $row['ename'],
                     'user_name'              => $row['user_name'],
                     'description'            => $row['description'],
                     'full_name'              => $row['full_name'],
                     'first_name'             => $row['first_name'],
                     'last_name'              => $row['last_name'],
                     'email'                  => $row['email'],
                     'email_hash'             => md5(strtolower(trim($row['email']))),
                     'phone'                  => $row['phone'],
                     'location_city'          => $row['location_city'],
                     'location_state'         => $row['location_state'],
                     'location_country'       => $row['location_country'],
                     'company_name'           => $row['company_name'],
                     'company_url'            => $row['company_url'],
                     'locale_language'        => $row['locale_language'],
                     'locale_decimal'         => $row['locale_decimal'],
                     'locale_thousands'       => $row['locale_thousands'],
                     'locale_dateformat'      => $row['locale_dateformat'],
                     'timezone'               => $row['timezone'],
                     'verify_code'            => $row['verify_code'],
                     'config'                 => $row['config'],
                     'eid_status'             => $row['eid_status'],
                     'created'                => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'                => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function getUsernameFromEid($eid)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $user_name = $db->fetchOne('select user_name from tbl_user where eid = ?', $eid);
        if ($user_name === false)
            return false;

        return $user_name;
    }

    public function getEmailFromEid($eid)
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $email = $db->fetchOne('select email from tbl_user where eid = ?', $eid);
        if ($email === false)
            return false;

        return $email;
    }

    public function getEidFromIdentifier($identifier)
    {
        // gets the eid from either the user_name or the email

        // identifiers can be a username or an email, so only perform the
        // most basic string check
        if (!is_string($identifier) || strlen($identifier) <= 0)
            return false;

        // the identifier is either the user_name or the email; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where user_name = $qidentifier or email = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function getEidFromUsername($identifier)
    {
        // make sure we have a string
        if (!is_string($identifier) || strlen($identifier) <= 0)
            return false;

        // get the eid; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where user_name = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function getEidFromEmail($identifier)
    {
        // make sure we have a string
        if (!is_string($identifier) || strlen($identifier) <= 0)
            return false;

        // get the eid; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where email = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function checkUserPassword($identifier, $password)
    {
        // identifiers can be a username or an email, so only perform the
        // most basic string check
        if (!is_string($identifier))
            return false;
        if (!is_string($password))
            return false;

        // get the password; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $user_info = $db->fetchRow("select password from tbl_user where user_name = $qidentifier or email = $qidentifier");
        if ($user_info === false)
            return false;

        $hashpw = $user_info['password'];
        return \Model::checkPasswordHash($hashpw, $password);
    }

    public function checkUserPasswordByEid($eid, $password)
    {
        $db = $this->getDatabase();
        $user_info = $db->fetchRow("select password from tbl_user where eid = ?", $eid);
        if ($user_info === false)
            return false;

        $hashpw = $user_info['password'];
        return \Model::checkPasswordHash($hashpw, $password);
    }
}
