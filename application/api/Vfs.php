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
        // provides a sample of a file in table form;
        // TODO: this function is very similar to the extract job; factor?

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

        // read the file to get the info
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->setOwner($owner_user_eid);
        $process_engine->queue('\Flexio\Jobs\Read::run', array('path' => $path));
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::chain', array());
        $process_engine->queue('\Flexio\Jobs\Convert::run', array('input' => array(), 'output' => array('format' => 'ndjson')));
        $process_engine->run();

        if ($process_engine->hasError())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $local_stdout = $process_engine->getStdout();

        // get the columns
        $columns = array();
        $column_info = $local_stdout->getStructure()->get();
        foreach ($column_info as $c)
        {
            $item = array();
            $item['name'] = $c['name'];
            $item['type'] = $c['type'];
            $item['width'] = $c['width'];
            $item['scale'] = $c['scale'];
            $columns[] = $item;
        }

        // get a small sample of the rows
        $rows = array();
        $idx = 0;
        $limit = 10;

        $column_names= $local_stdout->getStructure()->getNames();
        $column_names = array_flip($column_names);
        $reader = $local_stdout->getReader();
        while (true)
        {
            if ($idx >= $limit)
                break;

            $item = $reader->readline();
            if ($item === false)
                break;

            // get the key/value info for the rows
            $item = json_decode($item, true);

            // get the values corresponding to the headers
            $item_values = array_values(\Flexio\Base\Util::mapArray($column_names, $item));
            $rows[] = $item_values;
            $idx++;
        }

        // return the columns and rows
        $result = array();
        $result['columns'] = $columns;
        $result['rows'] = $rows;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }
}
