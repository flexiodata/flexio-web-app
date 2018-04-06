<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
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
    public static function about(\Flexio\Api\Request $request)
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

    public static function login(\Flexio\Api\Request $request)
    {
        $request->track(\Flexio\Api\Action::TYPE_USER_LOGIN);

        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $username = $validated_post_params['username'];
        $password = $validated_post_params['password'];

        // try to log in to the system
        $error_message = _('Authentication failed'); // default error message
        $result = \Flexio\System\System::login($username, $password, $error_message);

        if (!$result)
        {
            sleep(1);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, $error_message);
        }

        // return "about" info
        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        try
        {
            $current_user = \Flexio\Object\User::load($current_user_eid);
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            $result = $current_user->get();

            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        $result = array();
        $result['eid'] = '';
        $result['eid_type'] = \Model::TYPE_USER;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function logout(\Flexio\Api\Request $request)
    {
        $request->track(\Flexio\Api\Action::TYPE_USER_LOGOUT);

        \Flexio\System\System::clearLoginIdentity();
        @session_destroy();
        @setcookie('FXSESSID', '', time()-86400, '/');

        // return empty "about" info
        $result = array();
        $result['eid'] = '';
        $result['eid_type'] = \Model::TYPE_USER;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function validate(\Flexio\Api\Request $request)
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (count($post_params) === 0 || count($post_params) > 10)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure each of the items in the array has the required params
        foreach ($post_params as $p)
        {
            // checks to see if a username is available
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($p, array(
                    'key' => array('type' => 'string', 'required' => true),
                    'value' => array('type' => 'string', 'required' => true),
                    'type' => array('type' => 'string', 'required' => true),
                    'eid_type' => array('type' => 'string', 'required' => false)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
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
                case 'javascript':
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

            case 'ename':
                $user = $requesting_user_eid ?? '';
                $valid = self::validateEname($type, $eid_type, $user, $value, $message);
                break;

            case 'password':
                {
                    if (\Flexio\Base\Util::isValidPassword($value) === false)
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
                $err = self::validateTask($task, $error_list);

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
        // check for valid identifier
        if (\Flexio\Base\Identifier::isValid($value) === false)
        {
            switch ($type)
            {
                case 'identifier':
                    $message = _('An identifier must be lowercase, start with a letter, and contain between 3 and 80 characters that are either letters, numbers, underscores or hyphens');
                    break;
                case 'username':
                    $message = _('A username must be lowercase, start with a letter, and contain between 3 and 80 characters that are either letters, numbers, underscores or hyphens');
                    break;
            }

            return false;
        }

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
        if (\Flexio\Services\Email::isValid($value) === false)
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

    private static function validateEname(string $type, string $eid_type, string $user_eid, string $value, string &$message = '') : bool
    {
        try
        {
            if (\Flexio\Base\Identifier::isValid($value) === false)
            {
                // invalid identifier
                $valid = false;
                $message = _('An alias must be lowercase, start with a letter, and contain between 3 and 80 characters that are either letters, numbers, underscores or hyphens');
            }

            // enames can only be specific for a valid user; if the user doesn't load
            // an exception will be thrown
            $user = \Flexio\Object\User::load($user_eid);

            if (($eid_type == \Model::TYPE_PIPE || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Pipe::getEidFromName($user->getEid(), $value) !== false)
            {
                // identifier already exists
                $valid = false;
                $message = _('This alias is already taken.');
            }

            if (($eid_type == \Model::TYPE_CONNECTION || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Connection::getEidFromName($user->getEid(), $value) !== false)
            {
                // identifier already exists
                $valid = false;
                $message = _('This alias is already taken.');
            }

        }
        catch (\Flexio\Base\Exception $e)
        {
            // can't validate against the user (e.g. user doesn't exist)
            return false;
        }

        return true;
    }

    private static function validateTask(array $task, array &$errors) : bool
    {
        // returns true if the task is valid; false otherwise; sets the errors
        // parameter to an array of errors

        $errors = \Flexio\Jobs\Process::create()->validate($t);
        if (count($errors) > 0)
            return false;

        return true;
    }
}
