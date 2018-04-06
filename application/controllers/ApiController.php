<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2009-05-22
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Controllers;


class ApiController extends \Flexio\System\FxControllerAction
{
    public function init()
    {
        parent::init();
        $this->renderRaw();
    }

    public function indexAction()
    {
        // get the request and related info
        $request = $this->getRequest();
        $method = $request->getMethod();
        $query_params = $request->getQueryParams();
        $post_params = $request->getPostParams();


        // see if we can find the api version
        $apiversion = '';
        if ($request->getUrlPathPart(1) == 'v1') // v1 api path:  www.flex.io/api/v1
            $apiversion = 'v1';
        else if ($request->getUrlPathPart(1) == 'v2')  // TODO: we want this to become:  api.flex.io/1; for now we have www.flex.io/api/v2
            $apiversion = 'v2';



        if (IS_DEBUG() && strpos($_SERVER['HTTP_ORIGIN'] ?? '',"://localhost:") !== false)
        {
            header('Access-Control-Allow-Credentials: true'); // allow cookies (may not combine with allow origin: *)
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD');
            header('Access-Control-Max-Age: 1000');
            header('Access-Control-Allow-Headers: authorization, origin, x-csrftoken, content-type, accept'); // note that '*' is not valid for Access-Control-Allow-Headers
            //header('Content-Type: application/json');  // this line absolutely can't be right, so it got commented out
        }
        else
        {
            if (0 == strncmp($request->REQUEST_URI, '/api/v1/connections', 19) ||
                0 == strncmp($request->REQUEST_URI, '/api/v1/processes', 17) ||
                0 == strncmp($request->REQUEST_URI, '/api/v1/pipes', 13) ||
                0 == strncmp($request->REQUEST_URI, '/api/v1/streams', 15))
            {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD');
                header('Access-Control-Allow-Headers: authorization, content-type');
            }
        }

        if ($method == 'OPTIONS')
            return;



/*
        // for OPTIONS method, handle CORS (cross origin)...
        if ($method == 'OPTIONS' && IS_DEBUG())
        {
            // TODO: if we open up our REST API further, we'll need to figure this out...

            // it seems like, for now, to dip our feet into these waters, we'll just test
            // allowing cross-domain access from localhost:8080 so the hot-module-loading
            // dev version works (even different ports are considered cross-domain)
            header('Access-Control-Allow-Credentials: true');
            //header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Origin: http://localhost:8080');
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD');
            header('Access-Control-Max-Age: 1000');
            // note that '*' is not valid for Access-Control-Allow-Headers
            header('Access-Control-Allow-Headers: authorization, origin, x-csrftoken, content-type, accept');
            header('Content-Type: application/json');
            return;
        }

        // handle non-options request
        if (IS_DEBUG())
        {
            // TODO: if we open up our REST API further, we'll need to figure this out...
            header('Access-Control-Allow-Credentials: true');
            //header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Origin: http://localhost:8080');
        }
*/

        // allow JSON to be sent as POST body; the check for enable_post_data_reading
        // is for calls that 'want' the json payload as their body, such as /pipe/:eid/run and
        // /process/:eid/run


        try
        {
            if ($method != 'GET' && ini_get('enable_post_data_reading') == '1')
            {
                $all_headers = getallheaders();

                // TODO: currently, in some configurations (e.g. the test site but not localhost),
                // two content-type headers are being returned; one of them is null, and the
                // other has the correct value; for now, check both, but we should find out
                // why two are being returned, get it so that only one is being returned, and
                // then convert all keys to lowercase and do a lookup on the lowercase (headers
                // are case insensitive, so there's no guarantee of case in the key, but to
                // convert everything to lowercase, we have to make sure there's only one so
                // we don't overwrite a good value for a given key with a bad one)
                foreach ($all_headers as $k => $v)
                {
                    $k = strtolower($k);

                    // only check for the existence of 'application/json' here instead of doing a
                    // straight up string comparison -- the reason for this is that there are cases
                    // where the the content type looks like this: "application/json;charset=UTF-8"
                    if (strcasecmp($k, 'content-type') == 0 && strpos($v, 'application/json') !== false)
                    {
                        $input = file_get_contents('php://input');
                        if (strlen($input) > 0)
                        {
                            $urlencoding_test = substr($input, 0, 3);
                            if ($urlencoding_test == '%7B' || $urlencoding_test == '%7b')
                                $input = rawurldecode($input);

                            $obj = @json_decode($input, true);

                            // note: input might not be parsable if the content type was specified
                            // as 'application/json' but the data that was posted is something else;
                            // in this case, throw a normal invalid parameter response
                            if ($obj === null)
                                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, _("Content-Type header is 'application/json' but posted data isn't properly formatted JSON"));

                            $post_params = $obj; // if we're posting JSON, this will take the place of the post params
                        }

                        break;
                    }
                }
            }

            // process the request
            switch ($apiversion)
            {
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

                case 'v2': \Flexio\Api\Api::request($request, $query_params, $post_params); break;
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);

            $error = array();
            $error['code'] = $info['code'];
            $error['message'] = $info['message'];

            if (IS_DEBUG() !== false)
            {
                $error['type'] = 'flexio exception';
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            \Flexio\Api\Response::sendError($error);
        }
    }
}
