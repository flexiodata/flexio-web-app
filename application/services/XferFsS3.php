<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-01-10
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class XferFsS3
{
    public static function isReady() : bool
    {
        global $g_config;

        try
        {
            $s3 = self::getS3();
            return $s3->doesBucketExist($g_config->s3fs_bucket, false);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public static function createDirectory(string $path) : bool
    {
        global $g_config;

        $full = self::makePath($path);
        $full .= '/';  // s3 uses a trailing slash to indicate a folder
        while (strpos($full, '//') !== false)
            $full = str_replace('//','/', $full);

        try
        {
            $s3 = self::getS3();
            $request = array(
                'Bucket'=> $g_config->s3fs_bucket,
                'Key' => $full,
                'Body'    => 'test'
            );

            $result = $s3->putObject($request);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public static function createFile(string $path, string $import_file) : bool
    {
        global $g_config;

        $full = self::makePath($path);

        $f = fopen($import_file, 'rb');
        if (!$f)
            return false;

        try
        {
            $s3 = self::getS3();
            $request = array(
                'Bucket' => $g_config->s3fs_bucket,
                'Key' => $path,
                'Body'    => $f
            );

            $result = $s3->putObject($request);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public static function deleteFile(string $path) : bool
    {
        // TODO: implement
        return false;
    }

    public static function exportFile(string $path, string $local_path) : bool
    {
        global $g_config;

        $f = fopen($local_path, 'w+b');
        if (!$f)
            return false;

        try
        {
            $s3 = self::getS3();
            $s3->getObject(array(
                'Bucket' => $g_config->s3fs_bucket,
                'Key'    => $path,
                'SaveAs' => $f
            ));
        }
        catch (\Exception $e)
        {
            @fclose($f);
            @unlink($local_path);
            return false;
        }

        return true;
    }

    public static function dumpFile(string $path) : bool
    {
        global $g_config;

        $f = fopen('php://output', 'w+b');
        if (!$f)
            return false;

        try
        {
            $s3 = self::getS3();
            $s3->getObject(array(
                'Bucket' => $g_config->s3fs_bucket,
                'Key'    => $path,
                'SaveAs' => $f
            ));
        }
        catch (\Exception $e)
        {
            @fclose($f);
            return false;
        }

        return true;
    }

    public static function fileSize(string $path) // TODO: add return type
    {
        global $g_config;

        try
        {
            $s3 = self::getS3();

            $full = self::makePath($path);

            return filesize("s3://{$g_config->s3fs_bucket}/{$full}");
        }
        catch (\Exception $e)
        {
            return false;
        }

        return false;
    }

    public static function fileExists(string $path) : bool
    {
        global $g_config;

        $full = self::makePath($path);

        try
        {
            $s3 = self::getS3();
            return $s3->doesObjectExist($g_config->s3fs_bucket, $full);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public static function getDirectories(string $path) : array
    {
        global $g_config;

        $path = trim($path);

        // trim off preceding slash
        if (strlen($path) > 0 && $path[0] == '/')
            $path = substr($path, 1);
        if ($path === false)
            $path = '';

        // add a trailing slash if necessary
        if (strlen($path) > 0 && substr($path, -1) != '/')
            $path .= '/';

        $s3 = self::getS3();

        $arr = array();

        $marker = null;
        while (true)
        {
            $params = array(
                'Bucket' => $g_config->s3fs_bucket,
                'Delimiter' => '/',
                'Prefix' => $path
            );
            if (!is_null($marker))
                $params['Marker'] = $marker;

            $result = $s3->listObjects($params);

            $common_prefixes = $result->get('CommonPrefixes');

            $dir = null;
            if ($common_prefixes)
            {
                foreach ($common_prefixes as $object)
                {
                    $dir = $object['Prefix'];
                    $arr[] = $dir;
                }
            }

            $is_truncated = $result->get('IsTruncated');
            if (!$is_truncated || is_null($dir))
                break;

            $marker = $dir;
        }

        $pathlen = strlen($path);
        foreach ($arr as &$a)
        {
            if (substr($a, -1) == '/')
                $a = substr($a, 0, strlen($a)-1);
            if ($pathlen > 0 && substr($a, 0, $pathlen) == $path)
            {
                $a = substr($a, $pathlen);
                if ($a === false) $a = '';
            }
        }
        unset($a);

        return $arr;
    }

    public static function getObjects(string $path) : array
    {
        global $g_config;

        $path = trim($path);

        // trim off preceding slash
        if (strlen($path) > 0 && $path[0] == '/')
            $path = substr($path, 1);
        if ($path === false)
            $path = '';

        // add a trailing slash if necessary
        if (strlen($path) > 0 && substr($path, -1) != '/')
            $path .= '/';

        $s3 = self::getS3();

        $arr = array();

        $marker = null;
        while (true)
        {
            $params = array(
                'Bucket' => $g_config->s3fs_bucket,
                'Prefix' => $path,
                'Delimiter' => '/'
            );
            if (!is_null($marker))
                $params['Marker'] = $marker;

            $result = $s3->listObjects($params);

            $objects = $result->get('Contents');
            if (!$objects) $objects = array();

            $key = null;
            foreach ($objects as $object)
            {
                $key = $object['Key'];
                if (substr($key, -1) != '/')
                    $arr[] = $key;
            }

            $is_truncated = $result->get('IsTruncated');
            if (!$is_truncated || is_null($key))
                break;

            $marker = $key;
        }

        $pathlen = strlen($path);
        foreach ($arr as &$a)
        {
            if (substr($a, -1) == '/')
                $a = substr($a, 0, strlen($a)-1);
            if ($pathlen > 0 && substr($a, 0, $pathlen) == $path)
            {
                $a = substr($a, $pathlen);
                if ($a === false) $a = '';
            }
        }
        unset($a);

        return $arr;
    }

    // lists both files and subdirectories
    public static function getDirectoryListing(string $path, string $initial_marker = null) : array
    {
        global $g_config;

        $path = trim($path);

        // trim off preceding slash
        if (strlen($path) > 0 && $path[0] == '/')
            $path = substr($path, 1);
        if ($path === false)
            $path = '';

        // add a trailing slash if necessary
        if (strlen($path) > 0 && substr($path, -1) != '/')
            $path .= '/';

        $s3 = self::getS3();

        $arr = array();
        $maxkey = '';


        $marker = null;
        if (!is_null($initial_marker))
        {
            $marker = $path . $initial_marker;
        }

        while (true)
        {
            $params = array(
                'Bucket' => $g_config->s3fs_bucket,
                'Prefix' => $path,
                'Delimiter' => '/'
            );
            if (!is_null($marker))
                $params['Marker'] = $marker;

            $result = $s3->listObjects($params);


            $common_prefixes = $result->get('CommonPrefixes');
            if ($common_prefixes)
            {
                $dir = null;
                foreach ($common_prefixes as $object)
                {
                    $key = $object['Prefix'];
                    $maxkey = max($maxkey, $key);
                    $arr[] = array('name' => $key, 'type' => 'DIR', 'size' => 0);
                }
            }

            $objects = $result->get('Contents');
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $key = $object['Key'];
                    $maxkey = max($maxkey, $key);
                    $arr[] = array('name' => $key, 'type' => 'FILE', 'size' => $object['Size']);
                }
            }



            $is_truncated = $result->get('IsTruncated');
            if (!$is_truncated || is_null($key))
                break;

            $marker = $maxkey;
        }

        $pathlen = strlen($path);
        foreach ($arr as &$a)
        {
            if (substr($a['name'], -1) == '/')
                $a['name']= substr($a['name'], 0, strlen($a['name'])-1);
            if ($pathlen > 0 && substr($a['name'], 0, $pathlen) == $path)
            {
                $a['name'] = substr($a['name'], $pathlen);
                if ($a['name'] === false) $a['name'] = '';
            }
        }
        unset($a);

        return $arr;
    }

    private static function makePath(string $path) : string
    {
        global $g_config;

        $str = '/' . $path;

        while (strpos($str, '//') !== false)
            $str = str_replace('//','/',$str);

        return $str;
    }


    protected static $_aws = null;
    protected static $_s3 = null;
    private static function getS3()
    {
        $aws = self::getAWS();
        if (!$aws)
            return null;

        if (null === self::$_s3)
        {
            self::$_s3 = $aws->get('s3');
            self::$_s3->setSslVerification(false,false);
            self::$_s3->registerStreamWrapper();
        }

        return self::$_s3;
    }

    private static function getAWS()
    {
        //setAutoloaderIgnoreErrors(true);

        //require_once dirname(dirname(__DIR__)) . '/library/aws/aws.phar';

        global $g_config;

        if (null === self::$_aws)
        {
            self::$_aws = \Aws\Common\Aws::factory(array(
               'key' => $g_config->s3fs_access_key,
               'secret' => $g_config->s3fs_secret_key
            ));
        }

        return self::$_aws;
    }
}
