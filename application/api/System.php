<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2011-04-27
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class System
{
    public static function about(\Flexio\Api\Request $request) : void
    {
        // return basic information

        $package_info = \Flexio\System\System::getPackageInfo();
        $git_version = \Flexio\System\System::getGitRevision();

        $result = array();
        $result['name'] = $package_info['name'] ?? '';
        $result['version'] = $package_info['version'] ?? '';
        $result['sha'] = $git_version;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function session(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        // TODO: track?

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true), // allow string here to accomodate username/email
                'password' => array('type' => 'string', 'required' => true)  // allow string here to fall through to general error message below
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $username = $validated_post_params['username'];
        $password = $validated_post_params['password'];

        // default error message
        $error_message = _('Invalid username or password.');

        // return a token that can be used to login
        try
        {
            // try to get the user eid from username
            $current_user_eid = \Flexio\Object\User::getEidFromIdentifier($username);
            if ($current_user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
            {
                $error_message = _('Account not verified.  Please verify your account.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // verify the user with their credentials
            if ($current_user->checkPassword($password) === false)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
            }

            $user_info_basic = array();
            $user_info_basic['timestamp'] = \Flexio\Base\Util::getCurrentTimestamp();
            $user_info_basic['eid'] = $current_user_eid;
            $user_info_basic['random'] = \Flexio\Base\Util::generateRandomString(20);

            $user_info_basic = json_encode($user_info_basic);
            $token = \Flexio\Base\Util::encrypt($user_info_basic, $GLOBALS['g_store']->connection_enckey);

            // TODO: make sure encryption succeeds, or return an error

            $token = self::base64_url_encode($token); // make sure token is url safe since it'll be passwed as a query param
            $result = array(
                'token' => $token
            );

            // return the token
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function logintoken(\Flexio\Api\Request $request) : void
    {
        // function for logging in with a token; time-sensitive token is generated from session function

        $post_params = $request->getQueryParams();

        $request->track(\Flexio\Api\Action::TYPE_USER_LOGIN);
        // note: don't track request params since these contain credentials

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'token' => array('type' => 'string', 'required' => true) // token generated from session api call
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $token = $validated_post_params['token'];

        // attempt to decrypt the user info from the token
        $token = self::base64_url_decode($token);
        $user_info_basic = \Flexio\Base\Util::decrypt($token, $GLOBALS['g_store']->connection_enckey);
        if (!$user_info_basic)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $user_info_basic = json_decode($user_info_basic, true);

        if ($user_info_basic === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $current_user_eid = $user_info_basic['eid'] ?? false;
        if ($current_user_eid === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $timestamp = $user_info_basic['timestamp'] ?? false;
        if ($timestamp === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }
        // TODO: check the timestamp to make sure it's within a minute of the previous token


        // default error message
        $error_message = _('Authentication failed.');

        try
        {
            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // set the user session
            \Flexio\System\System::createUserSession($current_user->getEid());

            // return "about" info
            $result = $current_user->get();
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function login(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $request->track(\Flexio\Api\Action::TYPE_USER_LOGIN);
        // note: don't track request params since these contain credentials

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true), // allow string here to accomodate username/email
                'password' => array('type' => 'string', 'required' => true)  // allow string here to fall through to general error message below
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $username = $validated_post_params['username'];
        $password = $validated_post_params['password'];

        // default error message
        $error_message = _('Invalid username or password.');

        try
        {
            // try to get the user eid from username
            $current_user_eid = \Flexio\Object\User::getEidFromIdentifier($username);
            if ($current_user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
            {
                $error_message = _('Account not verified.  Please verify your account.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // verify the user with their credentials
            if ($current_user->checkPassword($password) === false)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
            }

            // set the user session
            \Flexio\System\System::createUserSession($current_user_eid);

            // return "about" info
            $result = $current_user->get();
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function logout(\Flexio\Api\Request $request) : void
    {
        $request->track(\Flexio\Api\Action::TYPE_USER_LOGOUT);

        \Flexio\System\System::destroyUserSession();

        // return empty "about" info
        $result = array();
        $result['eid'] = '';
        $result['eid_type'] = \Model::TYPE_USER;

        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function validate(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        // the input for a validation is an array of objects that each
        // have a key, value and type; only 10 items can be validated at a time
        /*
        [
            {
                "key": "",
                "value": "",
                "type": ""
            },
            {
                "key": "",
                "value": "",
                "type": ""
            }
        ]
        */

        // make sure params is an array
        if (!is_array($post_params))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (count($post_params) === 0 || count($post_params) > 10)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // make sure each of the items in the array has the required params
        foreach ($post_params as $p)
        {
            // checks to see if a username is available
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($p, array(
                    'key'      => array('type' => 'string', 'required' => true),
                    'value'    => array('type' => 'string', 'required' => true),
                    'type'     => array('type' => 'string', 'required' => true),
                    'eid_type' => array('type' => 'string', 'required' => false)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        // build up the return object of results
        $result = array();
        foreach ($post_params as $p)
        {
            $result[] = self::validateObject($p, $requesting_user_eid);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    private static function validateObject(array $params, string $requesting_user_eid = null) : array
    {
        $key = $params['key'];
        $value = $params['value'];
        $type = $params['type'];
        $eid_type = $params['eid_type'] ?? \Model::TYPE_UNDEFINED;

        // make sure the user is logged in for certain kinds of validation checks
        if (\Flexio\Base\Eid::isValid($requesting_user_eid) === false)
        {
            switch ($type)
            {
                case 'task':
                case 'javascript':
                case 'nodejs':
                case 'python':
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
            }
        }

        // continue with the validation checks
        $valid = false;
        $message = '';

        switch ($type)
        {
            case 'identifier':
            case 'username':
                $valid = self::validateIdentifier($type, $value, $message);
                break;

            case 'email':
                $valid = self::validateEmail($type, $value, $message);
                break;

            case 'alias':
                $user = $requesting_user_eid ?? '';
                $valid = self::validateAlias($type, $eid_type, $user, $value, $message);
                break;

            case 'password':
                {
                    if (\Flexio\Base\Password::isValid($value) === false)
                    {
                        // invalid password
                        $valid = false;
                        $message = _('A password must have at least 8 characters with at least 1 number');
                    }
                    else
                    {
                        $valid = true;
                        $message = '';
                    }
                }
                break;

            // python/javascript
            case 'javascript':
            case 'nodejs':
            case 'python':
                {
                    $code = base64_decode($value);
                    $err = \Flexio\Jobs\Execute::checkScript($type, $code);
                    if ($err === true)
                    {
                        $valid = true;
                        $message = '';
                    }
                     else
                    {
                        $valid = false;
                        $message = $err;
                    }
                }
                break;

            // task
            case 'task':
            {
                $task = $value;
                $error_list = array();
                $err = self::validateTask($requesting_user_eid, $task, $error_list);

                if ($err === true)
                {
                    $valid = true;
                    $message = '';
                }
                 else
                {
                    // if we have an error, return the message for the first error
                    $message = '';
                    if (count($error_list) > 0)
                        $message = $error_list[0]['message'];

                    $valid = false;
                    $message = $message;
                }
            }
            break;
        }

        // echo back the key and whether or not it's valid (note: don't echo
        // back the value to minimize transport of values like a password)
        $properties = array();
        $properties['key'] = $key;
        $properties['valid'] = $valid;
        $properties['message'] = $message;

        return $properties;
    }

    private static function validateIdentifier(string $type, string $value, string &$message = '') : bool
    {
        $prefix = 'An identifier';

        switch ($type)
        {
            case 'identifier': $prefix = 'An identifier';  break;
            case 'username':   $prefix = 'A username';     break;
        }

        // check for valid identifier
        if (\Flexio\Base\Identifier::isValid($value, $message, $prefix) === false)
            return false;

        // check if identifier already exists
        if (\Flexio\Object\User::getEidFromUsername($value) !== false)
        {
            switch ($type)
            {
                case 'identifier':
                    $message = _('This identifier is already taken.');
                    break;
                case 'username':
                    $message = _('This username is already taken.');
                    break;
            }

            return false;
        }

        return true;
    }

    private static function validateEmail(string $type, string $value, string &$message = '') : bool
    {
        // check for valid email
        if (\Flexio\Base\Email::isValid($value) === false)
        {
            $message = _('This email address must be formatted correctly.');
            return false;
        }

        // check if email already exists
        if (\Flexio\Object\User::getEidFromEmail($value) !== false)
        {
            $message = _('This email address is already taken.');
            return false;
        }

        return true;
    }

    private static function validateAlias(string $type, string $eid_type, string $user_eid, string $value, string &$message = '') : bool
    {
        try
        {
            $prefix = 'An alias';

            // check for valid identifier
            if (\Flexio\Base\Identifier::isValid($value, $message, $prefix) === false)
                return false;

            // an alias can only be specific for a valid user; if the user doesn't load
            // an exception will be thrown
            $user = \Flexio\Object\User::load($user_eid);

            if (($eid_type == \Model::TYPE_PIPE || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Pipe::getEidFromName($user->getEid(), $value) !== false)
            {
                // identifier already exists
                $message = _('This alias is already taken.');
                return false;
            }

            if (($eid_type == \Model::TYPE_CONNECTION || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Connection::getEidFromName($user->getEid(), $value) !== false)
            {
                // identifier already exists
                $message = _('This alias is already taken.');
                return false;
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            // can't validate against the user (e.g. user doesn't exist)
            return false;
        }

        return true;
    }

    private static function validateTask(string $process_owner_eid, array $task, array &$errors) : bool
    {
        // returns true if the task is valid; false otherwise; sets the errors
        // parameter to an array of errors

        $process = \Flexio\Jobs\Process::create();
        $process->setOwner($process_owner_eid);

        $errors = $process->validate($t);
        if (count($errors) > 0)
            return false;

        return true;
    }

    private static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/=', '._-');
    }

    private static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '._-', '+/='));
    }
}
