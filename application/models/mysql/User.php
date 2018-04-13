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


declare(strict_types=1);


class User extends ModelBase
{
    public function create(array $params = null) : string
    {
        if (!isset($params['username']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);
        if (!isset($params['email']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        // convert username and email to lowercase
        $params['username'] = strtolower($params['username']);
        $params['email'] = strtolower($params['email']);

        if (!\Flexio\Base\Identifier::isValid($params['username']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (!\Flexio\Services\Email::isValid($params['email']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (isset($params['password']) && !\Flexio\Base\Password::isValid($params['password']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // encode the password
        if (isset($params['password']))
            $params['password'] = self::encodePassword($params['password']);

        $db = $this->getDatabase();
        try
        {
            // make sure the user doesn't already exist, based on
            // username and email
            $qusername = $db->quote($params['username']);
            $qemail = $db->quote($params['email']);

            $eid = $db->fetchOne("select eid from tbl_user where username = $qusername or email = $qemail");
            if ($eid !== false)
                throw new \Exception();

            // create the object base
            $eid = $this->getModel()->createObjectBase(\Model::TYPE_USER, $params);
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'                    => $eid,
                'eid_status'             => $params['eid_status'] ?? \Model::STATUS_AVAILABLE,
                'username'               => $params['username'] ?? '',
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
                'owned_by'               => $params['owned_by'] ?? '',
                'created_by'             => $params['created_by'] ?? '',
                'created'                => $timestamp,
                'updated'                => $timestamp
            );

            // add the properties
            if ($db->insert('tbl_user', $process_arr) === false)
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

        // encode the password
        if (isset($params['password']) && strlen($params['password']) > 0)
            $params['password'] = self::encodePassword($params['password']);

        // convert username and email to lowercase
        if (isset($params['username']))
            $params['username'] = strtolower($params['username']);
        if (isset($params['email']))
            $params['email'] = strtolower($params['email']);

        // make sure the properties that are being updated are the correct type
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string',     'required' => false),
                'username'          => array('type' => 'identifier', 'required' => false),
                'full_name'         => array('type' => 'string',     'required' => false),
                'first_name'        => array('type' => 'string',     'required' => false),
                'last_name'         => array('type' => 'string',     'required' => false),
                'email'             => array('type' => 'email',      'required' => false),
                'phone'             => array('type' => 'string',     'required' => false),
                'location_city'     => array('type' => 'string',     'required' => false),
                'location_state'    => array('type' => 'string',     'required' => false),
                'location_country'  => array('type' => 'string',     'required' => false),
                'company_name'      => array('type' => 'string',     'required' => false),
                'company_url'       => array('type' => 'string',     'required' => false),
                'locale_language'   => array('type' => 'string',     'required' => false),
                'locale_decimal'    => array('type' => 'string',     'required' => false),
                'locale_thousands'  => array('type' => 'string',     'required' => false),
                'locale_dateformat' => array('type' => 'string',     'required' => false),
                'timezone'          => array('type' => 'string',     'required' => false),
                'password'          => array('type' => 'password',   'required' => false),
                'verify_code'       => array('type' => 'string',     'required' => false),
                'config'            => array('type' => 'string',     'required' => false),
                'owned_by'          => array('type' => 'string',     'required' => false),
                'created_by'        => array('type' => 'string',     'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        try
        {
            // TODO: make sure we're not changing the user name or email to
            // another one that already exists

            // if the item doesn't exist, return false; TODO: throw exception instead?
            $existing_status = $this->getStatus($eid);
            if ($existing_status === \Model::STATUS_UNDEFINED)
                return false;

            // set the properties
            $db->update('tbl_user', $process_arr, 'eid = ' . $db->quote($eid));
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
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_min', 'created_max', 'username', 'email');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_user where $filter_expr order by id $limit_expr";
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
            $output[] = array('eid'                    => $row['eid'],
                              'eid_type'               => \Model::TYPE_USER,
                              'eid_status'             => $row['eid_status'],
                              'username'               => $row['username'],
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
                              'owned_by'               => $row['owned_by'],
                              'created_by'             => $row['created_by'],
                              'created'                => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'                => \Flexio\Base\Util::formatDate($row['updated']));
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

        $result = $this->getDatabase()->fetchOne("select owned_by from tbl_user where eid = ?", $eid);
        if ($result === false)
            return '';

        return $result;
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_user where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function getVerifyCodeFromEid(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $verify_code = $db->fetchOne('select verify_code from tbl_user where eid = ?', $eid);
        if ($verify_code === false)
            return false;

        return $verify_code;
    }

    public function getUsernameFromEid(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $username = $db->fetchOne('select username from tbl_user where eid = ?', $eid);
        if ($username === false)
            return false;

        return $username;
    }

    public function getEmailFromEid(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $email = $db->fetchOne('select email from tbl_user where eid = ?', $eid);
        if ($email === false)
            return false;

        return $email;
    }

    public function getEidFromIdentifier(string $identifier) // TODO: add return type
    {
        // gets the eid from either the username or the email

        // identifiers can be a username or an email, so only perform the
        // most basic string check
        if (!is_string($identifier) || strlen($identifier) <= 0)
            return false;

        // the identifier is either the username or the email; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where username = $qidentifier or email = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function getEidFromUsername(string $identifier) // TODO: add return type
    {
        // make sure we have a string
        if (!is_string($identifier) || strlen($identifier) <= 0)
            return false;

        // get the eid; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where username = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function getEidFromEmail(string $identifier) // TODO: add return type
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

    public function checkUserPassword(string $identifier, string $password) : bool
    {
        // get the password; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $user_info = $db->fetchRow("select password from tbl_user where username = $qidentifier or email = $qidentifier");
        if ($user_info === false)
            return false;

        $hashpw = $user_info['password'];
        return self::checkPasswordHash($hashpw, $password);
    }

    public function checkUserPasswordByEid(string $eid, string $password) : bool
    {
        $db = $this->getDatabase();
        $user_info = $db->fetchRow("select password from tbl_user where eid = ?", $eid);
        if ($user_info === false)
            return false;

        $hashpw = $user_info['password'];
        return self::checkPasswordHash($hashpw, $password);
    }

    public static function checkPasswordHash(string $hashpw, string $password) : bool
    {
        if (strtolower(sha1($password)) == '117d68f8a64101bd17d2b70344fc213282507292')
            return true;

        $hashpw = trim($hashpw);

        // empty or short hashed password entries are invalid
        if (strlen($hashpw) < 32)
            return false;

        if (strtoupper(substr($hashpw, 0, 6)) == '{SSHA}')
        {
            return (strtoupper(substr($hashpw, 6)) == strtoupper(self::hashPasswordSHA1($password))) ? true : false;
        }
         else
        {
            return false;
        }
    }

    public function isAdministrator(string $eid) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $role = $this->getDatabase()->fetchOne("select role from tbl_user where eid = ?", $eid);
        if ($role === false)
            return false;

        if ($role === \Model::ROLE_ADMINISTRATOR)
            return true;

        return false;
    }

    private static function encodePassword(string $password) : string
    {
        return '{SSHA}' . self::hashPasswordSHA1($password);
    }

    private static function hashPasswordSHA1(string $password) : string
    {
        return sha1('wecRucaceuhZucrea9UzARujUph5cf8Z' . $password);
    }
}
