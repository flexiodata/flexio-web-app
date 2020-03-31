<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-19
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Vfs
{
    public static function list(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'q' => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();
        $path = $validated_query_params['q'] ?? '';

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $vfs = new \Flexio\Services\Vfs($owner_user_eid);
        $result = $vfs->list($path);

        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $request_url = urldecode($request->getUrl());
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $path = $request_url;

        $pos = strpos($path, '/vfs/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path, $pos+4);

        $is_data = false;
        $counter = 0;

        $vfs = new \Flexio\Services\Vfs($owner_user_eid);
        $vfs->read($path, function($data) use (&$is_data, &$counter) {
            if ($counter == 0)
            {
                if (is_array($data))
                {
                    $is_data = true;
                    header('Content-Type: application/json', true, 200);
                    echo '[';
                }
            }

            if (is_array($data))
            {
                if ($counter > 0)
                    echo ',';
                echo json_encode($data, JSON_UNESCAPED_SLASHES);
            }
             else
            {
                echo $data;
            }

            ++$counter;
        });

        if ($is_data)
            echo ']';

        exit(0);
    }

    public static function put(\Flexio\Api\Request $request) : void
    {
        $request_url = $request->getUrl();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object; TODO: right now, govern VFS write rights using write
        // permissions on the user; perhaps use user privileges on store/streams?
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_CREATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $path = $request_url;

        $pos = strpos($path, '/vfs/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path, $pos+4);

        $vfs = new \Flexio\Services\Vfs($owner_user_eid);

        $headers = $request->getHeaderParams();
        $headers = array_change_key_case($headers, $case = CASE_LOWER);
        if (($headers['content-type'] ?? '') == \Flexio\Base\ContentType::FLEXIO_FOLDER)
        {
            $success = $vfs->createDirectory($path);
            $result = array('success' => $success);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);
            return;
        }

        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $done = false;
        $vfs->write($path, function($len) use (&$php_stream_handle, &$done) {
            if ($done)
                return false;
            $buf = fread($php_stream_handle, $len);
            if (strlen($buf) != $len)
                $done = true;
            return $buf;
        });

        fclose($php_stream_handle);

        $result = array('success' => true);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function info(\Flexio\Api\Request $request) : void
    {
        // provides a sample of a file in table form

        $request_url = urldecode($request->getUrl());
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $path = parse_url($request_url, PHP_URL_PATH);

        $pos = strpos($path, '/vfs/info/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path, $pos+10);

        $task = \Flexio\Tests\Task::create([
            [
                "op" => "read",
                "path" => $path
            ],
            [
                "op" => "convert",
                "input" => [
                   // "format" => "delimited",
                   // "header" => true
                ],
                "output" => [
                    "format" => "table"
                ]
            ]
        ]);

        $local_process = \Flexio\Jobs\Process::create();
        $local_process->setOwner($owner_user_eid);
        $local_process->execute($task);
        $local_stdout = $local_process->getStdout();
        $column_info = $local_stdout->getStructure()->get();
        $row_info = \Flexio\Base\StreamUtil::getStreamContents($local_stdout, 1, 10);

        // get the columns
        $columns = array();
        foreach ($column_info as $c)
        {
            $item = array();
            $item['name'] = $c['name'];
            $item['type'] = $c['type'];
            $item['width'] = $c['width'];
            $item['scale'] = $c['scale'];
            $columns[] = $item;
        }

        // get a sample of rows values
        $rows = array();
        foreach ($row_info as $r)
        {
            // return rows as an array of objects since this is what
            // the preview grid in the UI expects
            $rows[] = $r;
        }

        $result = array();
        $result['columns'] = $columns;
        $result['rows'] = $rows;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function exec(\Flexio\Api\Request $request) : void
    {
        // EXPERIMENTAL API endpoint: creates and runs a process straight from a file on vfs

        $request_url = $request->getUrl();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // TODO: pass on query/post params to the execute job
        //$query_params = $request->getQueryParams();
        //$post_params = $request->getPostParams();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // TODO: check that user is within usage limits; should this be factored out into a separate object along with rights?
        if ($owner_user->processUsageWithinLimit() === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);

        // get the file to excecute from the vfs path
        $path = parse_url($request_url, PHP_URL_PATH);

        $pos = strpos($path, '/run');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // grab path, including preceding slash
        $path = substr($path, $pos+4);

        // get the file extension and set the language
        $language = false;
        $ext = strtolower(\Flexio\Base\File::getFileExtension($path));
        if ($ext === 'flexio')
            $language = 'flexio'; // execute content as a JSON pipe
        if ($ext === 'py')
            $language = 'python';
        if ($ext === 'js')
            $language = 'nodejs';

        if ($language === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, 'Unsupported language');

        // get the content
        $content = '';
        $vfs = new \Flexio\Services\Vfs($owner_user_eid);
        $vfs->read($path, function($data) use (&$content) {
            $content .= $data;
        });


        // create a new process job with the default task;
        // if the process is created with a request from an api token, it's
        // triggered with an api; if there's no api token, it's triggered
        // from a web session, in which case it's triggered by the UI;
        // TODO: this will work until we allow processes to be created from
        // public pipes that don't require a token
        $triggered_by = strlen($request->getToken()) > 0 ? \Model::PROCESS_TRIGGERED_API : \Model::PROCESS_TRIGGERED_INTERFACE;

        // set the owner based on the owner being posted to
        $process_params = array();
        $process_params['owned_by'] = $owner_user_eid;
        $process_params['created_by'] = $requesting_user_eid;
        $process_params['triggered_by'] = $triggered_by;

        // set the task info
        if ($language === 'flexio')
        {
            $task = array();
            $pipe = @json_decode($content,true);
            if (!is_null($pipe))
                $task = $pipe['task'] ?? array();
            $process_params['task'] = $task;
        }
        else
        {
            $execute_job_params = array();
            $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied
            $execute_job_params['lang'] = $language; // TODO: set the language from the extension
            $execute_job_params['code'] = base64_encode($content); // encode the script

            $process_params['task'] = [
                "op" => "sequence",
                "items" => [
                    $execute_job_params
                ]
            ];
        }

        // create a new process object for storing process info
        $process_store = \Flexio\Object\Process::create($process_params);

        // create a new process engine for running a process
        $process_engine = \Flexio\Jobs\Process::create();
        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        \Flexio\Api\ProcessHandler::addProcessInputFromStream($php_stream_handle, $post_content_type, $process_engine);

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(false  /*true: run in background*/);

        // return the result
        if ($process_engine->hasError())
        {
            $error = $process_engine->getError();
            \Flexio\Api\Response::sendError($error);
            exit(0);
        }

        $stream = $process_engine->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
        $response_code = $process_engine->getResponseCode();

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON, true, $response_code);
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        \Flexio\Api\Response::sendRaw($content);
    }
}
