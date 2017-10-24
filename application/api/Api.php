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


class Request
{
    private $method;
    private $url_params;
    private $query_params;
    private $post_params;
    private $requesting_user;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create() : \Flexio\Api\Request
    {
        return (new static);
    }

    public function copy() : \Flexio\Api\Request
    {
        // create a new object and set the properties
        $new_request = static::create();
        $new_request->setMethod($this->getMethod());
        $new_request->setUrlParams($this->getUrlParams());
        $new_request->setQueryParams($this->getQueryParams());
        $new_request->setPostParams($this->getPostParams());
        $new_request->setRequestingUser($this->getRequestingUser());
        return $new_request;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function setUrlParams(array $params)
    {
        $this->url_params = $params;
    }

    public function getUrlParams() : array
    {
        return $this->url_params;
    }

    public function setQueryParams(array $params)
    {
        $this->query_params = $params;
    }

    public function getQueryParams() : array
    {
        return $this->query_params;
    }

    public function setPostParams(array $params)
    {
        $this->post_params = $params;
    }

    public function getPostParams() : array
    {
        return $this->post_params;
    }

    public function setRequestingUser(string $param)
    {
        $this->requesting_user = $param;
    }

    public function getRequestingUser() : string
    {
        return $this->requesting_user;
    }

    private function initialize()
    {
        $this->method = '';
        $this->url_params = array();
        $this->query_params = array();
        $this->post_params = array();
        $this->requesting_user = '';
    }
}

class Api
{
    public static function request(\Flexio\System\FrameworkRequest $server_request, array $combined_params, array $query_params, array $post_params, bool $echo = true)
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
        $api_request = \Flexio\Api\Request::create();
        $api_request->setMethod($method);
        $api_request->setUrlParams($url_params);
        $api_request->setQueryParams($query_params);
        $api_request->setPostParams($post_params);

        $requesting_user_eid = \Flexio\System\System::getCurrentUserEid();
        if (\Flexio\Base\Eid::isValid($requesting_user_eid))
            $api_request->setRequestingUser($requesting_user_eid);
             else
            $api_request->setRequestingUser(\Flexio\Object\User::MEMBER_PUBLIC);

        // process the request
        try
        {
            $content = self::processRequest($api_request);
            self::sendContentResponse($content, $echo);
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

            self::sendErrorResponse($error, $echo);
        }
        catch (\Exception $e)
        {
            $error = array();
            $error['code'] = \Flexio\Base\Error::GENERAL;
            $error['message'] = '';

            if (IS_DEBUG() !== false)
            {
                $error['type'] = 'php exception';
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            self::sendErrorResponse($error, $echo);
        }
        catch (\Error $e)
        {
            $error = array();
            $error['code'] = \Flexio\Base\Error::GENERAL;
            $error['message'] = '';

            if (IS_DEBUG() !== false)
            {
                $error['type'] = 'php error';
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['debug_message'] = $e->getMessage();
                $error['trace'] = $e->getTrace();
            }

            self::sendErrorResponse($error, $echo);
        }
    }

    private static function processRequest(\Flexio\Api\Request $request)
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
        $request_route1 = $request->copy();
        $url_params_route1 = $request_route1->getUrlParams();

        // ROUTE 2: create a route for the request using eids or identifiers for
        // the second and forth api parameters
        $request_route2 = $request->copy();
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
                 if ($apiendpoint == 'GET /vfs/list')         return '\Flexio\Api\Vfs::list';
            else if (substr($apiendpoint,0,9) == 'GET /vfs/') return '\Flexio\Api\Vfs::get';
            else if (substr($apiendpoint,0,9) == 'PUT /vfs/') return '\Flexio\Api\Vfs::put';
        }

        switch ($apiendpoint)
        {
            default:
                return '';

            // system (public)
            case 'POS /validate'                       : return '\Flexio\Api\System::validate';
            case 'POS /login'                          : return '\Flexio\Api\System::login';
            case 'POS /logout'                         : return '\Flexio\Api\System::logout';

            // system (adminstrator)
            case 'GET /system/statistics/users'        : return '\Flexio\Api\System::getProcessUserStats';
            case 'GET /system/statistics/processes'    : return '\Flexio\Api\System::getProcessCreationStats';
            case 'GET /system/statistics/tasks'        : return '\Flexio\Api\System::getProcessTaskStats';
            case 'GET /system/configuration'           : return '\Flexio\Api\System::getConfiguration'; // displays config info

            // search
            case 'GET /search'                         : return '\Flexio\Api\Search::exec';

            // users

            // TODO: convert user password api to something like:
            // 'POS /users/me/credentials' // changing password
            // 'DEL /users/me/credentials' // resetting password

            case 'GET /users/me'                       : return '\Flexio\Api\User::about';
            case 'GET /users/me/statistics'            : return '\Flexio\Api\User::statistics';

            case 'POS /users'                          : return '\Flexio\Api\User::create';
            case 'POS /users/resendverify'             : return '\Flexio\Api\User::resendverify';
            case 'POS /users/activate'                 : return '\Flexio\Api\User::activate';
            case 'POS /users/:eid'                     : return '\Flexio\Api\User::set';
            case 'GET /users/:eid'                     : return '\Flexio\Api\User::get';
            case 'POS /users/:eid/changepassword'      : return '\Flexio\Api\User::changepassword';
            case 'POS /users/resetpassword'            : return '\Flexio\Api\User::resetpassword';
            case 'POS /users/requestpasswordreset'     : return '\Flexio\Api\User::requestpasswordreset';

            // sharing
            case 'GET /rights'                         : return '\Flexio\Api\Right::listall';
            case 'POS /rights'                         : return '\Flexio\Api\Right::create';
            case 'POS /rights/:eid'                    : return '\Flexio\Api\Right::set';
            case 'GET /rights/:eid'                    : return '\Flexio\Api\Right::get';
            case 'DEL /rights/:eid'                    : return '\Flexio\Api\Right::delete';
            // DEPRECATED (users/:eid/tokens; merge with rights?):
            case 'GET /users/:eid/tokens'              : return '\Flexio\Api\Token::listall';
            case 'POS /users/:eid/tokens'              : return '\Flexio\Api\Token::create';
            case 'GET /users/:eid/tokens/:eid'         : return '\Flexio\Api\Token::get';
            case 'DEL /users/:eid/tokens/:eid'         : return '\Flexio\Api\Token::delete';

            // projects
            case 'POS /projects'                       : return '\Flexio\Api\Project::create';
            case 'GET /projects'                       : return '\Flexio\Api\Project::listall';
            case 'POS /projects/:eid'                  : return '\Flexio\Api\Project::set';
            case 'GET /projects/:eid'                  : return '\Flexio\Api\Project::get';
            case 'DEL /projects/:eid'                  : return '\Flexio\Api\Project::delete';

            // trash
            case 'POS /trash'                          : return '\Flexio\Api\Trash::add';
            case 'GET /trash'                          : return '\Flexio\Api\Trash::listall';
            case 'DEL /trash'                          : return '\Flexio\Api\Trash::empty';
            case 'POS /restore'                        : return '\Flexio\Api\Trash::restore';

            // pipes
            case 'POS /pipes'                          : return '\Flexio\Api\Pipe::create';
            case 'GET /pipes'                          : return '\Flexio\Api\Pipe::listall';
            case 'POS /pipes/:eid'                     : return '\Flexio\Api\Pipe::set';
            case 'GET /pipes/:eid'                     : return '\Flexio\Api\Pipe::get';
            case 'DEL /pipes/:eid'                     : return '\Flexio\Api\Pipe::delete';
            case 'POS /pipes/:eid/tasks'               : return '\Flexio\Api\Pipe::addTaskStep';
            case 'DEL /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::deleteTaskStep';
            case 'POS /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::setTaskStep';
            case 'GET /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::getTaskStep';
            case 'POS /pipes/:eid/processes'           : return '\Flexio\Api\Process::create';
            case 'GET /pipes/:eid/processes'           : return '\Flexio\Api\Pipe::processes';

            // experimental API endpoint for running a pipe with form parameters
            case 'POS /pipes/:eid/run'                 : return '\Flexio\Api\Pipe::run';
            case 'GET /pipes/:eid/run'                 : return '\Flexio\Api\Pipe::run';
            case 'GET /pipes/:eid/validate'            : return '\Flexio\Api\Pipe::validate';

            // connections
            case 'POS /connections'                    : return '\Flexio\Api\Connection::create';
            case 'GET /connections'                    : return '\Flexio\Api\Connection::listall';
            case 'POS /connections/:eid'               : return '\Flexio\Api\Connection::set';
            case 'GET /connections/:eid'               : return '\Flexio\Api\Connection::get';
            case 'DEL /connections/:eid'               : return '\Flexio\Api\Connection::delete';
            case 'GET /connections/:eid/describe'      : return '\Flexio\Api\Connection::describe';
            case 'POS /connections/:eid/connect'       : return '\Flexio\Api\Connection::connect';
            case 'POS /connections/:eid/disconnect'    : return '\Flexio\Api\Connection::disconnect';

            // storage
            case 'POS /storage'                        : return '\Flexio\Api\Connection::create';
            case 'GET /storage'                        : return '\Flexio\Api\Connection::listall';
            case 'POS /storage/:eid'                   : return '\Flexio\Api\Connection::set';
            case 'GET /storage/:eid'                   : return '\Flexio\Api\Connection::get';
            case 'DEL /storage/:eid'                   : return '\Flexio\Api\Connection::delete';
            case 'GET /storage/:eid/describe'          : return '\Flexio\Api\Connection::describe';
            case 'POS /storage/:eid/connect'           : return '\Flexio\Api\Connection::connect';
            case 'POS /storage/:eid/disconnect'        : return '\Flexio\Api\Connection::disconnect';

            // processes
            case 'POS /processes'                      : return '\Flexio\Api\Process::create';
            case 'GET /processes'                      : return '\Flexio\Api\Process::listall';
            case 'POS /processes/:eid'                 : return '\Flexio\Api\Process::set';
            case 'GET /processes/:eid'                 : return '\Flexio\Api\Process::get';
            case 'GET /processes/:eid/tasks/:eid/input/info'  : return '\Flexio\Api\Process::getTaskInputInfo';
            case 'GET /processes/:eid/tasks/:eid/output/info' : return '\Flexio\Api\Process::getTaskOutputInfo';
            case 'POS /processes/:eid/run'             : return '\Flexio\Api\Process::run';

            // streams
            case 'POS /streams'                        : return '\Flexio\Api\Stream::create';
            case 'GET /streams/:eid'                   : return '\Flexio\Api\Stream::get';
            case 'POS /streams/:eid'                   : return '\Flexio\Api\Stream::set';
            case 'GET /streams/:eid/content'           : return '\Flexio\Api\Stream::content';
            case 'GET /streams/:eid/download'          : return '\Flexio\Api\Stream::download';
            case 'POS /streams/:eid/upload'            : return '\Flexio\Api\Stream::upload';

            // test suite
            case 'GET /tests/configure'                : return '\Flexio\Tests\TestBase::configure';
            case 'GET /tests/run'                      : return '\Flexio\Tests\TestBase::run';

            // DEBUG: endpoints for easy debugging using a URL in a browser
            case 'GET /processes/debug'                : return '\Flexio\Api\Process::debug'; // display process info
            case 'GET /debug/resetconfig'              : return '\Flexio\Api\User::resetConfig'; // resets the user configuration
            case 'GET /debug/createexamples'           : return '\Flexio\Api\User::createExamples'; // creates example pipes
        }
    }

    private static function sendContentResponse($content, bool $echo)
    {
        if ($echo === false)
            return $content;

        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        $response = @json_encode($content, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function sendErrorResponse(array $error, bool $echo)
    {
        $response = array();
        $response['error'] = $error;
        if ($echo === false)
            return $response;

        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        $error = $response['error'];
        $error_code = $error['code'] ?? '';
        $error_message = $error['message'] ?? '';

        // change the error code to be more specific in case of unauthorized access
        if ($error_code === \Flexio\Base\Error::INSUFFICIENT_RIGHTS && self::sessionAuthExpired() === true)
            $error_code = \Flexio\Base\Error::UNAUTHORIZED;

        // set the specific error header
        switch ($error_code)
        {
            // TODO: for now, map the default and ERROR_GENERAL to 400, which
            // is what we've been using for all error codes up 'till now; however,
            // we may want to switch to 500, in which case we'll need to properly
            // assign ERROR_GENERAL to other categories when appropriate
            default:
            case \Flexio\Base\Error::GENERAL:
                \Flexio\Base\Util::header_error(400);
                break;

            // "UNAUTHORIZED" type errors; the user might have access to the object
            // if they were logged in, but the session is invalid
            case \Flexio\Base\Error::UNAUTHORIZED:
                \Flexio\Base\Util::header_error(401);
                break;

            // "FORBIDDEN" type errors; access not allowed
            case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:
                \Flexio\Base\Util::header_error(403);
                break;

            // "NOT FOUND" type errors; invalid requests, invalid
            // parameters, or valid requests for objects that can't
            // be found
            case \Flexio\Base\Error::INVALID_VERSION:
            case \Flexio\Base\Error::INVALID_REQUEST:
            case \Flexio\Base\Error::MISSING_PARAMETER:
            case \Flexio\Base\Error::INVALID_PARAMETER:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
            case \Flexio\Base\Error::NO_OBJECT:
                \Flexio\Base\Util::header_error(404);
                break;

            // "UNPROCESSABLE ENTITY"; request can't be processed
            // for some reason
            case \Flexio\Base\Error::CREATE_FAILED:
            case \Flexio\Base\Error::DELETE_FAILED:
            case \Flexio\Base\Error::WRITE_FAILED:
            case \Flexio\Base\Error::READ_FAILED:
            case \Flexio\Base\Error::RATE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:
                \Flexio\Base\Util::header_error(422);
                break;

            // "INTERNAL SERVER ERROR"; something is wrong internally
            case \Flexio\Base\Error::UNDEFINED:
            case \Flexio\Base\Error::UNIMPLEMENTED:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
                \Flexio\Base\Util::header_error(500);
                break;
        }

        // if a message isn't specified, supply a default message
        if (strlen($error_message ) == 0)
            $error['message'] = \Flexio\Base\Error::getDefaultMessage($error_code);

        $response['error'] = $error;
        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function sessionAuthExpired() : bool
    {
        return (isset($_COOKIE['FXSESSID']) && strlen($_COOKIE['FXSESSID']) > 0 && $GLOBALS['g_store']->user_eid == '') ? true:false;
    }
}
