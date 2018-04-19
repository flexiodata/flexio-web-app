<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2017-11-13
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Admin
{
    public static function system(\Flexio\Api\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = self::checkServerSettings();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function users(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();


        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $userlist = \Flexio\Object\User::list($filter);

        $result = array();
        foreach ($userlist as $userobj)
        {
            $u = $userobj->get();

            $user_info = array();
            $user_info['eid'] = $u['eid'] ?? '';
            $user_info['username'] = $u['username'] ?? '';
            $user_info['email'] = $u['email'] ?? '';
            $user_info['first_name'] = $u['first_name'] ?? '';
            $user_info['last_name'] = $u['last_name'] ?? '';
            $user_info['created'] = $u['created'] ?? '';

            $result[] = $user_info;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function actions(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $actions = \Flexio\Object\Action::list($filter);

        $result = array();
        foreach ($actions as $a)
        {
            $result[] = $a->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function connections(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $connections = \Flexio\Object\Connection::list($filter);

        $result = array();
        foreach ($connections as $c)
        {
            $result[] = $c->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function pipes(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $pipes = \Flexio\Object\Pipe::list($filter);

        $result = array();
        foreach ($pipes as $p)
        {
            $result[] = $p->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function processes(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $processes = \Flexio\Object\Process::list($filter);

        $result = array();
        foreach ($processes as $p)
        {
            $result[] = $p->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function process_summary(\Flexio\Api\Request $request)
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'eid',     'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_query_params = $validator->getParams();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $stats = \Flexio\Object\Process::summary($filter);

        $result = array();
        foreach ($stats as $s)
        {
            $item = array();

            $item['user'] = array();
            $item['user']['eid'] = '';
            $item['user']['eid_type'] = '';
            $item['user']['username'] = '';
            $item['user']['first_name'] = '';
            $item['user']['last_name'] = '';

            $item['pipe'] = array();
            $item['pipe']['eid'] = '';
            $item['pipe']['eid_type'] = '';
            $item['pipe']['name'] = 'Anonymous';
            $item['pipe']['description'] = 'Anonymous Process';

            $item['process_created'] = $s['created'];
            $item['total_count'] = $s['total_count'];
            $item['total_time'] = $s['total_time'];
            $item['average_time'] = $s['average_time'];

            $user_info = array();
            try
            {
                $user = \Flexio\Object\User::load($s['user_eid']);
                if ($user->getStatus() !== \Model::STATUS_DELETED)
                {
                    $user_info = $user->get();
                    $item['user']['eid'] = $user_info['eid'];
                    $item['user']['eid_type'] = $user_info['eid_type'];
                    $item['user']['username'] = $user_info['username'];
                    $item['user']['first_name'] = $user_info['first_name'];
                    $item['user']['last_name'] = $user_info['last_name'];
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            $pipe_info = array();
            try
            {
                $pipe = \Flexio\Object\Pipe::load($s['pipe_eid']);
                if ($pipe->getStatus() !== \Model::STATUS_DELETED)
                {
                    $pipe_info = $pipe->get();
                    $item['pipe'] = array();
                    $item['pipe']['eid'] = $pipe_info['eid'];
                    $item['pipe']['eid_type'] = $pipe_info['eid_type'];
                    $item['pipe']['name'] = $pipe_info['name'];
                    $item['pipe']['description'] = $pipe_info['description'];
                }
                else
                {
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            $result[] = $item;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function cron(\Flexio\Api\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // the scheduler script uses UTC as its timezone
        //date_default_timezone_set('UTC');
        //$dt = \Flexio\Base\Util::getDateTimeParts();
        //printf("Scheduler time is: %02d:%02d\n", $dt['hours'], $dt['minutes']);
        $result = array('success' => true);

        $scheduler = new \Flexio\Api\Cron;
        $scheduler->loop(1);
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
