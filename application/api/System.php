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
    public static function login(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // try to log in to the system
        $error_message = _('Authentication failed'); // default error message
        $result = \Flexio\System\System::login($params['username'], $params['password'], $error_message);

        if (!$result)
        {
            sleep(1);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, $error_message);
        }

        return true;
    }

    public static function logout(array $params, string $requesting_user_eid = null) : bool
    {
        // validation placeholder; no parameters are used
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        \Flexio\System\System::clearLoginIdentity();
        @session_destroy();
        @setcookie('FXSESSID', '', time()-86400, '/');
        return true;
    }

    public static function validate(array $params, string $requesting_user_eid = null) : array
    {
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
            $result[] = self::validateObject($p['key'], $p['value'], $p['type']);
        }

        // wait 100 milliseconds to prevent large numbers of calls to mine for information
        $wait_interval = 100;
        usleep($wait_interval*1000);

        return $result;
    }

    public static function validateObject(string $key, string $value, string $type) : array
    {
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
        }

        // echo back the key and whether or not it's valid (note: don't echo
        // back the value to minimize transport of values like a password)
        $result = array();
        $result['key'] = $key;
        $result['valid'] = $valid;
        $result['message'] = $message;

        return $result;
    }

    public static function configuration() : array
    {
        if (!IS_TESTING())
            return array();

        return self::checkServerSettings();
    }

    private static function checkServerSettings() : array
    {
        $messages = array();

        $val = self::convertToNumber(ini_get('post_max_size'));
        if ($val < 1048576000)
            $messages[] = 'post_max_size must be 1000M or greater';

        $val = self::convertToNumber(ini_get('upload_max_filesize'));
        if ($val < 1048576000)
            $messages[] = 'upload_max_filesize must be 1000M or greater';

        $val = self::convertToNumber(ini_get('memory_limit'));
        if ($val < 268435456)
            $messages[] = 'memory_limit must be 256M or greater';

        $val = self::convertToNumber(ini_get('max_execution_time'));
        if ($val > 0 && $val < 3600)
            $messages[] = 'max_execution_time must be 3600 or greater.  Current value: ' . $val;

        $val = self::convertToNumber(ini_get('max_input_time'));
        if ($val < 3600)
            $messages[] = 'max_input_time must be 3600 or greater';

        $val = function_exists('mcrypt_get_iv_size');
        if (!$val)
            $messages[] = 'mcrypt library not installed; please install php5-mcrypt';

        $val = function_exists('curl_init');
        if (!$val)
            $messages[] = 'curl library not installed; please install php5-curl';

        $val = function_exists('imagecreatefrompng');
        if (!$val)
            $messages[] = 'php gd library not installed; please install php5-gd';

        $val = class_exists("SQLite3", false);
        if (!$val)
            $messages[] = 'php sqlite library not installed; please install php5-sqlite';

        //$val = class_exists("Imagick", false);
        //if (\Flexio\System\System::isPlatformLinux() && !$val)
        //    $messages[] = 'php imagick library not installed; please install php5-imagick';

        $val = file_exists(\Flexio\System\System::getBinaryPath('php'));
        if (!$val)
            $messages[] = 'cannot find php command line executable. On Linux, install php5-cli. On Windows, make sure $g_config->dir_home is set.';

        if (\Flexio\System\System::isPlatformWindows() && !class_exists("COM", false))
            $messages[] = 'please enable extension=php_com_dotnet.dll in php.ini';

        if (\Flexio\System\System::isPlatformLinux())
        {
            // make sure certain debian/ubuntu packages are installed
        }

        return $messages;
    }

    private static function convertToNumber(string $size_str) : int
    {
        switch (strtoupper(substr($size_str, -1)))
        {
            case 'G': return (int)$size_str * 1073741824;
            case 'M': return (int)$size_str * 1048576;
            case 'K': return (int)$size_str * 1024;
            default:  return (int)$size_str;
        }
    }
}
