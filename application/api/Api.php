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


namespace Flexio\Api;


class Api
{
    const ERROR_NONE                   =  'none';
    const ERROR_UNDEFINED              =  'undefined';
    const ERROR_GENERAL                =  'general';
    const ERROR_UNIMPLEMENTED          =  'unimplemented';
    const ERROR_NO_DATABASE            =  'no_database';
    const ERROR_NO_MODEL               =  'no_model';
    const ERROR_NO_SERVICE             =  'no_service';
    const ERROR_MISSING_PARAMETER      =  'missing_parameter';
    const ERROR_INVALID_PARAMETER      =  'invalid_parameter';
    const ERROR_INVALID_SYNTAX         =  'invalid_syntax';
    const ERROR_NO_OBJECT              =  'no_object';
    const ERROR_CREATE_FAILED          =  'create_failed';
    const ERROR_DELETE_FAILED          =  'delete_failed';
    const ERROR_WRITE_FAILED           =  'write_failed';
    const ERROR_READ_FAILED            =  'read_failed';
    const ERROR_UNAUTHORIZED           =  'unauthorized';
    const ERROR_INSUFFICIENT_RIGHTS    =  'insufficient_rights';
    const ERROR_SIZE_LIMIT_EXCEEDED    =  'size_limit_exceeded';

    // TODO: integrate these into database
    const ERROR_INVALID_METHOD         =  'invalid_method';
    const ERROR_INVALID_VERSION        =  'invalid_version';
    const ERROR_INVALID_REQUEST        =  'invalid_request';


    public static function request($server_request, $params, $echo = true)
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

        // get the user making the request; if we don't have an eid (or we
        // have an invalid eid), set the user to the public user, which is
        // an internal constant that isn't a valid eid so we can distinguish
        // between it and properly authenticated users)
        $requesting_user_eid = \Flexio\System\System::getCurrentUserEid();
        if (!\Flexio\System\Eid::isValid($requesting_user_eid))
            $requesting_user_eid = \Flexio\Object\User::USER_PUBLIC;

        // create the request object
        $request = Request::create();
        $request->setValidator(\Flexio\System\Validator::getInstance());
        $request->setRequestingUser($requesting_user_eid);


        // STEP 2: parse the params

        // if necessary, parse the url params
        $params = self::parseRequestString($params);

        // set the api version; make sure we have a valid api endpoint;
        // convert api version over to a number
        $apiversion_param = isset_or($params['apiversion'], '');
        unset($params['apiversion']);

        $apiversion = 0;
        if ($apiversion_param === 'v1')
            $apiversion = 1;

        $request->setApiVersion($apiversion);

        if (!isset($params['apiparam1']) || strlen($params['apiparam1']) == 0)
            return $request->getValidator()->fail(Api::ERROR_INVALID_METHOD);

        // split the request into the url params and the query params;
        $url_params = array();
        if (isset($params['apiparam1'])) $url_params['apiparam1'] = $params['apiparam1'];
        if (isset($params['apiparam2'])) $url_params['apiparam2'] = $params['apiparam2'];
        if (isset($params['apiparam3'])) $url_params['apiparam3'] = $params['apiparam3'];
        if (isset($params['apiparam4'])) $url_params['apiparam4'] = $params['apiparam4'];
        if (isset($params['apiparam5'])) $url_params['apiparam5'] = $params['apiparam5'];
        if (isset($params['apiparam6'])) $url_params['apiparam6'] = $params['apiparam6'];

        $query_params = $params;
        unset($query_params['apiparam1']);
        unset($query_params['apiparam2']);
        unset($query_params['apiparam3']);
        unset($query_params['apiparam4']);
        unset($query_params['apiparam5']);
        unset($query_params['apiparam6']);

        // handle tests for failures
        if (isset($query_params['testfail']) && strlen($query_params['testfail']) > 0 && (IS_DEBUG() || IS_TESTING()))
        {
            $fail_string = $query_params['testfail'];
            $response = $request->getValidator()->fail($fail_string);
            $response = self::packageResponse($response, $request);

            if ($echo === false)
                return $response;

            self::sendResponse($response, $request);
            return;
        }

        // handle api v1 requests
        if ($request->getApiVersion() === 1)
        {
            $response = self::processRequest($method, $url_params, $query_params, $request);
            $response = self::packageResponse($response, $request);

            if ($echo === false)
                return $response;

            self::sendResponse($response, $request);
            return;
        }

        // handle unsupported api versions
        $response = $request->getValidator()->fail(Api::ERROR_INVALID_VERSION);
        $response = self::packageResponse($response, $request);

        if ($echo === false)
            return $response;

        self::sendResponse($response, $request);
    }

    private static function processRequest($request_method, $url_params, $query_params, $request)
    {
        // make sure we have a valid request method
        switch ($request_method)
        {
            default:
                return $request->getValidator()->fail(Api::ERROR_INVALID_REQUEST);

            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                break;
        }

        // check url parameter count; has to be at least 1
        $url_param_count = count($url_params);
        if ($url_param_count < 1)
            return $request->getValidator()->fail(Api::ERROR_INVALID_REQUEST);

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
        if ($function !== false && is_callable($function) === true)
            return $function($query_params, $request);

        // ROUTE 2: if we weren't able to route the request "as is", try to
        // route the request based on checking for eids or identifiers in the
        // second and forth api parameters
        $api_params = self::mapUrlParamsToApiParams($url_params);

        if (\Flexio\System\Eid::isValid($api_params['apiparam2']) || \Flexio\System\Identifier::isValid($api_params['apiparam2']))
            $api_params['apiparam2'] = ':eid';
        if (\Flexio\System\Eid::isValid($api_params['apiparam4']) || \Flexio\System\Identifier::isValid($api_params['apiparam4']))
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

        if ($function !== false && is_callable($function) === true)
            return $function($query_params, $request);

        // we can't find the specified api endpoint
        return $request->getValidator()->fail(Api::ERROR_INVALID_METHOD);
    }

    private static function mapUrlParamsToApiParams($url_params)
    {
        // get the api params from the url params
        $api_params = array();
        $api_params['apiparam1'] = isset_or($url_params['apiparam1'], '');
        $api_params['apiparam2'] = isset_or($url_params['apiparam2'], '');
        $api_params['apiparam3'] = isset_or($url_params['apiparam3'], '');
        $api_params['apiparam4'] = isset_or($url_params['apiparam4'], '');
        $api_params['apiparam5'] = isset_or($url_params['apiparam5'], '');
        $api_params['apiparam6'] = isset_or($url_params['apiparam6'], '');
        return $api_params;
    }

    private static function createApiPath($request_method, $params)
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

    private static function getApiEndpoint($apiendpoint)
    {
        switch ($apiendpoint)
        {
            default:
                return false;

            // system (public)
            case 'POS /signup/check'                   : return '\Flexio\Api\System::signupcheck';
            case 'POS /login'                          : return '\Flexio\Api\System::login';
            case 'POS /logout'                         : return '\Flexio\Api\System::logout';

            // search
            case 'GET /search'                         : return '\Flexio\Api\Search::exec';

            // users
            case 'GET /users/me'                       : return '\Flexio\Api\UserApi::about';

            case 'POS /users'                          : return '\Flexio\Api\UserApi::create';
            case 'POS /users/resetpassword'            : return '\Flexio\Api\UserApi::resetpassword';
            case 'POS /users/requestpasswordreset'     : return '\Flexio\Api\UserApi::requestpasswordreset';
            case 'POS /users/resendverify'             : return '\Flexio\Api\UserApi::resendverify';
            case 'POS /users/activate'                 : return '\Flexio\Api\UserApi::activate';
            case 'POS /users/:eid'                     : return '\Flexio\Api\UserApi::set';
            case 'GET /users/:eid'                     : return '\Flexio\Api\UserApi::get';
            case 'POS /users/:eid/changepassword'      : return '\Flexio\Api\UserApi::changepassword';
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

            // help
            case 'POS /help/conversation'              : return '\Flexio\Api\Help::createConversation';

            // test suite
            case 'GET /tests/configure'                : return '\Flexio\Tests\TestBase::configure';
            case 'GET /tests/run'                      : return '\Flexio\Tests\TestBase::run';

            // DEBUG: endpoints for easy debugging using a URL in a browser
            case 'GET /processes/debug'                : return '\Flexio\Api\Process::debug';
            case 'GET /users/createsampleproject'      : return '\Flexio\Api\UserApi::createSample';
            case 'GET /debug/config'                   : return '\Flexio\Api\System::configuration';
        }
    }

    private static function packageResponse($response, $request)
    {
        if ($request->getValidator()->hasErrors() === false)
            return $response;

        // set the header based on the type of error
        $errors = $request->getValidator()->getErrors();
        $last_error = end($errors);
        $last_error = self::convertErrorToApiCode($last_error);

        $last_error_code = $last_error['code'];

        // set the error string in the content
        // example:
        // {"errors": [{"message":"trouble", "code":"1"}]

        $result = array();
        $result['errors'] = array();

        $errors = $request->getValidator()->getErrors();
        foreach ($errors as $e)
        {
            $e = self::convertErrorToApiCode($e);
            $item = &$result['errors'][];
            $item['message'] = $e['message'];
            $item['code'] = $e['code'];
        }

        return $result;
    }

    private static function sendResponse($response, $request)
    {
        // set the headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        // if we have any errors, set to the appropriate http header error code
        if ($request->getValidator()->hasErrors())
        {
            // log any errors
            self::logErrorsIfDebugging($request);

            // set the header based on the type of error
            $errors = $request->getValidator()->getErrors();
            $last_error = end($errors);
            $last_error = self::convertErrorToApiCode($last_error);

            $last_error_code = $last_error['code'];

            switch ($last_error_code)
            {
                // TODO: for now, map the default and ERROR_GENERAL to 400, which
                // is what we've been using for all error codes up 'till now; however,
                // we may want to switch to 500, in which case we'll need to properly
                // assign ERROR_GENERAL to other categories when appropriate
                default:
                case Api::ERROR_GENERAL:
                    \Flexio\System\Util::header_error(400);
                    break;

                // "UNAUTHORIZED" type errors; the user might have access to the object
                // if they were logged in, but the session is invalid
                case Api::ERROR_UNAUTHORIZED:
                    \Flexio\System\Util::header_error(401);
                    break;

                // "FORBIDDEN" type errors; access not allowed
                case Api::ERROR_INSUFFICIENT_RIGHTS:
                    \Flexio\System\Util::header_error(403);
                    break;

                // "NOT FOUND" type errors; invalid requests, invalid
                // parameters, or valid requests for objects that can't
                // be found
                case Api::ERROR_INVALID_VERSION:
                case Api::ERROR_INVALID_METHOD:
                case Api::ERROR_INVALID_REQUEST:
                case Api::ERROR_MISSING_PARAMETER:
                case Api::ERROR_INVALID_PARAMETER:
                case Api::ERROR_NO_DATABASE:
                case Api::ERROR_NO_MODEL:
                case Api::ERROR_NO_SERVICE:
                case Api::ERROR_NO_OBJECT:
                    \Flexio\System\Util::header_error(404);
                    break;

                // "UNPROCESSABLE ENTITY"; request can't be processed
                // for some reason
                case Api::ERROR_CREATE_FAILED:
                case Api::ERROR_DELETE_FAILED:
                case Api::ERROR_WRITE_FAILED:
                case Api::ERROR_READ_FAILED:
                case Api::ERROR_SIZE_LIMIT_EXCEEDED:
                    \Flexio\System\Util::header_error(422);
                    break;

                // "INTERNAL SERVER ERROR"; something is wrong internally
                case Api::ERROR_UNDEFINED:
                case Api::ERROR_UNIMPLEMENTED:
                case Api::ERROR_NO_DATABASE:
                case Api::ERROR_NO_MODEL:
                case Api::ERROR_NO_SERVICE:
                    \Flexio\System\Util::header_error(500);
                    break;
            }
        }

        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function logErrorsIfDebugging($request)
    {
        if (!isset($GLOBALS['g_config']->debug_error_log))
            return;

        if ($request->getValidator()->hasErrors() === false)
            return;

        ob_start();
        debug_print_backtrace();
        $data = ob_get_clean();

        $errors = $request->getValidator()->getErrors();
        foreach ($errors as $e)
        {
            $code = $e['code'];
            $message = $e['message'];
            self::convertErrorToApiCode($code, $message);

            if (is_array($message))
                $message = json_encode($message);

            file_put_contents($GLOBALS['g_config']->debug_error_log, "API error '$message' set!\n$data\n\n", FILE_APPEND);
        }
    }

    private static function convertErrorToApiCode($error)
    {
        // converts any validator error to an api error; populates
        // any empty message with an api code

        // make sure we have an error object we can map
        if (!is_array($error))
            return $error;
        if (!isset($error['code']))
            return $error;

        // TODO: verify that following logic will correctly handle expired sessions
        if ($error['code'] === Api::ERROR_INSUFFICIENT_RIGHTS && self::sessionAuthExpired() === true)
            $error['code'] = Api::ERROR_UNAUTHORIZED;

        $code = $error['code'];
        $message = $error['message'];

        // make sure any validator codes are represented as
        // an api code
        switch ($code)
        {
            case \Flexio\System\Validator::ERROR_NONE:              $code = Api::ERROR_NONE;              break;
            case \Flexio\System\Validator::ERROR_UNDEFINED:         $code = Api::ERROR_UNDEFINED;         break;
            case \Flexio\System\Validator::ERROR_GENERAL:           $code = Api::ERROR_GENERAL;           break;
            case \Flexio\System\Validator::ERROR_INVALID_SYNTAX:    $code = Api::ERROR_INVALID_SYNTAX;    break;
            case \Flexio\System\Validator::ERROR_MISSING_PARAMETER: $code = Api::ERROR_MISSING_PARAMETER; break;
            case \Flexio\System\Validator::ERROR_INVALID_PARAMETER: $code = Api::ERROR_INVALID_PARAMETER; break;
        }

        // if a message isn't specified, supply a default message
        if (!isset($message) || strlen($message) == 0)
        {
            // try to map the code to suitable api error message
            switch ($code)
            {
                default:                                $message = _('Operation failed');            break;
                case Api::ERROR_UNDEFINED:              $message = _('Operation failed');            break;
                case Api::ERROR_GENERAL:                $message = _('General error');               break;
                case Api::ERROR_UNIMPLEMENTED:          $message = _('Unimplemented');               break;
                case Api::ERROR_NO_DATABASE:            $message = _('Database not available');      break;
                case Api::ERROR_NO_MODEL:               $message = _('Model not available');         break;
                case Api::ERROR_NO_SERVICE:             $message = _('Service not available');       break;
                case Api::ERROR_NO_OBJECT:              $message = _('Object not available');        break;
                case Api::ERROR_INVALID_SYNTAX:         $message = _('Invalid syntax');              break;
                case Api::ERROR_MISSING_PARAMETER:      $message = _('Missing parameter');           break;
                case Api::ERROR_INVALID_PARAMETER:      $message = _('Invalid parameter');           break;
                case Api::ERROR_CREATE_FAILED:          $message = _('Could not create object');     break;
                case Api::ERROR_DELETE_FAILED:          $message = _('Could not delete object');     break;
                case Api::ERROR_WRITE_FAILED:           $message = _('Could not write to object');   break;
                case Api::ERROR_READ_FAILED:            $message = _('Could not read from object');  break;
                case Api::ERROR_UNAUTHORIZED:           $message = _('Unauthorized');                break;
                case Api::ERROR_INSUFFICIENT_RIGHTS:    $message = _('Insufficient rights');         break;
                case Api::ERROR_SIZE_LIMIT_EXCEEDED:    $message = _('Size limit exceeded');         break;
            }
        }

        // set the code and message on the object and return it
        $error['code'] = $code;
        $error['message'] = $message;

        return $error;
    }

    private static function parseRequestString($params)
    {
        // only convert strings
        if (!is_string($params))
            return $params;

        // parse the input string as a URL and reset the params
        $components = parse_url($params);

        // get the endpoint components from the path
        $url_params = array();
        if (isset($components['path']))
        {
            $parts = explode('/', $components['path'], 6);

            if (isset($parts[2])) $url_params['apiversion'] = $parts[2];
            if (isset($parts[3])) $url_params['apiparam1'] = $parts[3];
            if (isset($parts[4])) $url_params['apiparam2'] = $parts[4];
            if (isset($parts[5])) $url_params['apiparam3'] = $parts[5];
            if (isset($parts[6])) $url_params['apiparam4'] = $parts[6];
        }

        // get the query parameters; make sure any api-related params
        // are stripped out
        $query_params = array();
        if (isset($components['query']))
        {
            parse_str($components['query'], $query_params);
            unset($query_params['apiversion']);
            unset($query_params['apiparam1']);
            unset($query_params['apiparam2']);
            unset($query_params['apiparam3']);
            unset($query_params['apiparam4']);
        }

        // reset the parameters with the parsed values
        $result = array_merge($url_params, $query_params);
        return $result;
    }

    private static function sessionAuthExpired()
    {
        return (isset($_COOKIE['FXSESSID']) && strlen($_COOKIE['FXSESSID']) > 0 && $GLOBALS['g_store']->user_eid == '') ? true:false;
    }
}
