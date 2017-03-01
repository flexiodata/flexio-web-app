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


namespace Flexio\Api;


class System
{
    public static function login($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        // try to log in to the system
        $error_message = _('Authentication failed'); // default error message
        $result = \Flexio\System\System::login($params['username'], $params['password'], $error_message);

        if (!$result)
        {
            sleep(1);
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER, $error_message);
        }

        return true;
    }

    public static function logout($params, $request)
    {
        // validation placeholder; no parameters are used
        if (($params = $request->getValidator()->check($params, array(
            ))) === false)
            return $request->getValidator()->fail();

        \Flexio\System\System::clearLoginIdentity();
        @session_destroy();
        @setcookie('FXSESSID', '', time()-86400, '/');
        return true;
    }

    public static function signupcheck($params, $request)
    {
        // checks to see if a username is available
        if (($params = $request->getValidator()->check($params, array(
                'full_name' => array('type' => 'string', 'required' => false),
                'user_name' => array('type' => 'string', 'required' => false),
                'email' => array('type' => 'string', 'required' => false),
                'password' => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        // return result
        $result = array();

        if (isset($params['full_name']))
        {
            // TODO: perform any name validation?
            $result['full_name'] = array();
            $result['full_name']['valid'] = true;
            $result['full_name']['message'] = '';
        }

        if (isset($params['user_name']))
        {
            $username = $params['user_name'];

            $result['user_name'] = array();
            $result['user_name']['valid'] = true;
            $result['user_name']['message'] = '';

            // check if the username isn't a valid identifier
            if (!\Flexio\System\Identifier::isValid($username))
            {
                $result['user_name']['valid'] = false;
                $result['user_name']['message'] = _('A username must be lowercase, start with a letter, and contain between 3 and 39 alphanumeric/underscore/hyphen characters');
            }

            // check if the username is already taken
            $user = \Flexio\Object\User::load($username);
            if ($user !== false)
            {
                $result['user_name']['valid'] = false;
                $result['user_name']['message'] = _('This username is already taken.');
            }
        }

        if (isset($params['email']))
        {
            $email = $params['email'];

            $result['email'] = array();
            $result['email']['valid'] = true;
            $result['email']['message'] = '';

            // check if the email is invalid
            if (!\Flexio\Services\Email::isValid($email))
            {
                $result['email']['valid'] = false;
                $result['email']['message'] = _('This email address must be formatted correctly.');
            }

            // check if the email is already taken
            $user = \Flexio\Object\User::load($email);
            if ($user !== false)
            {
                $result['email']['valid'] = false;
                $result['email']['message'] = _('This email address is already taken.');
            }
        }

        if (isset($params['password']))
        {
            $password = $params['password'];

            $result['password'] = array();
            $result['password']['valid'] = true;
            $result['password']['message'] = '';

            // check the password
            if (!\Flexio\System\Util::isValidPassword($password))
            {
                $result['password']['valid'] = false;
                $result['password']['message'] = _('A password must have at least 8 characters');
            }
        }

        return $result;
    }

    public static function configuration()
    {
        if (!IS_TESTING())
            return;

        return self::checkServerSettings();
    }

    private static function checkServerSettings()
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
        //if (\Flexio\System\Util::isPlatformLinux() && !$val)
        //    $messages[] = 'php imagick library not installed; please install php5-imagick';

        $val = file_exists(\Flexio\System\Util::getBinaryPath('php'));
        if (!$val)
            $messages[] = 'cannot find php command line executable. On Linux, install php5-cli. On Windows, make sure $g_config->dir_home is set.';

        if (\Flexio\System\Util::isPlatformWindows() && !class_exists("COM", false))
            $messages[] = 'please enable extension=php_com_dotnet.dll in php.ini';

        if (\Flexio\System\Util::isPlatformLinux())
        {
            // make sure certain debian/ubuntu packages are installed
        }

        return $messages;
    }

    private static function convertToNumber($size_str)
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
