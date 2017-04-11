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
    public static function request(\Flexio\System\FrameworkRequest $server_request, array $params, $echo = true)
    {
        // get the method
        $method = $server_request->getMethod();

        // get the url parts and map them to the api parameters
        $urlpart0 = $server_request->getUrlPathPart(0);
        $urlpart1 = $server_request->getUrlPathPart(1);
        $urlpart2 = $server_request->getUrlPathPart(2);
        $urlpart3 = $server_request->getUrlPathPart(3);
        $urlpart4 = $server_request->getUrlPathPart(4);
        $urlpart5 = $server_request->getUrlPathPart(5);
        $urlpart6 = $server_request->getUrlPathPart(6);
        $urlpart7 = $server_request->getUrlPathPart(7);

        if (isset($urlpart1)) $params['apiversion'] = $urlpart1;
        if (isset($urlpart2)) $params['apiparam1']  = $urlpart2;
        if (isset($urlpart3)) $params['apiparam2']  = $urlpart3;
        if (isset($urlpart4)) $params['apiparam3']  = $urlpart4;
        if (isset($urlpart5)) $params['apiparam4']  = $urlpart5;
        if (isset($urlpart6)) $params['apiparam5']  = $urlpart6;
        if (isset($urlpart7)) $params['apiparam6']  = $urlpart7;


        // STEP 1: create the request object
        $requesting_user_eid = \Flexio\System\System::getCurrentUserEid();
        if (!\Flexio\Base\Eid::isValid($requesting_user_eid))
            $requesting_user_eid = \Flexio\Object\User::USER_PUBLIC;

        $request = Request::create();
        $request->setRequestingUser($requesting_user_eid);


        // STEP 2: process the request
        try
        {
            // if we're testing a failure, throw an error right away
            if (isset($params['testfail']) && strlen($params['testfail']) > 0 && (IS_DEBUG() || IS_TESTING()))
            {
                $fail_string = $params['testfail'];
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, $fail_string);
            }

            if (!isset($params['apiversion']) || $params['apiversion'] !== 'v1')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

            if (!isset($params['apiparam1']) || strlen($params['apiparam1']) == 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_VERSION);

            // split the request into the url params and the query params;
            $url_params = array();
            if (isset($params['apiparam1'])) $url_params['apiparam1'] = $params['apiparam1'];
            if (isset($params['apiparam2'])) $url_params['apiparam2'] = $params['apiparam2'];
            if (isset($params['apiparam3'])) $url_params['apiparam3'] = $params['apiparam3'];
            if (isset($params['apiparam4'])) $url_params['apiparam4'] = $params['apiparam4'];
            if (isset($params['apiparam5'])) $url_params['apiparam5'] = $params['apiparam5'];
            if (isset($params['apiparam6'])) $url_params['apiparam6'] = $params['apiparam6'];

            $query_params = $params;
            unset($query_params['apiversion']);
            unset($query_params['apiparam1']);
            unset($query_params['apiparam2']);
            unset($query_params['apiparam3']);
            unset($query_params['apiparam4']);
            unset($query_params['apiparam5']);
            unset($query_params['apiparam6']);

            // send the response
            $response = self::processRequest($method, $url_params, $query_params, $request);
            if ($echo === false)
                return $response;

            self::sendContentResponse($response);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $info['code'];
            $trace = json_encode($e->getTrace());
            $message = $info['message'] . (IS_DEBUG() === false ? '' : "; exception thrown in file $file on line $line; trace: $trace");

            $response = array();
            $response['errors'][] = array(
                'code' => $code,
                'message' => $message
            );
            if ($echo === false)
                return $response;

            self::sendErrorResponse($response);
        }
        catch (\Exception $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = \Flexio\Base\Error::GENERAL;
            $trace = json_encode($e->getTrace());
            $message = (IS_DEBUG() === false ? '' : "exception thrown in file $file on line $line; trace: $trace");

            $response = array();
            $response['errors'][] = array(
                'code' => $code,
                'message' => $message
            );
            if ($echo === false)
                return $response;

            self::sendErrorResponse($response);
        }
        catch (\Error $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = \Flexio\Base\Error::GENERAL;
            $trace = json_encode($e->getTrace());
            $message = (IS_DEBUG() === false ? '' : "error thrown in file $file on line $line; trace: $trace");

            $response = array();
            $response['errors'][] = array(
                'code' => $code,
                'message' => $message
            );
            if ($echo === false)
                return $response;

            self::sendErrorResponse($response);
        }
    }

    private static function processRequest(string $request_method, array $url_params, array $query_params, \Flexio\Api\Request $request)
    {
        // make sure we have a valid request method
        switch ($request_method)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                break;
        }

        // check url parameter count; has to be at least 1
        $url_param_count = count($url_params);
        if ($url_param_count < 1)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // save the request parameters; TODO: create action/history object
        $action_params_to_save = array(
            'user_eid' => $request->getRequestingUser(),
            'request_method' => $request_method,
            'url_params' => json_encode($url_params),
            'query_params' => json_encode($query_params)
        );
        \Flexio\System\System::getModel()->action->record($action_params_to_save);


        // ROUTE 1: try to route the request "as is" before looking for any
        // eids or identifiers in the path; if we can route the request,
        // we're done; this gives precedence to api keywords over eids and
        // identifiers
        $api_params = self::mapUrlParamsToApiParams($url_params);
        $api_path = self::createApiPath($request_method, $api_params);

        $function = self::getApiEndpoint($api_path);
        if (is_callable($function) === true)
            return $function($query_params, $request);


        // ROUTE 2: if we weren't able to route the request "as is", try to
        // route the request based on checking for eids or identifiers in the
        // second and forth api parameters
        $api_params = self::mapUrlParamsToApiParams($url_params);

        if (\Flexio\Base\Eid::isValid($api_params['apiparam2']) || \Flexio\Base\Identifier::isValid($api_params['apiparam2']))
            $api_params['apiparam2'] = ':eid';
        if (\Flexio\Base\Eid::isValid($api_params['apiparam4']) || \Flexio\Base\Identifier::isValid($api_params['apiparam4']))
            $api_params['apiparam4'] = ':eid';

        $api_path = self::createApiPath($request_method, $api_params);

        // create the input parameters by merging the eid(s) specified
        // in the url with the query parameters;
        $p = array();
        if ($api_params['apiparam2'] === ':eid' && $api_params['apiparam4'] === ':eid')
        {
            // if the second and fourth api parameters are eids,
            // the first is the parent and the second the primary
            $p['parent_eid'] = $url_params['apiparam2'];
            $p['eid'] = $url_params['apiparam4'];
        }

        if ($api_params['apiparam2'] === ':eid' && $api_params['apiparam4'] !== ':eid')
        {
            // if the second parameter is an eid, but the fourth
            // isn't, treat the second eid as the parent eid if there's
            // a third parameter and eid is a query parameter; otherwise
            // treat the second parameter as the primary eid
            if (isset($url_params['apiparam3']) && isset($query_params['eid']))
                $p['parent_eid'] = $url_params['apiparam2'];
                 else
                $p['eid'] = $url_params['apiparam2'];
        }
        $query_params = array_merge($p, $query_params);

        $function = self::getApiEndpoint($api_path);
        if (is_callable($function) === true)
            return $function($query_params, $request);

        // we can't find the specified api endpoint
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);
    }

    private static function getApiEndpoint(string $apiendpoint) : string
    {
        switch ($apiendpoint)
        {
            default:
                return '';

            // system (public)
            case 'POS /validate'                       : return '\Flexio\Api\System::validate';
            case 'POS /login'                          : return '\Flexio\Api\System::login';
            case 'POS /logout'                         : return '\Flexio\Api\System::logout';

            // search
            case 'GET /search'                         : return '\Flexio\Api\Search::exec';

            // users
            case 'GET /users/me'                       : return '\Flexio\Api\User::about';
            case 'GET /users/me/statistics'            : return '\Flexio\Api\User::statistics';

            case 'POS /users'                          : return '\Flexio\Api\User::create';
            case 'POS /users/resetpassword'            : return '\Flexio\Api\User::resetpassword';
            case 'POS /users/requestpasswordreset'     : return '\Flexio\Api\User::requestpasswordreset';
            case 'POS /users/resendverify'             : return '\Flexio\Api\User::resendverify';
            case 'POS /users/activate'                 : return '\Flexio\Api\User::activate';
            case 'POS /users/:eid'                     : return '\Flexio\Api\User::set';
            case 'GET /users/:eid'                     : return '\Flexio\Api\User::get';
            case 'POS /users/:eid/changepassword'      : return '\Flexio\Api\User::changepassword';
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
            case 'GET /projects/:eid/pipes'            : return '\Flexio\Api\Project::pipes';
            case 'GET /projects/:eid/connections'      : return '\Flexio\Api\Project::connections';
            case 'POS /projects/:eid/trash'            : return '\Flexio\Api\Project::addTrash';
            case 'GET /projects/:eid/trash'            : return '\Flexio\Api\Project::trashed';
            case 'DEL /projects/:eid/trash'            : return '\Flexio\Api\Project::clearTrash';
            case 'POS /projects/:eid/restore'          : return '\Flexio\Api\Project::unTrash';

            // followers
            case 'POS /projects/:eid/followers'        : return '\Flexio\Api\Follower::create';
            case 'GET /projects/:eid/followers'        : return '\Flexio\Api\Follower::listall';
            case 'DEL /projects/:eid/followers/:eid'   : return '\Flexio\Api\Follower::delete';

            // pipes
            case 'POS /pipes'                          : return '\Flexio\Api\Pipe::create';
            case 'GET /pipes'                          : return '\Flexio\Api\Pipe::listall';
            case 'POS /pipes/:eid'                     : return '\Flexio\Api\Pipe::set';
            case 'GET /pipes/:eid'                     : return '\Flexio\Api\Pipe::get';
            case 'DEL /pipes/:eid'                     : return '\Flexio\Api\Pipe::delete';
            case 'GET /pipes/:eid/comments'            : return '\Flexio\Api\Pipe::comments';
            case 'POS /pipes/:eid/tasks'               : return '\Flexio\Api\Pipe::addTaskStep';
            case 'DEL /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::deleteTaskStep';
            case 'POS /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::setTaskStep';
            case 'GET /pipes/:eid/tasks/:eid'          : return '\Flexio\Api\Pipe::getTaskStep';
            case 'POS /pipes/:eid/processes'           : return '\Flexio\Api\Process::create';
            case 'GET /pipes/:eid/processes'           : return '\Flexio\Api\Pipe::processes';

            // experimental API endpoint for running a pipe with form parameters
            case 'POS /pipes/:eid/run'                 : return '\Flexio\Api\Pipe::run';
            case 'GET /pipes/:eid/validate'            : return '\Flexio\Api\Pipe::validate';

            // connections
            case 'POS /connections'                    : return '\Flexio\Api\Connection::create';
            case 'POS /connections/:eid'               : return '\Flexio\Api\Connection::set';
            case 'GET /connections/:eid'               : return '\Flexio\Api\Connection::get';
            case 'DEL /connections/:eid'               : return '\Flexio\Api\Connection::delete';
            case 'GET /connections/:eid/comments'      : return '\Flexio\Api\Connection::comments';
            case 'GET /connections/:eid/describe'      : return '\Flexio\Api\Connection::describe';
            case 'POS /connections/:eid/connect'       : return '\Flexio\Api\Connection::connect';
            case 'POS /connections/:eid/disconnect'    : return '\Flexio\Api\Connection::disconnect';

            // processes
            case 'POS /processes'                      : return '\Flexio\Api\Process::create';
            case 'GET /processes/:eid'                 : return '\Flexio\Api\Process::get';
            case 'POS /processes/:eid'                 : return '\Flexio\Api\Process::set';
            case 'POS /processes/:eid/input'           : return '\Flexio\Api\Process::addInput';
            case 'GET /processes/:eid/input'           : return '\Flexio\Api\Process::getInput';
            case 'GET /processes/:eid/output'          : return '\Flexio\Api\Process::getOutput';
            case 'GET /processes/:eid/tasks/:eid/input/info'  : return '\Flexio\Api\Process::getTaskInputInfo';
            case 'GET /processes/:eid/tasks/:eid/output/info' : return '\Flexio\Api\Process::getTaskOutputInfo';
            case 'GET /processes/statistics'           : return '\Flexio\Api\Process::getStatistics';

            // DEPRECATED: another way to do this that doesn't involve a high-level url endpoint verb?
            case 'POS /processes/:eid/run'             : return '\Flexio\Api\Process::run';
            case 'POS /processes/:eid/cancel'          : return '\Flexio\Api\Process::cancel';
            case 'POS /processes/:eid/pause'           : return '\Flexio\Api\Process::pause';

            // streams
            case 'POS /streams'                        : return '\Flexio\Api\Stream::create';
            case 'GET /streams/:eid'                   : return '\Flexio\Api\Stream::get';
            case 'POS /streams/:eid'                   : return '\Flexio\Api\Stream::set';
            case 'GET /streams/:eid/content'           : return '\Flexio\Api\Stream::content';
            case 'GET /streams/:eid/download'          : return '\Flexio\Api\Stream::download';
            case 'POS /streams/:eid/upload'            : return '\Flexio\Api\Stream::upload';

            // comments
            case 'POS /comments'                       : return '\Flexio\Api\Comment::create';

            // test suite
            case 'GET /tests/configure'                : return '\Flexio\Tests\TestBase::configure';
            case 'GET /tests/run'                      : return '\Flexio\Tests\TestBase::run';

            // DEBUG: endpoints for easy debugging using a URL in a browser
            case 'GET /processes/debug'                : return '\Flexio\Api\Process::debug'; // display process info
            case 'GET /debug/sysconfig'                : return '\Flexio\Api\System::configuration'; // displays config info
            case 'GET /debug/resetconfig'              : return '\Flexio\Api\User::resetConfig'; // resets the user configuration
            case 'GET /debug/createsampleproject'      : return '\Flexio\Api\User::createSample'; // creates a sample project
        }
    }

    private static function sendContentResponse($response)
    {
        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function sendErrorResponse($response)
    {
        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');


        // TODO: don't try to set/return an array of errors
        $error = end($response);


        // get the error code and message
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
            case \Flexio\Base\Error::INVALID_METHOD:
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

/*
        // if a message isn't specified, supply a default message
        if (strlen($error_message ) == 0)
        {
            // try to map the code to suitable api error message
            switch ($error_message)
            {
                default:                                         $error_message = _('Operation failed');            break;
                case \Flexio\Base\Error::UNDEFINED:              $error_message = _('Operation failed');            break;
                case \Flexio\Base\Error::GENERAL:                $error_message = _('General error');               break;
                case \Flexio\Base\Error::UNIMPLEMENTED:          $error_message = _('Unimplemented');               break;
                case \Flexio\Base\Error::NO_DATABASE:            $error_message = _('Database not available');      break;
                case \Flexio\Base\Error::NO_MODEL:               $error_message = _('Model not available');         break;
                case \Flexio\Base\Error::NO_SERVICE:             $error_message = _('Service not available');       break;
                case \Flexio\Base\Error::NO_OBJECT:              $error_message = _('Object not available');        break;
                case \Flexio\Base\Error::INVALID_SYNTAX:         $error_message = _('Invalid syntax');              break;
                case \Flexio\Base\Error::MISSING_PARAMETER:      $error_message = _('Missing parameter');           break;
                case \Flexio\Base\Error::INVALID_PARAMETER:      $error_message = _('Invalid parameter');           break;
                case \Flexio\Base\Error::CREATE_FAILED:          $error_message = _('Could not create object');     break;
                case \Flexio\Base\Error::DELETE_FAILED:          $error_message = _('Could not delete object');     break;
                case \Flexio\Base\Error::WRITE_FAILED:           $error_message = _('Could not write to object');   break;
                case \Flexio\Base\Error::READ_FAILED:            $error_message = _('Could not read from object');  break;
                case \Flexio\Base\Error::UNAUTHORIZED:           $error_message = _('Unauthorized');                break;
                case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:    $error_message = _('Insufficient rights');         break;
                case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:    $error_message = _('Size limit exceeded');         break;
            }

            // TODO: set error message
        }
*/

        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function sessionAuthExpired() : bool
    {
        return (isset($_COOKIE['FXSESSID']) && strlen($_COOKIE['FXSESSID']) > 0 && $GLOBALS['g_store']->user_eid == '') ? true:false;
    }

    private static function mapUrlParamsToApiParams(array $url_params) : array
    {
        // get the api params from the url params
        $api_params = array();
        $api_params['apiparam1'] = $url_params['apiparam1'] ?? '';
        $api_params['apiparam2'] = $url_params['apiparam2'] ?? '';
        $api_params['apiparam3'] = $url_params['apiparam3'] ?? '';
        $api_params['apiparam4'] = $url_params['apiparam4'] ?? '';
        $api_params['apiparam5'] = $url_params['apiparam5'] ?? '';
        $api_params['apiparam6'] = $url_params['apiparam6'] ?? '';
        return $api_params;
    }

    private static function createApiPath(string $request_method, array $params) : string
    {
        // creates an api endpoint string that's used to lookup the
        // appropriate api implementation
        $apiendpoint = '';

        if ($request_method === 'GET')    $apiendpoint .= 'GET ';
        if ($request_method === 'POST')   $apiendpoint .= 'POS ';
        if ($request_method === 'PUT')    $apiendpoint .= 'PUT ';
        if ($request_method === 'DELETE') $apiendpoint .= 'DEL ';

        $apiendpoint .= (strlen($params['apiparam1']) > 0 ? ('/' . $params['apiparam1']) : '');
        $apiendpoint .= (strlen($params['apiparam2']) > 0 ? ('/' . $params['apiparam2']) : '');
        $apiendpoint .= (strlen($params['apiparam3']) > 0 ? ('/' . $params['apiparam3']) : '');
        $apiendpoint .= (strlen($params['apiparam4']) > 0 ? ('/' . $params['apiparam4']) : '');
        $apiendpoint .= (strlen($params['apiparam5']) > 0 ? ('/' . $params['apiparam5']) : '');
        $apiendpoint .= (strlen($params['apiparam6']) > 0 ? ('/' . $params['apiparam6']) : '');

        return $apiendpoint;
    }
}
