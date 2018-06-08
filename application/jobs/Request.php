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

/*
// DESCRIPTION:
{
    "op": "request", // string, required
    "method": ""     // string, required, enum: get|post (TODO: support additional request types)
    "url": "",       // string, required
    "headers": []    // array,
    "params": []     // array (GET parameters; TODO: different name)
    "data": ""       // string (POST parameters; TODO: correct?)
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('type' => 'string',     'required' => true),
        'method'     => array('type' => 'string',     'required' => true),
        'url'        => array('type' => 'string',     'required' => true)
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Request extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $params = $this->getJobParameters();

        $owner_user_eid = $process->getOwner();

        // note: don't clear out the streams; this job simply adds a new stream

        // get the parameters
        $connection = false;
        $connection_identifier = $params['connection'] ?? false;
        $method = $params['method'] ?? null;
        $url = $params['url'] ?? '';
        $headers = $params['headers'] ?? array();
        $get_params = $params['params'] ?? array();
        $post_data = $params['data'] ?? '';
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';



        if ($connection_identifier !== false)
        {
            if (\Flexio\Base\Eid::isValid($connection_identifier) === false)
            {
                $eid_from_identifier = \Flexio\Object\Connection::getEidFromName($owner_user_eid, $connection_identifier);
                $connection_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
            }
            $connection = \Flexio\Object\Connection::load($connection_identifier);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
         else
        {
            try
            {
                $eid_from_identifier = $url;
                if (\Flexio\Base\Eid::isValid($eid_from_identifier) === false)
                {
                    $eid_from_identifier = \Flexio\Object\Connection::getEidFromName($owner_user_eid, $eid_from_identifier);
                    if ($eid_from_identifier === false)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
                }

                $connection = \Flexio\Object\Connection::load($eid_from_identifier);
                if ($connection->getStatus() === \Model::STATUS_DELETED)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

                $url = ''; // use connection info instead
            }
            catch (\Flexio\Base\Exception $e)
            {
                // connection not found
            }

        }

        if ($connection)
        {
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

            if (isset($connection_info['auth']) && $connection_info['auth'] == 'basic' && isset($connection_info['username']))
            {
                $username = $connection_info['username'];
            }

            if (isset($connection_info['auth']) && $connection_info['auth'] == 'basic' && isset($connection_info['password']))
            {
                $password = $connection_info['password'];
            }

            if (isset($connection_info['data']) && is_array($connection_info['data']) && count($connection_info['data']) > 0)
            {
                if (is_array($post_data))
                {
                    $newpostdata = $connection_info['data'];
                    if ($post_data !== null)
                        $newpostdata = array_merge($newpostdata, $post_data);
                    $post_data = $newpostdata;
                }
            }

            $connection_headers = $connection_info['headers'] ?? false;

            if (is_array($connection_headers) && count($connection_headers) > 0)
            {
                if (count($headers) == 0)
                {
                    $headers = $connection_headers;
                }
                else
                {
                    if (!\Flexio\Base\Util::isAssociativeArray($connection_headers) ||
                        !\Flexio\Base\Util::isAssociativeArray($headers))
                    {
                        $new_headers = [];

                        if (\Flexio\Base\Util::isAssociativeArray($connection_headers))
                        {
                            foreach ($connection_headers as $k => $v)
                                $new_headers[] = "$k: $v";
                        }
                         else
                        {
                            $new_headers = $connection_headers;
                        }

                        if (\Flexio\Base\Util::isAssociativeArray($headers))
                        {
                            foreach ($headers as $k => $v)
                                $new_headers[] = "$k: $v";
                        }
                         else
                        {
                            array_push($new_headers, $headers);
                        }

                        $headers = $new_headers;
                    }
                     else
                    {
                        $lowercase_key_lookup = array();
                        foreach ($new_headers as $k => $v)
                        {
                            $new_headers[strtolower($k)] = $k;
                        }

                        foreach ($headers as $k => $v)
                        {
                            $lowercase_key = strtolower($k);
                            if (isset($lowercase_key_lookup[$lowercase_key]))
                            {
                                unset($new_headers[ $lowercase_key_lookup[$lowercase_key] ]);
                            }

                            $new_headers[$k] = $v;
                        }

                        $headers = $new_headers;
                    }
                }
            }
        }


        if (strlen($url) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter: 'url'");

        $ch = curl_init();

        if (is_array($get_params) && count($get_params) > 0)
        {
            if (parse_url($url, PHP_URL_QUERY))
            {
                $url .= '&';
            }
             else
            {
                $url .= '?';
            }

            $url .= http_build_query($get_params);
        }


        // configure the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // configure the method
        if ($method === null)
            $method = 'get';
             else
            $method = strtolower($method);

        switch ($method)
        {
            // if method isn't supplied, it's already set to get; 'default' here
            // is an invalid method
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            case 'get':     curl_setopt($ch, CURLOPT_HTTPGET, true); break;
            //case 'head':    curl_setopt($ch, CURLOPT_HTTPHEAD, true); break;
            //case 'put':     curl_setopt($ch, CURLOPT_HTTPPUT, true); break;
            case 'post':    curl_setopt($ch, CURLOPT_POST, true); break;
            //case 'patch':   curl_setopt($ch, CURLOPT_HTTPPATCH, true); break;
            //case 'delete':  curl_setopt($ch, CURLOPT_HTTPDELETE, true); break;
            //case 'options': curl_setopt($ch, CURLOPT_HTTPOPTIONS, true); break;
        }

        if (strlen($username) > 0 || strlen($password) > 0)
        {
            $userpwd = "$username:$password";
            curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
        }

        if ($method == 'post')
        {
            // use `application/x-www-form-urlencoded` instead of `multipart/form-data`
            //$urlencoded_post_data = http_build_query($post_data);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $urlencoded_post_data);

            $content_type = '';
            foreach ($headers as $k => $v)
            {
                if (strtolower($k) == 'content-type')
                {
                    $content_type = $v;
                    break;
                }
            }
            if ($content_type == 'application/json' && is_array($post_data))
            {
                $post_data = json_encode($post_data);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
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

        $outstream = $process->getStdout();

        // make the call and get the result
        $outstream_properties = array(
           // 'name' => $url,
           // 'path' => $url,
            'mime_type' => \Flexio\Base\ContentType::STREAM // default
        );
        $outstream->set($outstream_properties);

        // TODO: get header info?
        //curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $data) use (&$streamwriter) {});

        $streamwriter = $outstream->getWriter();
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$streamwriter) {
            $length = strlen($data);
            $streamwriter->write($data);
            return $length;
        });

        $result = curl_exec($ch);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        // note: sometimes the content type isn't returned by the request; in this
        // case, look at the content type and get it that way
        if (!isset($content_type) || $content_type === false)
        {
            $outputstream_reader = $outstream->getReader();
            $buffer = $outputstream_reader->read();
            \Flexio\Base\ContentType::getMimeAndContentType($buffer, $content_type, $temp);
        }

        $streamwriter->close();
        $outstream->set(array(
            'size' => $streamwriter->getBytesWritten(),
            'mime_type' => $content_type
        ));

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
}
