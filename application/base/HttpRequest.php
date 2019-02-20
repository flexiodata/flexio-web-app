<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2014-10-16
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class HttpRequest
{
    public static function exec($method, $url, $basicauth, $params = array(), $ssl_version = false)
    {
        $request_method = null;
        switch ($method)
        {
            default:
                return false;

            case 'GET'  : $request_method = CURLOPT_HTTPGET; break;
            case 'POST' : $request_method = CURLOPT_POST;    break;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, $request_method, true);

        if (is_string($basicauth))
            curl_setopt($ch, CURLOPT_USERPWD, $basicauth); // (e.g: "user:password")

        if ($request_method == CURLOPT_POST)
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);




        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));



        // note: per documentation, best to let CURL use the default given
        // known vulnerabilities with SSL2 and SSL3: see CURLOPT_SSLVERSION
        // here: http://php.net/manual/en/function.curl-setopt.php
        if (is_integer($ssl_version))
            curl_setopt($ch, CURLOPT_SSLVERSION, $ssl_version);

        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result)
            return false;

        return $result;
    }
}
