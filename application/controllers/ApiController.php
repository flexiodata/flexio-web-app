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


class ApiController extends FxControllerAction
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
        $params = $request->getParams();



        if (IS_DEBUG() && strpos(isset_or($_SERVER['HTTP_ORIGIN'],''),"://localhost:808") !== false)
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
            if (0 == strncmp($request->REQUEST_URI, '/api/v1/processes/', 18) ||
                0 == strncmp($request->REQUEST_URI, '/api/v1/pipes/', 14))
            {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD');
                header('Access-Control-Allow-Headers: authorization');
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
        // allow JSON to be sent directly to the server
        if ($method != 'GET')
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
                        $obj = @json_decode($input, true);

                        if ($obj === null)
                        {
                            header('HTTP/1.1 500 Internal Server Error (JSON parse error)');
                            exit(0);
                        }

                        $params = array_merge($params, $obj);
                    }

                    break;
                }
            }
        }

        // process the request
        \Flexio\Api\Api::request($request, $params);
    }
}
