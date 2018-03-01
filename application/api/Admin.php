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
    public static function addToZipArchive($arhivefilename, $zippedfilename, $inputfilename)
    {
        // create the zip archive if needed and add the temporary file to the zip archive
        $zip = new \ZipArchive();
        $zip->open($arhivefilename, \ZipArchive::CREATE);
        $zip->addFile($inputfilename, $zippedfilename);
        $zip->close();
    }

    public static function echoZipDownload($zip_name)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".$zip_name."\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($zip_name));
        @readfile($zip_name);
    }

    public static function getExtract(\Flexio\Api\Request $request)
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $items = $params['items'] ?? false;

        if (!is_string($items) || $items !== '*') // for now only allow all items to be selected
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $items_selected = array(
            'object' => 'select eid, eid_type, eid_status, ename, created, updated from tbl_object',
            'association' => 'select source_eid, target_eid, association_type, created, updated from tbl_association',
            'user' => 'select eid, user_name, email, description, full_name, first_name, last_name, created, updated from tbl_user',
            'project' => 'select eid, name, description, created, updated from tbl_project',
            'pipe' => 'select eid, name, description, task, input, output, schedule, schedule_status, created, updated from tbl_pipe',
            'connection' => 'select eid, name, description, connection_type, connection_status, expires, created, updated from tbl_connection',
            'process' => 'select eid, parent_eid, process_mode, process_hash, task, input, output, started_by, started, finished, process_info, process_status, cache_used, created, updated from tbl_process',
            'comment' => 'select eid, comment, created, updated from tbl_comment',
            'system' => 'select name, value, created, updated from tbl_system'
        );

        // create the zip archive
        $filezip = \Flexio\Base\File::getTempFilename('zip');

        $db = \Flexio\System\System::getModel()->getDatabase();
        $db->beginTransaction(); // needed to make sure eid generation is safe
        try
        {
            foreach ($items_selected as $name => $query)
            {
                // create a temporary file
                $tmpfile = \Flexio\Base\File::getTempFilename('csv');
                $outstream = fopen($tmpfile, 'w');

                // dump the results to the temp file
                $first = true;
                $result = $db->query($query);
                while ($result && ($row = $result->fetch()))
                {
                    if ($first === true)
                    {
                        $header = array_keys($row);
                        fputcsv($outstream, $header);
                    }

                    fputcsv($outstream, $row);
                    $first = false;
                }
                fclose($outstream);

                // write the results of the temp file to the zip arhive
                // and delete the temporary file
                self::addToZipArchive($filezip, $name . '.csv', $tmpfile);
                unlink($tmpfile);
            }

            $db->commit();
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        // send the zip archive and delete the temporary zip archive
        self::echoZipDownload($filezip);
        unlink($filezip);
    }

    public static function getUserList(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $user_list = \Flexio\System\System::getModel()->user->getUserList();

        $result = array();
        foreach ($user_list as $u)
        {
            $user_info = array();
            $user_info['eid'] = $u['eid'] ?? '';
            $user_info['user_name'] = $u['user_name'] ?? '';
            $user_info['email'] = $u['email'] ?? '';
            $user_info['first_name'] = $u['first_name'] ?? '';
            $user_info['last_name'] = $u['last_name'] ?? '';
            $user_info['created'] = $u['created'] ?? '';

            $result[] = $user_info;
        }

        return $result;
    }

    public static function getUserProcessStats(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stats = \Flexio\System\System::getModel()->process->getUserProcessStats();

        $result = array();
        foreach ($stats as $s)
        {
            $user_info = array();
            $user = \Flexio\Object\User::load($s['user_eid']);
            if ($user !== false)
                $user_info = $user->get();

            $pipe_info = array();
            $pipe = \Flexio\Object\Pipe::load($s['pipe_eid']);
            if ($pipe !== false)
                $pipe_info = $pipe->get();

            $item = array();
            $item['user'] = array();
            $item['user']['eid'] = $user_info['eid'] ?? '';
            $item['user']['eid_type'] = $user_info['eid_type'] ?? '';
            $item['user']['user_name'] = $user_info['user_name'] ?? '';
            $item['user']['first_name'] = $user_info['first_name'] ?? '';
            $item['user']['last_name'] = $user_info['last_name'] ?? '';

            $item['pipe'] = array();
            $item['pipe']['eid'] = $pipe_info['eid'] ?? '';
            $item['pipe']['eid_type'] = $pipe_info['eid_type'] ?? '';
            $item['pipe']['name'] = $pipe_info['name'] ?? 'Anonymous';
            $item['pipe']['description'] = $pipe_info['description'] ?? 'Anonymous Process';

            $item['process_created'] = $s['created'];
            $item['total_count'] = $s['total_count'];
            $item['total_time'] = $s['total_time'];
            $item['average_time'] = $s['average_time'];

            $result[] = $item;
        }

        return $result;
    }

    public static function getPipeProcessStats(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stats = \Flexio\System\System::getModel()->process->getPipeProcessStats();

        $result = array();
        foreach ($stats as $s)
        {
            $pipe = \Flexio\Object\Pipe::load($s['pipe_eid']);
            if ($pipe === false)
                continue;

            $pipe_info = $pipe->get();

            $item = array();
            $item['pipe'] = array();
            $item['pipe']['eid'] = $pipe_info['eid'];
            $item['pipe']['eid_type'] = $pipe_info['eid_type'];
            $item['pipe']['name'] = $pipe_info['name'];
            $item['pipe']['ename'] = $pipe_info['ename'];
            $item['pipe']['description'] = $pipe_info['description'];
            $item['pipe']['owned_by'] = $pipe_info['owned_by'] ?? null;
            $item['process_created'] = $s['created'];
            $item['total_count'] = $s['total_count'];
            $item['total_time'] = $s['total_time'];
            $item['average_time'] = $s['average_time'];

            $result[] = $item;
        }

        return $result;
    }

    public static function getProcessTaskStats(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return \Flexio\System\System::getModel()->process->getProcessTaskStats();
    }

    public static function getConfiguration(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow users from flex.io to get this info
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

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
