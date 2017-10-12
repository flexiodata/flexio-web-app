<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-19
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Vfs
{
    public static function list(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'q' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $path = $validated_params['q'] ?? '';

        $vfs = new \Flexio\Services\Vfs();
        $result = $vfs->listObjects($path);

        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result;
    }


    public static function get(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $path = $_SERVER['REQUEST_URI'];
        if (substr($path,0,12) != '/api/v1/vfs/')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path,11);

        $vfs = new \Flexio\Services\Vfs();
        $vfs->read($path, function($data) {
            echo $data;
        });
    }

    public static function put(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $path = $_SERVER['REQUEST_URI'];
        if (substr($path,0,12) != '/api/v1/vfs/')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path,11);

        $php_stream_handle = fopen('php://input', 'rb');
        $done = false;

        $vfs = new \Flexio\Services\Vfs();
        $vfs->write($path, function($len) use (&$php_stream_handle, &$done) {
            if ($done)
                return false;
            $buf = fread($php_stream_handle, $len);
            if (strlen($buf) != $len)
                $done = true;
            return $buf;
        });

        fclose($php_stream_handle);

        return array('success' => true);

    }
}
