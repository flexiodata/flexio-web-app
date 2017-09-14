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
    public function run()
    {
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];

        // pass on the streams
        $this->getOutput()->merge($this->getInput());

        // get the parameters
        $method = $params['method'] ?? false;
        $url = $params['url'] ?? false;
        $headers = $params['headers'] ?? array();

        if ($method === false || $url === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $ch = curl_init();

        // configure the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // configure the method
        $method = strtolower($method);
        switch ($method)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            case 'head':    curl_setopt($ch, CURLOPT_HTTPHEAD, true); break;
            case 'get':     curl_setopt($ch, CURLOPT_HTTPGET, true); break;
            case 'put':     curl_setopt($ch, CURLOPT_HTTPPUT, true); break;
            case 'post':    curl_setopt($ch, CURLOPT_HTTPPOST, true); break;
            case 'patch':   curl_setopt($ch, CURLOPT_HTTPPATCH, true); break;
            case 'delete':  curl_setopt($ch, CURLOPT_HTTPDELETE, true); break;
            case 'options': curl_setopt($ch, CURLOPT_HTTPOPTIONS, true); break;
        }

        // TODO: for now, configure some defaults; remove defaults; use info provided in header
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  // 30 seconds connection timeout
        curl_setopt($ch, CURLOPT_USERAGENT, 'Flex.io');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // make the call and get the result
        $outstream_properties = array(
            'name' => $url,
            'path' => $url,
            'mime_type' => \Flexio\Base\ContentType::MIME_TYPE_STREAM // default
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->addStream($outstream);

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


    // job definition info
    const MIME_TYPE = 'flexio.request';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.request",
        "params": {
            "method": "head|get|put|post|patch|delete|options",
            "url": "https://www.flex.io",
            "headers: []
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
