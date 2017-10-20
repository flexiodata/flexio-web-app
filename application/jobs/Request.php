<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-09-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Request extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];

        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        // note: don't clear out the streams; this job simply adds a new stream

        // get the parameters
        $connection_identifier = $params['connection'] ?? false;
        $method = $params['method'] ?? null;
        $url = $params['url'] ?? '';
        $headers = $params['headers'] ?? array();
        $get_params = $params['params'] ?? array();
        $post_data = $params['data'] ?? '';
        $form_data = $params['formdata'] ?? null;
        $userpwd = $params['userpwd'] ?? null;

/*
        $updated_url = $url;
        $updated_headers = $headers;
        $connection_info_merged = self::mergeInfoIfConnectionUrl($updated_url, $updated_headers);

        if ($connection_info_merged)
        {
            // adjust the header and the headers with the info supplied in the connection
            $url = $updated_url;
            $headers = $updated_headers;
        }
*/

        if ($connection_identifier !== false)
        {
            $connection = \Flexio\Object\Connection::load($connection_identifier);
            if ($connection === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            
            // TODO: rights
            //if ($connection->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            // we have a connection; get the connection info and adjust the url
            $connection_properties = $connection->get();
            $connection_info = $connection_properties['connection_info'] ?? [];

            if (isset($connection_info['url']) && strpos($url, '://') === false)
            {
                $base = $connection_info['url'];
                if (strlen($url) > 0)
                {
                    if (substr($base, -1) != '/')
                        $base .= '/';
                    $url = $base . (substr($url,0,1) == '/' ? substr($url,1) : $url);
                }
                 else
                {
                    $url = $base;
                }
            }

            if ($method === null && isset($connection_info['method']))
            {
                $method = $connection_info['method'];
            }

            if ($userpwd === null && isset($connection_info['auth']) && $connection_info['auth'] == 'basic' && isset($connection_info['username']))
            {
                $userpwd = $connection_info['username'] . ':' . $connection_info['password'];
            }

            if (isset($connection_info['formdata']) && is_array($connection_info['formdata']) && count($connection_info['formdata']) > 0)
            {
                $newformdata = $connection_info['formdata'];
                if ($form_data !== null)
                    $newformdata = array_merge($newformdata, $form_data);
                $form_data = $newformdata;
            }

            $connection_headers = $connection_info['headers'] ?? false;
        }


        if (strlen($url) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter: 'url'");

        $ch = curl_init();

        // configure the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // configure the method
        $method = strtolower($method);
        switch ($method)
        {
            default: // default to get
            case 'get':     curl_setopt($ch, CURLOPT_HTTPGET, true); break;
            //case 'head':    curl_setopt($ch, CURLOPT_HTTPHEAD, true); break;
            //case 'put':     curl_setopt($ch, CURLOPT_HTTPPUT, true); break;
            case 'post':    curl_setopt($ch, CURLOPT_POST, true); break;
            //case 'patch':   curl_setopt($ch, CURLOPT_HTTPPATCH, true); break;
            //case 'delete':  curl_setopt($ch, CURLOPT_HTTPDELETE, true); break;
            //case 'options': curl_setopt($ch, CURLOPT_HTTPOPTIONS, true); break;
        }

        if (isset($userpwd))
        {
            curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
        }

        if ($method == 'post')
        {
            // use `application/x-www-form-urlencoded` instead of `multipart/form-data`
            //$urlencoded_post_data = http_build_query($post_data);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $urlencoded_post_data);

            if (isset($form_data))
            {
                // form data is a php array; php will do the encoding
                curl_setopt($ch, CURLOPT_POSTFIELDS, $form_data);
            }
             else
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }

        if (count($headers) > 0)
        {
            if (\Flexio\Base\Util::isAssociativeArray($headers))
            {
                $h = [];
                foreach ($headers as $k => $v)
                {
                    $h[] = "$k: $v";
                }
                $headers = $h;
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // TODO: for now, configure some defaults; remove defaults; use info provided in header
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  // 30 seconds connection timeout
        curl_setopt($ch, CURLOPT_USERAGENT, 'Flex.io');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


        $outstream = $context->getStdout();

        // make the call and get the result
        $outstream_properties = array(
            'name' => $url,
            'path' => $url,
            'mime_type' => \Flexio\Base\ContentType::MIME_TYPE_STREAM // default
        );
        $outstream->set($outstream_properties);

        // TODO: get header info?
        //curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$streamwriter) {});

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$streamwriter) {
            $length = strlen($data);
            $streamwriter->write($data);
            return $length;
        });

        $result = curl_exec($ch);

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());

        // TODO: get the mime type from the returned info

        // cleanup
        if ($result === false)
        {
            $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // TODO: a call that fails doesn't mean the request job itself failed;
            // do we want to do anything other than pass on the info from the write
            // function?
        }
         else
        {
            curl_close($ch);
        }
    }

    private static function mergeInfoIfConnectionUrl(&$url, &$headers) : bool
    {
        // returns true if url contains a connection; false otherwise

        // if the first part of the URL contains a connection eid, get the
        // base URL and header info from the connection; then add on the
        // rest of the supplied info; otherwise, use the info as-is
        $url_parts = parse_url($url);
        if (is_array($url_parts) === false)
            return false;

        if (array_key_exists('scheme', $url_parts) === true)
            return false;

        if (array_key_exists('path', $url_parts) === false)
            return false;

        $url_path = $url_parts['path'];
        $url_path_parts = explode('/', trim($url_path, '/'));
        $potential_connection_eid = $url_path_parts[0] ?? '';

        $connection = \Flexio\Object\Connection::load($potential_connection_eid);
        if ($connection === false)
            return false;

        // we have a connection; get the connection info and adjust the url
        $connection_properties = $connection->get();
        $connection_info = $connection_properties['connection_info'];

        $connection_url = $connection_info['url'] ?? false;
        $connection_headers = $connection_info['headers'] ?? false;

        if (is_string($connection_url))
        {
            $path = implode('/',array_splice($url_path_parts,1)); // recombine everything else besides the connection
            $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
            $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
            $url_str_to_append = "$path$query$fragment";
            $new_url = \Flexio\Base\Util::appendUrlPath($connection_url, $url_str_to_append);
            $url = $new_url;
        }

        if (is_array($connection_headers))
            $headers = array_merge($connection_headers, $headers);

        return true;
    }


    // job definition info
    const MIME_TYPE = 'flexio.request';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.request",
        "params": {
            "method": "head|get|put|post|patch|delete|options",
            "url": "https://www.flex.io",
            "headers": []
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.request"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
