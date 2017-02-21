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


namespace Flexio\Services;


class XferFs
{
    public static function __callStatic($name, $arguments)
    {
        global $g_config;

        if (isset($g_config->s3fs_bucket) && strlen($g_config->s3fs_bucket) > 0)
        {
            return call_user_func_array('\Flexio\Services\XferFsS3::' . $name, $arguments);
        }
         else
        {
            if (!isset($g_config->localfs_base_path))
            {
                $g_config->localfs_base_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'gpxferfs.local';
                if (!is_dir($g_config->localfs_base_path))
                    @mkdir($g_config->localfs_base_path, 0700);
            }
            return call_user_func_array('\Flexio\Services\XferFsLocal::' . $name, $arguments);
        }
    }
}
