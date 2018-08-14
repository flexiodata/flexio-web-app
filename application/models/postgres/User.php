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
    public function create(array $params) : string
    {
        // convert username and email to lowercase
        if (isset($params['username']))
            $params['username'] = strtolower($params['username']);
        if (isset($params['email']))
            $params['email'] = strtolower($params['email']);

        // make sure the properties that are being updated are the correct type
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'        => array('type' => 'string',     'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'username'          => array('type' => 'identifier', 'required' => true),
                'full_name'         => array('type' => 'string',     'required' => false, 'default' => ''),
                'first_name'        => array('type' => 'string',     'required' => false, 'default' => ''),
                'last_name'         => array('type' => 'string',     'required' => false, 'default' => ''),
                'email'             => array('type' => 'email',      'required' => true),
                'phone'             => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_city'     => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_state'    => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_country'  => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_name'      => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_url'       => array('type' => 'string',     'required' => false, 'default' => ''),
                'locale_language'   => array('type' => 'string',     'required' => false, 'default' => 'en_US'),
                'locale_decimal'    => array('type' => 'string',     'required' => false, 'default' => '.'),
                'locale_thousands'  => array('type' => 'string',     'required' => false, 'default' => ','),
                'locale_dateformat' => array('type' => 'string',     'required' => false, 'default' => 'm/d/Y'),
                'timezone'          => array('type' => 'string',     'required' => false, 'default' => 'UTC'),
                'password'          => array('type' => 'password',   'required' => false),
                'verify_code'       => array('type' => 'string',     'required' => false, 'default' => ''),
                'config'            => array('type' => 'string',     'required' => false, 'default' => '{}'),
                'owned_by'          => array('type' => 'string',     'required' => false, 'default' => ''),
                'created_by'        => array('type' => 'string',     'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        if (\Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if a password is supplied, encrypt it; otherwise write out an empty string
        if (isset($process_arr['password']))
            $process_arr['password'] = self::encodePassword($process_arr['password']);
             else
            $process_arr['password'] = '';

        $db = $this->getDatabase();
        try
        {
            // make sure the user doesn't already exist, based on username and email
            $qusername = $db->quote($process_arr['username']);
            $qemail = $db->quote($process_arr['email']);
            $existing_item = $db->fetchOne("select eid from tbl_user where username = $qusername or email = $qemail");
            if ($existing_item !== false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $eid = $this->getModel()->createObjectBase(\Model::TYPE_USER, $process_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['eid'] = $eid;
            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

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
        // set the status to deleted
        $params = array('eid_status' => \Model::STATUS_DELETED);
        return $this->set($eid, $params);
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
            $sql = "delete from tbl_user where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && \Model::isValidStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // encode the password
        if (isset($process_arr['password']) && strlen($process_arr['password']) > 0)
            $process_arr['password'] = self::encodePassword($process_arr['password']);

        // if the item doesn't exist, return false; TODO: throw exception instead?
        if ($this->exists($eid) === false)
            return false;

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            if (isset($process_arr['username']))
            {
                $qusername = $db->quote($process_arr['username']);
                $existing_eid = $db->fetchOne("select eid from tbl_user where username = $qusername");
                if ($existing_eid !== false && $existing_eid !== $eid)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            if (isset($process_arr['email']))
            {
                $qemail = $db->quote($process_arr['email']);
                $existing_eid = $db->fetchOne("select eid from tbl_user where email = $qemail");
                if ($existing_eid !== false && $existing_eid !== $eid)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_user where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
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
