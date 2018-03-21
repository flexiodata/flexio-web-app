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
    public static function request(\Flexio\System\FrameworkRequest $server_request, array $query_params, array $post_params)
    {
        // get the method
        $method = $server_request->getMethod();

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

        $function = self::getApiEndpoint($request_method, $url_params);
        if (is_callable($function) === true)
            return $function($request);

        // we can't find the specified api endpoint
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);
    }

    private static function getApiEndpoint(string $request_method, array $url_params) : string
    {
        // note: creates an api endpoint string that's used to lookup the appropriate api implementation


        // STEP 1: create a shorthand for the method
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

        $apiendpoint .= (strlen($url_params['apiparam1']) > 0 ? ('/' . $url_params['apiparam1']) : '');
        $apiendpoint .= (strlen($url_params['apiparam2']) > 0 ? ('/' . $url_params['apiparam2']) : '');
        $apiendpoint .= (strlen($url_params['apiparam3']) > 0 ? ('/' . $url_params['apiparam3']) : '');
        $apiendpoint .= (strlen($url_params['apiparam4']) > 0 ? ('/' . $url_params['apiparam4']) : '');
        $apiendpoint .= (strlen($url_params['apiparam5']) > 0 ? ('/' . $url_params['apiparam5']) : '');
        $apiendpoint .= (strlen($url_params['apiparam6']) > 0 ? ('/' . $url_params['apiparam6']) : '');

        if (($url_params['apiparam1'] ?? '') == 'vfs')
        {
                 if ($apiendpoint == 'GET /vfs/list')         return '\Flexio\Api2\Vfs::list';
            else if (substr($apiendpoint,0,9) == 'GET /vfs/') return '\Flexio\Api2\Vfs::get';
            else if (substr($apiendpoint,0,9) == 'PUT /vfs/') return '\Flexio\Api2\Vfs::put';
        }

        switch ($apiendpoint)
        {
            default:
                return '';

            // changelog between v1 and v2:
            // * new users are created by posting to signup endpoint:
            //   v1: POST /users => v2: POST /signup
            // * all connections, pipes, processes, streams, statistics endpoints prefixed with owner;
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


            // TODO: migrate VFS api endpoints over to new user scheme?


            // TODO: figure out how to handle these endpoints:
            /*
            case 'POS /users/requestpasswordreset'   : return '\Flexio\Api2\User::requestpasswordreset';
            case 'POS /users/resendverify'           : return '\Flexio\Api2\User::resendverify';
            case 'POS /users/activate'               : return '\Flexio\Api2\User::activate';
            */



            // PUBLIC ENDPOINTS:

            // system
            case 'GET /about'                                : return '\Flexio\Api2\System::about';
            case 'POS /validate'                             : return '\Flexio\Api2\System::validate';
            case 'POS /login'                                : return '\Flexio\Api2\System::login';
            case 'POS /logout'                               : return '\Flexio\Api2\System::logout';
            case 'POS /signup'                               : return '\Flexio\Api2\User::create';



            // AUTHENTICATED ENDPOINTS:

            // users
            case 'POS /:userid/account'                      : return '\Flexio\Api2\User::set';
            case 'GET /:userid/account'                      : return '\Flexio\Api2\User::get';
            case 'POS /:userid/account/credentials'          : return '\Flexio\Api2\User::changepassword';
            case 'DEL /:userid/account/credentials'          : return '\Flexio\Api2\User::resetpassword';

            // authorization
            case 'GET /:userid/auth/rights'                  : return '\Flexio\Api2\Right::list';
            case 'POS /:userid/auth/rights'                  : return '\Flexio\Api2\Right::create';
            case 'POS /:userid/auth/rights/:eid'             : return '\Flexio\Api2\Right::set';
            case 'GET /:userid/auth/rights/:eid'             : return '\Flexio\Api2\Right::get';
            case 'DEL /:userid/auth/rights/:eid'             : return '\Flexio\Api2\Right::delete';
            case 'GET /:userid/auth/keys'                    : return '\Flexio\Api2\Token::list';
            case 'POS /:userid/auth/keys'                    : return '\Flexio\Api2\Token::create';
            case 'GET /:userid/auth/keys/:eid'               : return '\Flexio\Api2\Token::get';
            case 'DEL /:userid/auth/keys/:eid'               : return '\Flexio\Api2\Token::delete';

            // connections
            case 'POS /:userid/connections'                  : return '\Flexio\Api2\Connection::create';
            case 'GET /:userid/connections'                  : return '\Flexio\Api2\Connection::list';
            case 'POS /:userid/connections/:eid'             : return '\Flexio\Api2\Connection::set';
            case 'GET /:userid/connections/:eid'             : return '\Flexio\Api2\Connection::get';
            case 'DEL /:userid/connections/:eid'             : return '\Flexio\Api2\Connection::delete';
            case 'GET /:userid/connections/:eid/describe'    : return '\Flexio\Api2\Connection::describe';
            case 'POS /:userid/connections/:eid/connect'     : return '\Flexio\Api2\Connection::connect';
            case 'POS /:userid/connections/:eid/disconnect'  : return '\Flexio\Api2\Connection::disconnect';

            // pipes
            case 'POS /:userid/pipes'                        : return '\Flexio\Api2\Pipe::create';
            case 'GET /:userid/pipes'                        : return '\Flexio\Api2\Pipe::list';
            case 'POS /:userid/pipes/:eid'                   : return '\Flexio\Api2\Pipe::set';
            case 'GET /:userid/pipes/:eid'                   : return '\Flexio\Api2\Pipe::get';
            case 'DEL /:userid/pipes/:eid'                   : return '\Flexio\Api2\Pipe::delete';
            case 'POS /:userid/pipes/:eid/processes'         : return '\Flexio\Api2\Process::create';
            case 'GET /:userid/pipes/:eid/processes'         : return '\Flexio\Api2\Pipe::processes';
            case 'POS /:userid/pipes/:eid/run'               : return '\Flexio\Api2\Pipe::run';
            case 'GET /:userid/pipes/:eid/run'               : return '\Flexio\Api2\Pipe::run';

            // processes
            case 'POS /:userid/processes'                    : return '\Flexio\Api2\Process::create';
            case 'GET /:userid/processes'                    : return '\Flexio\Api2\Process::list';
            case 'POS /:userid/processes/:eid'               : return '\Flexio\Api2\Process::set';
            case 'GET /:userid/processes/:eid'               : return '\Flexio\Api2\Process::get';
            case 'GET /:userid/processes/:eid/log'           : return '\Flexio\Api2\Process::log';
            case 'POS /:userid/processes/:eid/run'           : return '\Flexio\Api2\Process::run';
            case 'POS /:userid/processes/:eid/cancel'        : return '\Flexio\Api2\Process::cancel';

            // streams
            case 'GET /:userid/streams/:eid'                 : return '\Flexio\Api2\Stream::get';
            case 'GET /:userid/streams/:eid/content'         : return '\Flexio\Api2\Stream::content';

            // statistics
            case 'GET /:userid/statistics/processes'         : return '\Flexio\Api2\Statistics::getUserProcessStats';



            // INTERNAL ENDPOINTS

            // admin
            case 'GET /admin/list/users'                     : return '\Flexio\Api2\Admin::getUserList';
            case 'GET /admin/statistics/users'               : return '\Flexio\Api2\Admin::getUserProcessStats';
            case 'GET /admin/configuration'                  : return '\Flexio\Api2\Admin::getConfiguration';
            case 'GET /admin/resetconfig'                    : return '\Flexio\Api2\User::resetConfig';    // resets the user configuration

            case 'GET /admin/tests/configure'                : return '\Flexio\Tests\Base::configure';
            case 'GET /admin/tests/run'                      : return '\Flexio\Tests\Base::run';
        }
    }
}
