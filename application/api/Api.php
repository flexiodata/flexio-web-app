<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2014-09-30
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Api
{
    // TODO: migrate VFS api endpoints over to new user scheme?

    // TODO: figure out how to handle these endpoints:
    // 'POS /users/resendverify'         => '\Flexio\Api\User::resendverify'
    // 'POS /users/activate'             => '\Flexio\Api\User::activate'

    // TODO: rename vfs endpoint to files or storage or something else?

    // TODO: should processes run with pipe owner privileges; what about case where two
    // users are running the same pipe; doesn't seem like each should see the output for
    // the last time it ran for the other; if so, maybe processes should run with privileges
    // of the process owner, which could be the requesting user; we have the following endpoints
    // which would need to be sorted out:
    // 'POS /:userid/pipes/:objeid/processes'        => '\Flexio\Api\Process::create',
    // 'GET /:userid/pipes/:objeid/processes'        => '\Flexio\Api\Pipe::processes',

    // TODO: do we need the stream API, or can we get the content exclusively through VFS

    // TODO: implement tail query parameter


    private static $endpoints = array(

        // PUBLIC ENDPOINTS:

        // system
        'GET /about'                                  => '\Flexio\Api\System::about',
        'POS /validate'                               => '\Flexio\Api\System::validate',
        'POS /login'                                  => '\Flexio\Api\System::login',
        'POS /logout'                                 => '\Flexio\Api\System::logout',
        'POS /signup'                                 => '\Flexio\Api\User::create',
        'POS /forgotpassword'                         => '\Flexio\Api\User::requestpasswordreset',
        'POS /resetpassword'                          => '\Flexio\Api\User::resetpassword',

        // AUTHENTICATED ENDPOINTS:

        // users
        'POS /:userid/account'                        => '\Flexio\Api\User::set',
        'GET /:userid/account'                        => '\Flexio\Api\User::get',
        'DEL /:userid/account'                        => '\Flexio\Api\User::purge',
        'POS /:userid/account/credentials'            => '\Flexio\Api\User::changepassword',

        // authorization
        'GET /:userid/auth/rights'                    => '\Flexio\Api\Right::list',
        'POS /:userid/auth/rights'                    => '\Flexio\Api\Right::create',
        'POS /:userid/auth/rights/:objeid'            => '\Flexio\Api\Right::set',
        'GET /:userid/auth/rights/:objeid'            => '\Flexio\Api\Right::get',
        'DEL /:userid/auth/rights/:objeid'            => '\Flexio\Api\Right::delete',
        'GET /:userid/auth/keys'                      => '\Flexio\Api\Token::list',
        'POS /:userid/auth/keys'                      => '\Flexio\Api\Token::create',
        'GET /:userid/auth/keys/:objeid'              => '\Flexio\Api\Token::get',
        'DEL /:userid/auth/keys/:objeid'              => '\Flexio\Api\Token::delete',

        // actions
        'GET /:userid/actions'                        => '\Flexio\Api\Action::list',
        'GET /:userid/actions/summary'                => '\Flexio\Api\Action::summary',

        // connections
        'POS /:userid/connections'                    => '\Flexio\Api\Connection::create',
        'GET /:userid/connections'                    => '\Flexio\Api\Connection::list',
        'POS /:userid/connections/:objeid'            => '\Flexio\Api\Connection::set',
        'GET /:userid/connections/:objeid'            => '\Flexio\Api\Connection::get',
        'DEL /:userid/connections/:objeid'            => '\Flexio\Api\Connection::delete',
        'POS /:userid/connections/:objeid/connect'    => '\Flexio\Api\Connection::connect',
        'POS /:userid/connections/:objeid/disconnect' => '\Flexio\Api\Connection::disconnect',

        // pipes
        'POS /:userid/pipes'                          => '\Flexio\Api\Pipe::create',
        'GET /:userid/pipes'                          => '\Flexio\Api\Pipe::list',
        'POS /:userid/pipes/:objeid'                  => '\Flexio\Api\Pipe::set',
        'GET /:userid/pipes/:objeid'                  => '\Flexio\Api\Pipe::get',
        'DEL /:userid/pipes/:objeid'                  => '\Flexio\Api\Pipe::delete',
        'POS /:userid/pipes/:objeid/run'              => '\Flexio\Api\Pipe::run',
        'GET /:userid/pipes/:objeid/run'              => '\Flexio\Api\Pipe::run',

        // processes
        'POS /:userid/processes'                      => '\Flexio\Api\Process::create',
        'GET /:userid/processes'                      => '\Flexio\Api\Process::list',
        'GET /:userid/processes/summary'              => '\Flexio\Api\Process::summary',
        'POS /:userid/processes/:objeid'              => '\Flexio\Api\Process::set',
        'GET /:userid/processes/:objeid'              => '\Flexio\Api\Process::get',
        'DEL /:userid/processes/:objeid'              => '\Flexio\Api\Process::delete',
        'GET /:userid/processes/:objeid/log'          => '\Flexio\Api\Process::log',
        'POS /:userid/processes/:objeid/run'          => '\Flexio\Api\Process::run',
        'POS /:userid/processes/:objeid/cancel'       => '\Flexio\Api\Process::cancel',

        // streams
        'GET /:userid/streams/:objeid'                => '\Flexio\Api\Stream::get',
        'GET /:userid/streams/:objeid/content'        => '\Flexio\Api\Stream::content',

        // vfs
        'GET /:userid/vfs/list'                       => '\Flexio\Api\Vfs::list',
        'GET /:userid/vfs/*'                          => '\Flexio\Api\Vfs::get',
        'PUT /:userid/vfs/*'                          => '\Flexio\Api\Vfs::put',

        // INTERNAL ENDPOINTS

        // admin
        'POS /admin/email/run'                        => '\Flexio\Api\Admin::email',
        'POS /admin/cron/run'                         => '\Flexio\Api\Admin::cron',
        'GET /admin/info/system'                      => '\Flexio\Api\Admin::system',
        'GET /admin/info/users'                       => '\Flexio\Api\Admin::users',
        'GET /admin/info/actions'                     => '\Flexio\Api\Admin::actions',
        'GET /admin/info/connections'                 => '\Flexio\Api\Admin::connections',
        'GET /admin/info/pipes'                       => '\Flexio\Api\Admin::pipes',
        'GET /admin/info/processes'                   => '\Flexio\Api\Admin::processes',
        'GET /admin/info/processes/summary'           => '\Flexio\Api\Admin::process_summary',
        'GET /admin/tests/configure'                  => '\Flexio\Tests\Base::configure',
        'GET /admin/tests/run'                        => '\Flexio\Tests\Base::run',

        // test
        'GET /admin/action/test'                      => '\Flexio\Api\Action::test'
    );

    public static function request(\Flexio\System\FrameworkRequest $server_request) : void
    {
        // STEP 1: get basic, cleaned-up, request info
        $request_user_agent = $server_request->getUserAgent();
        $request_ip_address = strtolower($server_request->getIpAddress());
        $request_url = $server_request->getUri(); // leave URL as-is to match param handling
        $request_method = strtoupper($server_request->getMethod());
        $request_timestamp = \Flexio\System\System::getTimestamp();
        $request_header_params = $server_request->getHeaders();
        $request_query_params = $server_request->getQueryParams();
        $request_post_params = $server_request->getPostParams();


        // STEP 2: set basic response headers
        if (IS_DEBUG() && (strpos($_SERVER['HTTP_ORIGIN'] ?? '',"://localhost:") !== false) || GET_HTTP_HOST() == 'localhost')
        {
            header('Access-Control-Allow-Credentials: true'); // allow cookies (may not combine with allow origin: *)
            header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN']??'*'));
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, HEAD');
            header('Access-Control-Max-Age: 1000');
            header('Access-Control-Allow-Headers: authorization, origin, x-csrftoken, content-type, accept'); // note that '*' is not valid for Access-Control-Allow-Headers
        }
        else
        {
            $host = GET_HTTP_HOST();
            if (substr($host, 0, 4) == 'api.' && substr($host, -8) == '.flex.io')
            {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, HEAD');
                header('Access-Control-Allow-Headers: authorization, content-type');
            }
        }

        if ($request_method == 'OPTIONS')
            return;


        // STEP 3: create a request object and set basic request info
        $api_request = \Flexio\Api\Request::create();

        $api_request->setUserAgent($request_user_agent);
        $api_request->setIpAddress($request_ip_address);
        $api_request->setUrl($request_url);
        $api_request->setMethod($request_method);
        $api_request->setRequestCreated($request_timestamp);
        $api_request->setHeaderParams($request_header_params);
        $api_request->setQueryParams($request_query_params);
        $api_request->setPostParams($request_post_params);


        try
        {
            // STEP 4: set the url params
            $request_url_parts = $server_request->getUrlParts();
            $mapped_url_params = self::extractUrlParams($request_url_parts);
            if (!isset($mapped_url_params['apiversion']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

            $api_request->setUrlParams($mapped_url_params);


            // STEP 5: set the requesting user; if we can find a requesting user from
            // the params, set the system session user to that user; if we can't find
            // the requesting user from the request params, then try to get the requesting
            // user from the system session info; if we can't, it's a public request

            $requesting_user_eid = \Flexio\Object\User::MEMBER_PUBLIC; // default
            $requesting_user_token = '';
            $user_eid_from_token = '';

            try
            {
                $requesting_user_token = self::getTokenFromRequestParams($request_header_params, $request_query_params);
                $token_info = \Flexio\System\System::getModel()->token->getInfoFromAccessCode($requesting_user_token);
                if ($token_info)
                {
                    $user = \Flexio\Object\User::load($token_info['owned_by']);
                    if ($user->getStatus() === \Model::STATUS_DELETED)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

                    $user_eid_from_token = $user->getEid();
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            $user_eid_from_session = \Flexio\System\System::getCurrentUserEid();

            if (\Flexio\Base\Eid::isValid($user_eid_from_token) === true)
            {
                \Flexio\System\System::setCurrentUserEid($user_eid_from_token);
                $requesting_user_eid = $user_eid_from_token;
            }
            else
            {
                if (\Flexio\Base\Eid::isValid($user_eid_from_session) === true)
                    $requesting_user_eid = $user_eid_from_session;
            }

            $api_request->setRequestingUser($requesting_user_eid);
            $api_request->setToken($requesting_user_token);


            // STEP 6: if JSON is sent as part of a POST body, set the request posted
            // parameters with the converted JSON; the check for enable_post_data_reading
            // is for calls that 'want' the json payload as their body, such as
            // /pipe/:eid/run and /process/:eid/run
            if ($request_method != 'GET' && ini_get('enable_post_data_reading') == '1')
            {
                $new_post_params = self::getPostContent($request_header_params, 'php://input');
                if (isset($new_post_params))
                    $api_request->setPostParams($new_post_params);
            }
        }
        catch (\Flexio\Base\Exception | \Exception | \Error $e)
        {
            $error = self::createError($e);
            \Flexio\Api\Response::sendError($error);
            return;
        }


        // STEP 7: process the request
        self::processRequest($api_request);
    }

    public static function processRequest(\Flexio\Api\Request $api_request) : void
    {
        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in \Flexio\Jobs\Process

            // find the api implementation function associated with the api request
            $function = self::getApiEndpoint($api_request);
            if (is_callable($function) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

            // invoke the api implementation function
            $function($api_request);
            return;
        }

        try
        {
            // find the api implementation function associated with the api request
            $function = self::getApiEndpoint($api_request);
            if (is_callable($function) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

            // invoke the api implementation function
            $function($api_request);
            return; // success; if not, fall through to try/catch and error handler code
        }
        catch (\Flexio\Base\Exception | \Exception | \Error $e)
        {
            $error = self::createError($e);

            // we have an error; if an action has been set, set the response type and info
            if ($api_request->getActionType() !== \Flexio\Api\Action::TYPE_UNDEFINED)
            {
                $http_error_code = (string)\Flexio\Api\Response::getHttpErrorCode($error['code']);
                $api_request->setResponseCode($http_error_code);
                $api_request->setResponseParams($error);
                $api_request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
                $api_request->track();
            }

            // send the error info
            \Flexio\Api\Response::sendError($error);
        }
    }

    private static function getApiEndpoint(\Flexio\Api\Request $request) : string
    {
        // note: creates an api endpoint string that's used to lookup the appropriate api implementation

        $requesting_user = $request->getRequestingUser();
        $request_method = $request->getMethod();
        $url_params = $request->getUrlParams();

        // the url path may or may not start with an owner, which we'll determine below; for
        // now, see if the first part of the path is an owner, which we'll use below; this
        // allows us to only have to try to identify the owner once
        $user_eid = self::resolveOwnerIdentifier($requesting_user, $url_params['apiparam1']);


        // PATH POSSIBILITY 1: there are no identifiers of any kind; match on the raw path
        $api_params = $url_params;
        $apiendpoint = self::buildApiEndpointString($request_method, $api_params);


        $function = self::$endpoints[$apiendpoint] ?? false;
        if ($function !== false)
            return $function;

        // PATH POSSIBILITY 2: the path starts with an owner identifier, but the rest of the path is fixed
        $api_params = $url_params;
        $api_params['apiparam1'] = $user_eid !== '' ? ':userid' : $api_params['apiparam1'];
        $apiendpoint = self::buildApiEndpointString($request_method, $api_params);

        $function = self::$endpoints[$apiendpoint] ?? false;
        if ($function !== false)
        {
            $request->setOwnerFromUrl($user_eid);
            return $function;
        }

        // PATH POSSIBILITY 3: the path starts with an owner identifier, and there's also an object identifer
        // in the third part of the path
        $api_params = $url_params;
        $object_eid = self::resolveObjectIdentifier($user_eid, $url_params['apiparam2'], $url_params['apiparam3']);
        $api_params['apiparam1'] = $user_eid !== '' ? ':userid' : $api_params['apiparam1'];
        $api_params['apiparam3'] = $object_eid !== '' ? ':objeid' : $api_params['apiparam3'];
        $apiendpoint = self::buildApiEndpointString($request_method, $api_params);

        $function = self::$endpoints[$apiendpoint] ?? false;
        if ($function !== false)
        {
            $request->setOwnerFromUrl($user_eid);
            $request->setObjectFromUrl($object_eid);
            return $function;
        }

        // PATH POSSIBILITY 4: the path starts with an owner identifier, and there's also an object identifer
        // in the fourth part of the path
        $api_params = $url_params;
        $object_eid = self::resolveObjectIdentifier($user_eid, $url_params['apiparam3'], $url_params['apiparam4']);
        $api_params['apiparam1'] = $user_eid !== '' ? ':userid' : $api_params['apiparam1'];
        $api_params['apiparam4'] = $object_eid !== '' ? ':objeid' : $api_params['apiparam4'];
        $apiendpoint = self::buildApiEndpointString($request_method, $api_params);

        $function = self::$endpoints[$apiendpoint] ?? false;
        if ($function !== false)
        {
            $request->setOwnerFromUrl($user_eid);
            $request->setObjectFromUrl($object_eid);
            return $function;
        }

        // PATH POSSIBILITY 5; the path is a vfs path with a path after the vfs prefix
        $api_params = $url_params;
        $api_params['apiparam1'] = $user_eid !== '' ? ':userid' : $api_params['apiparam1'];
        $apiendpoint = self::buildApiEndpointString($request_method, $api_params);

             if (substr($apiendpoint,0,17) === 'GET /:userid/vfs/') $apiendpoint = 'GET /:userid/vfs/*';
        else if (substr($apiendpoint,0,17) === 'PUT /:userid/vfs/') $apiendpoint = 'PUT /:userid/vfs/*';

        $function = self::$endpoints[$apiendpoint] ?? false;
        if ($function !== false)
        {
            $request->setOwnerFromUrl($user_eid);
            return $function;
        }

        // we couldn't find any function
        return '';
    }

    private static function resolveOwnerIdentifier(string $requesting_user, string $identifier) : string
    {
        // if the identifier is an eid, we're done
        if (\Flexio\Base\Eid::isValid($identifier))
            return $identifier;

        // if the identifier is 'me', return the requesting user
        if ($identifier === 'me')
            return $requesting_user;

        // if we don't have an eid identifier, try to load the user eid from
        // the identifier
        $user_eid = \Flexio\Object\User::getEidFromUsername($identifier);
        if ($user_eid !== false)
            return $user_eid;

        // invalid identifier
        return '';
    }

    private static function resolveObjectIdentifier(string $owner, string $type, string $identifier) : string
    {
        // if the identifier is an eid, we're done
        if (\Flexio\Base\Eid::isValid($identifier))
            return $identifier;

        // if we don't have an eid identifier and we have a pipe endpoint, try
        // to load the pipe eid from the identifier
        if ($type === 'pipes')
        {
            $pipe_eid = \Flexio\Object\Pipe::getEidFromName($owner, $identifier);
            if ($pipe_eid !== false)
                return $pipe_eid;
        }

        // if we don't have an eid identifier and we have a connection endpoint, try
        // to load the connection eid from the identifier
        if ($type === 'connections')
        {
            $connection_eid = \Flexio\Object\Connection::getEidFromName($owner, $identifier);
            if ($connection_eid !== false)
                return $connection_eid;
        }

        // invalid identifier
        return '';
    }

    private static function buildApiEndpointString(string $request_method, array $api_params) : string
    {
        $apiendpoint = '';
        switch ($request_method)
        {
            default:
                return ''; // invalid request

            case 'GET':     $apiendpoint .= 'GET '; break;
            case 'POST':    $apiendpoint .= 'POS '; break;
            case 'PUT':     $apiendpoint .= 'PUT '; break;
            case 'DELETE':  $apiendpoint .= 'DEL '; break;
        }

        $apiendpoint .= (strlen($api_params['apiparam1']) > 0 ? ('/' . $api_params['apiparam1']) : '');
        $apiendpoint .= (strlen($api_params['apiparam2']) > 0 ? ('/' . $api_params['apiparam2']) : '');
        $apiendpoint .= (strlen($api_params['apiparam3']) > 0 ? ('/' . $api_params['apiparam3']) : '');
        $apiendpoint .= (strlen($api_params['apiparam4']) > 0 ? ('/' . $api_params['apiparam4']) : '');
        $apiendpoint .= (strlen($api_params['apiparam5']) > 0 ? ('/' . $api_params['apiparam5']) : '');
        $apiendpoint .= (strlen($api_params['apiparam6']) > 0 ? ('/' . $api_params['apiparam6']) : '');

        return $apiendpoint;
    }

    private static function getTokenFromRequestParams(array $header_params, array $query_params) : string
    {
        // AUTHENTICATION TYPE 1: try to get the token from the query params
        if (isset($query_params['flexio_api_key']))
        {
            $access_code = $query_params['flexio_api_key'];
            $access_code = trim($access_code);
            return $access_code;
        }

        // AUTHENTICATION TYPE 2: try to get the token from the header
        $header_params = array_change_key_case($header_params, $case = CASE_LOWER);
        if (isset($header_params['authorization']))
        {
            $auth_header = trim($header_params['authorization']);

            $pos = strpos($auth_header, ' ');
            $auth_type = ($pos === false) ? $auth_header : substr($auth_header, 0, $pos);
            $params_raw = ($pos === false) ? '' : trim(substr($auth_header, $pos+1));

            if ($auth_type == 'Bearer')
            {
                $access_code = trim($params_raw);
                return $access_code;
            }
        }

        return '';
    }

    private static function getPostContent(array $header_params, string $input) : ?array
    {
        // TODO: currently, in some configurations (e.g. the test site but not localhost),
        // two content-type headers are being returned; one of them is null, and the
        // other has the correct value; for now, check both, but we should find out
        // why two are being returned, get it so that only one is being returned, and
        // then convert all keys to lowercase and do a lookup on the lowercase (headers
        // are case insensitive, so there's no guarantee of case in the key, but to
        // convert everything to lowercase, we have to make sure there's only one so
        // we don't overwrite a good value for a given key with a bad one)

        foreach ($header_params as $k => $v)
        {
            $k = strtolower($k);

            // only check for the existence of 'application/json' here instead of doing a
            // straight up string comparison -- the reason for this is that there are cases
            // where the content type looks like this: "application/json;charset=UTF-8"
            if (strcasecmp($k, 'content-type') == 0 && strpos($v, 'application/json') !== false)
            {
                $content = file_get_contents($input);
                if (strlen($content) > 0)
                {
                    $urlencoding_test = substr($content, 0, 3);
                    if ($urlencoding_test == '%7B' || $urlencoding_test == '%7b')
                        $content = rawurldecode($content);

                    $obj = @json_decode($content, true);

                    // note: input might not be parsable if the content type was specified
                    // as 'application/json' but the data that was posted is something else;
                    // in this case, throw a normal invalid syntax response
                    if ($obj === null)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _("Content-Type header is 'application/json' but posted data isn't properly formatted JSON"));

                    return $obj;
                }

                break;
            }
        }

        return null;
    }

    private static function extractUrlParams(array $url_parts) : array
    {
        // API v2 request can currently come from one of two patterns
        // TODO: handle both for now, but remove /api/v2 when appropriate
        // https://api.host.io/v1
        // https://www.host.io/api/v2

        $url_params = array();
        $url_part0 = $url_parts[0] ?? '';
        $url_part1 = $url_parts[1] ?? '';

        if ($url_part0 === 'v1')
        {
            $url_params['apiversion'] = $url_parts[0] ?? '';
            $url_params['apiparam1']  = $url_parts[1] ?? '';
            $url_params['apiparam2']  = $url_parts[2] ?? '';
            $url_params['apiparam3']  = $url_parts[3] ?? '';
            $url_params['apiparam4']  = $url_parts[4] ?? '';
            $url_params['apiparam5']  = $url_parts[5] ?? '';
            $url_params['apiparam6']  = $url_parts[6] ?? '';
        }

        if ($url_part0 === 'api' && $url_part1 === 'v2')
        {
            $url_params['apiversion'] = $url_parts[1] ?? '';
            $url_params['apiparam1']  = $url_parts[2] ?? '';
            $url_params['apiparam2']  = $url_parts[3] ?? '';
            $url_params['apiparam3']  = $url_parts[4] ?? '';
            $url_params['apiparam4']  = $url_parts[5] ?? '';
            $url_params['apiparam5']  = $url_parts[6] ?? '';
            $url_params['apiparam6']  = $url_parts[7] ?? '';
        }

        return $url_params;
    }

    private static function createError($e) : array
    {
        $type = '';
        $code = '';
        $message = '';

        if ($e instanceof \Flexio\Base\Exception)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);

            $type = 'flexio exception';
            $code = $info['code'];
            $message = $info['message'];
        }
        elseif ($e instanceof \Exception)
        {
            $type = 'system exception';
            $code = \Flexio\Base\Error::GENERAL;
        }
        elseif ($e instanceof \Error)
        {
            $type = 'system error';
            $code = \Flexio\Base\Error::GENERAL;
        }

        $error = array();
        $error['code'] = $code;
        $error['message'] = $message;

        if (IS_DEBUG())
        {
            $error['type'] = $type;
            $error['module'] = $e->getFile();
            $error['line'] = $e->getLine();
            $error['debug_message'] = $e->getMessage();
            $error['trace'] = $e->getTraceAsString();
        }

        return $error;
    }
}
