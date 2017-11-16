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
    public static function about(\Flexio\Api\Request $request) : array
    {
        // return basic information

        $package_info = \Flexio\System\System::getPackageInfo();
        $git_version = \Flexio\System\System::getGitRevision();

        $result = array();
        $result['name'] = $package_info['name'] ?? '';
        $result['version'] = $package_info['version'] ?? '';
        $result['sha'] = $git_version;
        return $result;
    }

    public static function login(\Flexio\Api\Request $request) : array
    {
        // note: should return the same as User::about();

        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();

        // try to log in to the system
        $error_message = _('Authentication failed'); // default error message
        $result = \Flexio\System\System::login($validated_params['username'], $validated_params['password'], $error_message);

        if (!$result)
        {
            sleep(1);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, $error_message);
        }

        // return "about" info
        $current_user_eid = \Flexio\System\System::getCurrentUserEid();
        $user = \Flexio\Object\User::load($current_user_eid);
        if ($user === false)
        {
            $properties = array();
            $properties['eid'] = '';
            $properties['eid_type'] = \Model::TYPE_USER;
            return $properties;
        }

        // track the user login
        \Flexio\Object\Action::track(\Flexio\Object\Action::TYPE_SIGNED_IN, $user->getEid(), $user->get());

        // return the user info
        return $user->get();
    }

    public static function logout(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        // validation placeholder; no parameters are used
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        \Flexio\System\System::clearLoginIdentity();
        @session_destroy();
        @setcookie('FXSESSID', '', time()-86400, '/');

        // track the user signout
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user !== false)
            \Flexio\Object\Action::track(\Flexio\Object\Action::TYPE_SIGNED_IN, $user->getEid(), $user->get());

        // return empty "about" info
        $properties = array();
        $properties['eid'] = '';
        $properties['eid_type'] = \Model::TYPE_USER;
        return $properties;
    }

    public static function validate(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
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
        if (!is_array($params))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (count($params) === 0 || count($params) > 10)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure each of the items in the array has the required params
        foreach ($params as $p)
        {
            // checks to see if a username is available
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($p, array(
                    'key' => array('type' => 'string', 'required' => true),
                    'value' => array('type' => 'string', 'required' => true),
                    'type' => array('type' => 'string', 'required' => true)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        // build up the return object of results
        $result = array();
        foreach ($params as $p)
        {
            $result[] = self::validateObject($p['key'], $p['value'], $p['type'], $requesting_user_eid);
        }

        return $result;
    }

    private static function validateObject(string $key, string $value, string $type, string $requesting_user_eid = null) : array
    {
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
            case 'ename':
                {
                    if (\Flexio\Base\Identifier::isValid($value) === false)
                    {
                        // invalid identifier
                        $valid = false;

                        switch ($type)
                        {
                            case 'identifier':
                                $message = _('An identifier must be lowercase, start with a letter, and contain between 3 and 39 alphanumeric/underscore/hyphen characters');
                                break;
                            case 'username':
                                $message = _('A username must be lowercase, start with a letter, and contain between 3 and 39 alphanumeric/underscore/hyphen characters');
                                break;
                            case 'ename':
                                $message = _('An alias must be lowercase, start with a letter, and contain between 3 and 39 alphanumeric/underscore/hyphen characters');
                                break;
                        }
                    }
                    else if (\Flexio\Object\User::load($value) !== false ||
                             \Flexio\Object\Store::load($value) !== false)
                    {
                        // identifier already exists
                        $valid = false;

                        switch ($type)
                        {
                            case 'identifier':
                                $message = _('This identifier is already taken.');
                                break;
                            case 'username':
                                $message = _('This username is already taken.');
                                break;
                            case 'ename':
                                $message = _('This alias is already taken.');
                                break;
                        }
                    }
                    else
                    {
                        $valid = true;
                        $message = '';
                    }
                }
                break;

            case 'email':
                {
                    if (\Flexio\Services\Email::isValid($value) === false)
                    {
                        // invalid identifier
                        $valid = false;
                        $message = _('This email address must be formatted correctly.');
                    }
                    else if (\Flexio\Object\User::load($value) !== false)
                    {
                        // identifier already exists
                        $valid = false;
                        $message = _('This email address is already taken.');
                    }
                    else
                    {
                        $valid = true;
                        $message = '';
                    }
                }
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

            // python/javascript requires an active user to validate
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
        }

        // echo back the key and whether or not it's valid (note: don't echo
        // back the value to minimize transport of values like a password)
        $result = array();
        $result['key'] = $key;
        $result['valid'] = $valid;
        $result['message'] = $message;

        return $result;
    }
}
