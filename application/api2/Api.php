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
namespace Flexio\Api2;


class Api
{
    // changelog between v1 and v2:
    // * new users are created by posting to signup endpoint:
    //   v1: POST /users => v2: POST /signup
    // * all connections, pipes, processes, streams, vfs, statistics endpoints prefixed with owner;
    //   following are examples:
    //   v1: POS /connections => v2: POS /:userid/connections
    //   v1: DEL /pipes/:eid  => v2: DEL /:userid/pipes/:eid
    // * /users/:eid/tokens/* api endpoints are now /:userid/auth/keys/*:
    //   v1: GET /users/:eid/tokens      => v2: GET /:userid/auth/keys
    //   v1: POS /users/:eid/tokens      => v2: POS /:userid/auth/keys
    //   v1: GET /users/:eid/tokens/:eid => v2: GET /:userid/auth/keys/:eid
    //   v1: DEL /users/:eid/tokens/:eid => v2: DEL /:userid/auth/keys/:eid
    // * /rights/* api endpoints are now /:userid/auth/rights/*:
    //   v1: GET /rights      => v2: GET /:userid/auth/rights
    //   v1: POS /rights      => v2: POS /:userid/auth/rights
    //   v1: POS /rights/:eid => v2: POS /:userid/auth/rights/:eid
    //   v1: GET /rights/:eid => v2: GET /:userid/auth/rights/:eid
    //   v1: DEL /rights/:eid => v2: DEL /:userid/auth/rights/:eid
    // * removed /users/me; this info is given by /:ownerid/account
    //   v1: GET /users/me' => v2: GET /:ownerid/account
    // * endpoints for setting/getting user info are now under /:ownerid/account:
    //   v1: POS /users/:eid => v2: POS /:ownerid/account
    //   v2: GET /users/:eid => v2: GET /:ownerid/account
    // * endpionts for setting/resetting user password info are now under /:ownerid/account/credentials
    //   v1: POS /users/:eid/changepassword => v2: POS /:userid/account/credentials
    //   v1: POS /users/resetpassword       => v2: DEL /:userid/account/credentials
    // * removed internal /process/debug endpiont:
    //   v1: GET /processes/debug => v2: (removed)
    // * renamed /tests/* endpoints to /admin/tests/*:
    //   v1: GET /tests/configure => v2: /admin/tests/configure
    //   v1: GET /tests/run       => v2: /admin/tests/run
    // * removed /admin/extract:
    //   v1: GET /admin/extract => v2: (removed)
    // * removed /connections/:eid/describe; connection items are now retrieved through VFS
    //   v1: GET /connections/:eid/describe => v2: (removed)


    // TODO: migrate VFS api endpoints over to new user scheme?

    // TODO: figure out how to handle these endpoints:
    // 'POS /users/requestpasswordreset' => '\Flexio\Api2\User::requestpasswordreset'
    // 'POS /users/resendverify'         => '\Flexio\Api2\User::resendverify'
    // 'POS /users/activate'             => '\Flexio\Api2\User::activate'

    // TODO: rename vfs endpoint to files?

    // TODO: should processes run with pipe owner privileges; what about case where two
    // users are running the same pipe; doesn't seem like each should see the output for
    // the last time it ran for the other; if so, maybe processes should run with privileges
    // of the process owner, which could be the requesting user; we have the following endpoints
    // which would need to be sorted out:
    // 'POS /:userid/pipes/:objeid/processes'        => '\Flexio\Api2\Process::create',
    // 'GET /:userid/pipes/:objeid/processes'        => '\Flexio\Api2\Pipe::processes',

    // TODO: do we need the stream API, or can we get the content exclusively through VFS


    private static $endpoints = array(

        // PUBLIC ENDPOINTS:

        // system
        'GET /about'                                  => '\Flexio\Api2\System::about',
        'POS /validate'                               => '\Flexio\Api2\System::validate',
        'POS /login'                                  => '\Flexio\Api2\System::login',
        'POS /logout'                                 => '\Flexio\Api2\System::logout',
        'POS /signup'                                 => '\Flexio\Api2\User::create',

        // AUTHENTICATED ENDPOINTS:

        // users
        'POS /:userid/account'                        => '\Flexio\Api2\User::set',
        'GET /:userid/account'                        => '\Flexio\Api2\User::get',
        'POS /:userid/account/credentials'            => '\Flexio\Api2\User::changepassword',
        'DEL /:userid/account/credentials'            => '\Flexio\Api2\User::resetpassword',

        // authorization
        'GET /:userid/auth/rights'                    => '\Flexio\Api2\Right::list',
        'POS /:userid/auth/rights'                    => '\Flexio\Api2\Right::create',
        'POS /:userid/auth/rights/:objeid'            => '\Flexio\Api2\Right::set',
        'GET /:userid/auth/rights/:objeid'            => '\Flexio\Api2\Right::get',
        'DEL /:userid/auth/rights/:objeid'            => '\Flexio\Api2\Right::delete',
        'GET /:userid/auth/keys'                      => '\Flexio\Api2\Token::list',
        'POS /:userid/auth/keys'                      => '\Flexio\Api2\Token::create',
        'GET /:userid/auth/keys/:objeid'              => '\Flexio\Api2\Token::get',
        'DEL /:userid/auth/keys/:objeid'              => '\Flexio\Api2\Token::delete',

        // connections
        'POS /:userid/connections'                    => '\Flexio\Api2\Connection::create',
        'GET /:userid/connections'                    => '\Flexio\Api2\Connection::list',
        'POS /:userid/connections/:objeid'            => '\Flexio\Api2\Connection::set',
        'GET /:userid/connections/:objeid'            => '\Flexio\Api2\Connection::get',
        'DEL /:userid/connections/:objeid'            => '\Flexio\Api2\Connection::delete',
        'POS /:userid/connections/:objeid/connect'    => '\Flexio\Api2\Connection::connect',
        'POS /:userid/connections/:objeid/disconnect' => '\Flexio\Api2\Connection::disconnect',

        // pipes
        'POS /:userid/pipes'                          => '\Flexio\Api2\Pipe::create',
        'GET /:userid/pipes'                          => '\Flexio\Api2\Pipe::list',
        'POS /:userid/pipes/:objeid'                  => '\Flexio\Api2\Pipe::set',
        'GET /:userid/pipes/:objeid'                  => '\Flexio\Api2\Pipe::get',
        'DEL /:userid/pipes/:objeid'                  => '\Flexio\Api2\Pipe::delete',
        'POS /:userid/pipes/:objeid/processes'        => '\Flexio\Api2\Process::create',
        'GET /:userid/pipes/:objeid/processes'        => '\Flexio\Api2\Pipe::processes',
        'POS /:userid/pipes/:objeid/run'              => '\Flexio\Api2\Pipe::run',
        'GET /:userid/pipes/:objeid/run'              => '\Flexio\Api2\Pipe::run',

        // processes
        'POS /:userid/processes'                      => '\Flexio\Api2\Process::create',
        'GET /:userid/processes'                      => '\Flexio\Api2\Process::list',
        'POS /:userid/processes/:objeid'              => '\Flexio\Api2\Process::set',
        'GET /:userid/processes/:objeid'              => '\Flexio\Api2\Process::get',
        'GET /:userid/processes/:objeid/log'          => '\Flexio\Api2\Process::log',
        'POS /:userid/processes/:objeid/run'          => '\Flexio\Api2\Process::run',
        'POS /:userid/processes/:objeid/cancel'       => '\Flexio\Api2\Process::cancel',

        // streams
        'GET /:userid/streams/:objeid'                => '\Flexio\Api2\Stream::get',
        'GET /:userid/streams/:objeid/content'        => '\Flexio\Api2\Stream::content',

        // vfs
        'GET /:userid/vfs/list'                       => '\Flexio\Api2\Vfs::list',
        'GET /:userid/vfs/*'                          => '\Flexio\Api2\Vfs::get',
        'PUT /:userid/vfs/*'                          => '\Flexio\Api2\Vfs::put',

        // statistics
        'GET /:userid/statistics/processes'           => '\Flexio\Api2\Statistics::getUserProcessStats',

        // INTERNAL ENDPOINTS

        // admin
        'GET /admin/list/users'                       => '\Flexio\Api2\Admin::getUserList',
        'GET /admin/statistics/users'                 => '\Flexio\Api2\Admin::getUserProcessStats',
        'GET /admin/configuration'                    => '\Flexio\Api2\Admin::getConfiguration',
        'GET /admin/resetconfig'                      => '\Flexio\Api2\User::resetConfig',    // resets the user configuration

        'GET /admin/tests/configure'                  => '\Flexio\Tests\Base::configure',
        'GET /admin/tests/run'                        => '\Flexio\Tests\Base::run'
    );

    public static function request(\Flexio\System\FrameworkRequest $server_request, array $query_params, array $post_params)
    {
        // get the method and url
        $method = $server_request->getMethod();
        $url = $server_request->REQUEST_URI;

        // get the url parts and map them to the api parameters
        $url_params = array();
        $url_params['apibase']    = $server_request->getUrlPathPart(0) ?? '';
        $url_params['apiversion'] = $server_request->getUrlPathPart(1) ?? '';
        $url_params['apiparam1']  = $server_request->getUrlPathPart(2) ?? '';
        $url_params['apiparam2']  = $server_request->getUrlPathPart(3) ?? '';
        $url_params['apiparam3']  = $server_request->getUrlPathPart(4) ?? '';
        $url_params['apiparam4']  = $server_request->getUrlPathPart(5) ?? '';
        $url_params['apiparam5']  = $server_request->getUrlPathPart(6) ?? '';
        $url_params['apiparam6']  = $server_request->getUrlPathPart(7) ?? '';

        // package the request info
        $api_request = \Flexio\Api2\Request::create();
        $api_request->setMethod($method);
        $api_request->setUrl($url);
        $api_request->setUrlParams($url_params);
        $api_request->setQueryParams($query_params);
        $api_request->setPostParams($post_params);

        $requesting_user_eid = \Flexio\System\System::getCurrentUserEid();
        if (\Flexio\Base\Eid::isValid($requesting_user_eid))
            $api_request->setRequestingUser($requesting_user_eid);
             else
            $api_request->setRequestingUser(\Flexio\Object\User::MEMBER_PUBLIC);

        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in \Flexio\Jobs\Process
            $content = self::processRequest($api_request);
            \Flexio\Api2\Response::sendContent($content);
            return;
        }

        // process the request
        try
        {
            $content = self::processRequest($api_request);
            \Flexio\Api2\Response::sendContent($content);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);

            $error = array();
            $error['code'] = $info['code'];
            $error['message'] = $info['message'];

            if (IS_DEBUG())
            {
                $error['type'] = 'flexio exception';
                $error['module'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            \Flexio\Api2\Response::sendError($error);
        }
        catch (\Exception $e)
        {
            $error = array();
            $error['code'] = \Flexio\Base\Error::GENERAL;
            $error['message'] = '';

            if (IS_DEBUG())
            {
                $error['type'] = 'system exception';
                $error['module'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            \Flexio\Api2\Response::sendError($error);
        }
        catch (\Error $e)
        {
            $error = array();
            $error['code'] = \Flexio\Base\Error::GENERAL;
            $error['message'] = '';

            if (IS_DEBUG())
            {
                $error['type'] = 'system error';
                $error['module'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            \Flexio\Api2\Response::sendError($error);
        }
    }

    private static function processRequest(\Flexio\Api2\Request $request)
    {
        $request_method = $request->getMethod();
        $url_params = $request->getUrlParams();
        $query_params = $request->getQueryParams();
        $post_params = $request->getPostParams();

        if (isset($query_params['testfail']) && strlen($query_params['testfail']) > 0 && (IS_DEBUG() || IS_TESTING()))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, $query_params['testfail']);

        if ($url_params['apibase'] !== 'api' || $url_params['apiversion'] !== 'v2')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

        $function = self::getApiEndpoint($request);
        if (is_callable($function) === true)
            return $function($request);

        // we can't find the specified api endpoint
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);
    }

    private static function getApiEndpoint(\Flexio\Api2\Request $request) : string
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
            return $function;

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
}
