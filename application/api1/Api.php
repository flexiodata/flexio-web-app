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
namespace Flexio\Api1;


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
        $api_request = \Flexio\Api1\Request::create();
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
            \Flexio\Api1\Response::sendContent($content);
            return;
        }

        // process the request
        try
        {
            $content = self::processRequest($api_request);
            \Flexio\Api1\Response::sendContent($content);
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

            \Flexio\Api1\Response::sendError($error);
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

            \Flexio\Api1\Response::sendError($error);
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

            \Flexio\Api1\Response::sendError($error);
        }
    }

    private static function processRequest(\Flexio\Api1\Request $request)
    {
        $request_method = $request->getMethod();
        $url_params = $request->getUrlParams();
        $query_params = $request->getQueryParams();
        $post_params = $request->getPostParams();

        if (isset($query_params['testfail']) && strlen($query_params['testfail']) > 0 && (IS_DEBUG() || IS_TESTING()))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, $query_params['testfail']);

        if ($url_params['apibase'] !== 'api' || $url_params['apiversion'] !== 'v1')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

        // ROUTE 1: create a route for the request "as is" without looking for
        // any identifiers in the path; this gives precedence to api keywords over
        // eids and identifiers
        $request_route1 = $request->clone();
        $url_params_route1 = $request_route1->getUrlParams();

        // ROUTE 2: create a route for the request using eids or identifiers for
        // the second and forth api parameters
        $request_route2 = $request->clone();
        $url_params_route2 = $request_route2->getUrlParams();

        if (\Flexio\Base\Eid::isValid($url_params_route2['apiparam2']) || \Flexio\Base\Identifier::isValid($url_params_route2['apiparam2']))
            $url_params_route2['apiparam2'] = ':eid';
        if (\Flexio\Base\Eid::isValid($url_params_route2['apiparam4']) || \Flexio\Base\Identifier::isValid($url_params_route2['apiparam4']))
            $url_params_route2['apiparam4'] = ':eid';

        // create the input parameters by merging the eid(s) specified
        // in the url with the query parameters;
        $p = array();
        if ($url_params_route2['apiparam2'] === ':eid' && $url_params_route2['apiparam4'] === ':eid')
        {
            // if the second and fourth api parameters are eids,
            // the first is the parent and the second the primary
            $p['parent_eid'] = $url_params['apiparam2'];
            $p['eid'] = $url_params['apiparam4'];
        }

        if ($url_params_route2['apiparam2'] === ':eid' && $url_params_route2['apiparam4'] !== ':eid')
        {
            // if the second parameter is an eid, but the fourth
            // isn't, treat the second eid as the parent eid if there's
            // a third parameter and eid is a query parameter; otherwise
            // treat the second parameter as the primary eid
            if (strlen($url_params['apiparam3']) > 0 && isset($query_params['eid']))
                $p['parent_eid'] = $url_params['apiparam2'];
                 else
                $p['eid'] = $url_params['apiparam2'];
        }
        $updated_query_params = array_merge($p, $query_params);
        $updated_post_params = array_merge($p, $post_params);
        $request_route2->setQueryParams($updated_query_params);
        $request_route2->setPostParams($updated_post_params);

        // try a request with the URL as is
        $function = self::getApiEndpoint($request_method, $url_params_route1);
        if (is_callable($function) === true)
            return $function($request_route1);

        // try a request, treating the second and forth as eids or identifiers
        $function = self::getApiEndpoint($request_method, $url_params_route2);
        if (is_callable($function) === true)
            return $function($request_route2);

        // we can't find the specified api endpoint
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);
    }

    private static function getApiEndpoint(string $request_method, array $url_params) : string
    {
        // creates an api endpoint string that's used to lookup the
        // appropriate api implementation
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
                 if ($apiendpoint == 'GET /vfs/list')         return '\Flexio\Api1\Vfs::list';
            else if (substr($apiendpoint,0,9) == 'GET /vfs/') return '\Flexio\Api1\Vfs::get';
            else if (substr($apiendpoint,0,9) == 'PUT /vfs/') return '\Flexio\Api1\Vfs::put';
        }

        switch ($apiendpoint)
        {
            default:
                return '';

            // system (public)
            case 'GET /about'                          : return '\Flexio\Api1\System::about';
            case 'POS /validate'                       : return '\Flexio\Api1\System::validate';
            case 'POS /login'                          : return '\Flexio\Api1\System::login';
            case 'POS /logout'                         : return '\Flexio\Api1\System::logout';

            // users

            // TODO: convert user password api to something like:
            // 'POS /users/me/credentials' // changing password
            // 'DEL /users/me/credentials' // resetting password

            case 'POS /users'                          : return '\Flexio\Api1\User::create';
            case 'GET /users/me'                       : return '\Flexio\Api1\User::about';
            case 'POS /users/:eid'                     : return '\Flexio\Api1\User::set';
            case 'GET /users/:eid'                     : return '\Flexio\Api1\User::get';
            case 'POS /users/:eid/changepassword'      : return '\Flexio\Api1\User::changepassword';
            case 'POS /users/resetpassword'            : return '\Flexio\Api1\User::resetpassword';
            case 'POS /users/requestpasswordreset'     : return '\Flexio\Api1\User::requestpasswordreset';
            case 'POS /users/resendverify'             : return '\Flexio\Api1\User::resendverify';
            case 'POS /users/activate'                 : return '\Flexio\Api1\User::activate';

            // sharing
            case 'GET /rights'                         : return '\Flexio\Api1\Right::list';
            case 'POS /rights'                         : return '\Flexio\Api1\Right::create';
            case 'POS /rights/:eid'                    : return '\Flexio\Api1\Right::set';
            case 'GET /rights/:eid'                    : return '\Flexio\Api1\Right::get';
            case 'DEL /rights/:eid'                    : return '\Flexio\Api1\Right::delete';
            case 'GET /users/:eid/tokens'              : return '\Flexio\Api1\Token::list';
            case 'POS /users/:eid/tokens'              : return '\Flexio\Api1\Token::create';
            case 'GET /users/:eid/tokens/:eid'         : return '\Flexio\Api1\Token::get';
            case 'DEL /users/:eid/tokens/:eid'         : return '\Flexio\Api1\Token::delete';

            // connections
            case 'POS /connections'                    : return '\Flexio\Api1\Connection::create';
            case 'GET /connections'                    : return '\Flexio\Api1\Connection::list';
            case 'POS /connections/:eid'               : return '\Flexio\Api1\Connection::set';
            case 'GET /connections/:eid'               : return '\Flexio\Api1\Connection::get';
            case 'DEL /connections/:eid'               : return '\Flexio\Api1\Connection::delete';
            case 'GET /connections/:eid/describe'      : return '\Flexio\Api1\Connection::describe';
            case 'POS /connections/:eid/connect'       : return '\Flexio\Api1\Connection::connect';
            case 'POS /connections/:eid/disconnect'    : return '\Flexio\Api1\Connection::disconnect';

            // pipes
            case 'POS /pipes'                          : return '\Flexio\Api1\Pipe::create';
            case 'GET /pipes'                          : return '\Flexio\Api1\Pipe::list';
            case 'POS /pipes/:eid'                     : return '\Flexio\Api1\Pipe::set';
            case 'GET /pipes/:eid'                     : return '\Flexio\Api1\Pipe::get';
            case 'DEL /pipes/:eid'                     : return '\Flexio\Api1\Pipe::delete';
            case 'POS /pipes/:eid/processes'           : return '\Flexio\Api1\Process::create';
            case 'GET /pipes/:eid/processes'           : return '\Flexio\Api1\Pipe::processes';
            case 'POS /pipes/:eid/run'                 : return '\Flexio\Api1\Pipe::run';
            case 'GET /pipes/:eid/run'                 : return '\Flexio\Api1\Pipe::run';

            // processes
            case 'POS /processes'                      : return '\Flexio\Api1\Process::create';
            case 'GET /processes'                      : return '\Flexio\Api1\Process::list';
            case 'POS /processes/:eid'                 : return '\Flexio\Api1\Process::set';
            case 'GET /processes/:eid'                 : return '\Flexio\Api1\Process::get';
            case 'GET /processes/:eid/log'             : return '\Flexio\Api1\Process::log';
            case 'POS /processes/:eid/run'             : return '\Flexio\Api1\Process::run';
            case 'POS /processes/:eid/cancel'          : return '\Flexio\Api1\Process::cancel';

            // streams
            case 'GET /streams/:eid'                   : return '\Flexio\Api1\Stream::get';
            case 'GET /streams/:eid/content'           : return '\Flexio\Api1\Stream::content';

            // statistics
            case 'GET /statistics/processes'           : return '\Flexio\Api1\Statistics::processes';

            // ADMIN (internal):
            case 'GET /admin/list/users'               : return '\Flexio\Api1\Admin::userlist';
            case 'GET /admin/statistics/users'         : return '\Flexio\Api1\Admin::processes';
            case 'GET /admin/configuration'            : return '\Flexio\Api1\Admin::config';
            case 'GET /admin/resetconfig'              : return '\Flexio\Api1\User::resetConfig';    // resets the user configuration
            case 'GET /admin/createexamples'           : return '\Flexio\Api1\User::createExamples'; // creates example pipes

            // DEBUG (internal):
            case 'GET /processes/debug'                : return '\Flexio\Api1\Process::debug';    // display process info

            // TEST (internal):
            case 'GET /tests/configure'                : return '\Flexio\Tests\Base::configure';
            case 'GET /tests/run'                      : return '\Flexio\Tests\Base::run';
        }
    }
}
