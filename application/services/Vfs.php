<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-10-04
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Vfs
{
    public function listObjects(string $path = '') : array
    {
        


        $f = array(
            'name' => $file,
            'path' => $full_path,
            'size' => null,
            'modified' => null,
            'is_dir' => ($row['mimeType'] == 'application/vnd.google-apps.folder' ? true : false),
            'root' => 'googledrive'
        );

        // filesystem is always ready
        return true;
    }

    public function getConnectionEname(string $path) : string
    {
        $path = trim($path);
        if (strlen($path) == 0)
            return '';

        $off = ($path[0] == '/' ? 1:0);

        $pos = strpos($path, '/', $off);
        if ($pos === false)
            return substr($path, $off);
                else
            return substr($path, $off, $pos-$off);
    }
}
