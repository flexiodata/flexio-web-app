<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-10
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class ProcessHandler
{
    // utility functions that can be run as functions in a process using queue, but
    // that don't have a full task associated with them

    public static function chain(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        // moves stdout to stdin and clears stdout; used for chaining jobs
        // manually in a queue, similar to what the sequence task does
        $new_stdout_stream = \Flexio\Base\Stream::create()->setMimeType(\Flexio\Base\ContentType::TEXT);
        $process->setStdin($process->getStdout());
        $process->setStdout($new_stdout_stream);
    }

    public static function incrementProcessCount(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        // try to increment the active process count; if we're unable to, then the user is as the
        // maximum number of processes, so a little and then try again

        $current_attempt = 0;
        $max_attempts = 10*60*5; // allow 5-minutes-worth of attempts (at 10 per second)

        while (true)
        {
            $owner_eid = $process->getOwner();
            $success = \Flexio\System\System::getModel()->user->incrementActiveProcessCount($owner_eid);
            if ($success == true)
                break;

            usleep(100000); // sleep 100ms

            $current_attempt++;
            if ($current_attempt < $max_attempts)
                continue;

            // if we can't increment the process count after a certain amount of time
            // throw an error
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::RATE_LIMIT_EXCEEDED);
        }
    }

    public static function decrementProcessCount(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        $owner_eid = $process->getOwner();
        \Flexio\System\System::getModel()->user->decrementActiveProcessCount($owner_eid);
    }

    public static function addMountParams(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        // callback function to add parameters from mounts for functions that
        // are mounted

        // load the pipe parent, which is the mount connection; if we're unable
        // to load the mount because it doesn't exist, there's nothing to
        // populate; note: this will also silently return where parent eids do
        // exist, but they can't be loaded; downstream logic that requires the
        // mount parameters will fail, but may want to cut-it-off here
        try
        {
            $pipe_eid = $callback_params['parent_eid'] ?? '';
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            $pipe_info = $pipe->get();

            $connection_eid = $pipe_info['parent']['eid'] ?? '';
            $connection = \Flexio\Object\Connection::load($connection_eid);
            $connection_info = $connection->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
            return;
        }

        $setup_config = $connection_info['setup_config'];
        if (!$setup_config)
            return;

        $mount_variables = array();
        foreach ($setup_config as $key => $value)
        {
            // note: setup config is a set of key/values; currently, values can
            // be either strings or oauth connection object; if we don't have a
            // connection object, simply pass on the value, otherwise, "dereference"
            // the connection and pass on the connection info

            // if we don't have an object identifier, simply pass on what's there
            $mount_item_eid = $value['eid'] ?? false;
            if ($mount_item_eid === false)
            {
                $mount_variables[$key] = $value;
                continue;
            }

            // if we have an eid, try to load the appropriate oauth service and
            // get the access token
            try
            {
                $requesting_user_eid = $process->getOwner();
                $connection = \Flexio\Object\Connection::load($mount_item_eid);
                if ($connection->getStatus() !== \Model::STATUS_AVAILABLE)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
                if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

                $service = $connection->getService(); // getting the service refreshes tokens
                $connection_info = $service->get();
                $connection_info = json_encode($connection_info);

                $stream = \Flexio\Base\Stream::create();
                $stream->setMimeType(\Flexio\Base\ContentType::FLEXIO_CONNECTION_INFO);
                $stream->buffer = (string)$connection_info; // shortcut to speed it up -- can also use getWriter()->write((string)$v)

                $mount_variables[$key] = $stream;
            }
            catch (\Flexio\Base\Exception $e)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            }
        }

        // merge the mount variables into the existing parameters
        $user_variables = $process->getParams();
        $process->setParams(array_merge($user_variables, $mount_variables));
    }

    public static function saveStdoutToElasticSearch(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($callback_params, array(
                'index' => array('type' => 'string', 'required' => true) // the name of the index to drop/create/write-to
            ))->hasErrors()) === true)
        {
            // note: parameters are internal, so proper error is write failing
            // as opposed to invalid parameters
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $validated_params = $validator->getParams();
        $index = $validated_params['index'];

        // create a new index; delete any index that's already there
        $elasticsearch = \Flexio\System\System::getSearchCache();
        $elasticsearch->deleteIndex($index);
        $elasticsearch->createIndex($index);

        // get the stdout mime type
        $stdout_stream_info = $process->getStdout()->get();
        if ($stdout_stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $stdout_mime_type = $stdout_stream_info['mime_type'];

        if ($stdout_mime_type === \Flexio\Base\ContentType::NDJSON)
        {
            // handle json content type
            //   ["col1"=>"val1", "col2"=>"val2"]\n
            //   ["col1"=>"val3", "col2"=>"val4"]\n
            $stdout_reader = $process->getStdout()->getReader();

            // write the output to elasticsearch
            $params = array(
                'path' => $index // service uses path for consistency with other services
            );
            $elasticsearch->write($params, function() use (&$stdout_reader) {
                $row = $stdout_reader->readline();
                if ($row === false)
                    return false;
                // TODO: coerce row types?
                $row = json_decode($row, true);
                return $row;
            });
        }

        if ($stdout_mime_type === \Flexio\Base\ContentType::JSON)
        {
            // handle json content type
            // [
            //   ["col1"=>"val1", "col2"=>"val2"],
            //   ["col1"=>"val3", "col2"=>"val4"]
            // ]
            $stdout_reader= $process->getStdout()->getReader();

            $data_to_write = '';
            while (($chunk = $stdout_reader->read(32768)) !== false)
            {
                $data_to_write .= $chunk;
            }
            $data_to_write = json_decode($data_to_write, true);

            if (!is_array($data_to_write))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            // write the output to elasticsearch
            $params = array(
                'path' => $index // service uses path for consistency with other services
            );
            $elasticsearch->write($params, function() use (&$data_to_write) {
                $row = array_shift($data_to_write);
                if (!is_array($row))
                    return false;
                // TODO: coerce row types?
                return $row;
            });
        }
    }

    public static function addProcessInputFromStream($php_stream_handle, string $post_content_type, $process) : void
    {
        // note: this isn't a process handler, but is convenient to include here since
        // it uses a process and is related to the way processes are used in the API

        $stream = false;
        $streamwriter = false;
        $form_params = array();

        // first fetch query string parameters
        foreach ($_GET as $key => $value)
        {
            $form_params["query.$key"] = $value;
        }

        // \Flexio\Base\MultipartParser can handle application/x-www-form-urlencoded and /multipart/form-data
        // for all other types, we will take the post body and make it into the stdin

        $mime_type = $post_content_type;
        $semicolon_pos = strpos($mime_type, ';');
        if ($semicolon_pos !== false)
            $mime_type = substr($mime_type, 0, $semicolon_pos);
        if ($mime_type != 'application/x-www-form-urlencoded' && $mime_type != 'multipart/form-data')
        {
            $stream = \Flexio\Base\Stream::create();

            // post body is a data stream, post it as our pipe's stdin
            $first = true;
            $done = false;
            $streamwriter = null;
            while (!$done)
            {
                $data = fread($php_stream_handle, 32768);

                if ($data === false || strlen($data) != 32768)
                    $done = true;

                if ($first && $data !== false && strlen($data) > 0)
                {
                    $stream_info = array();
                    $stream_info['mime_type'] = $mime_type;
                    $stream->set($stream_info);
                    $streamwriter = $stream->getWriter();
                }

                if ($streamwriter)
                    $streamwriter->write($data);
            }

            $process->setParams($form_params);
            $process->setStdin($stream);

            return;
        }

        $size = 0;

        $parser = \Flexio\Base\MultipartParser::create();
        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params, &$size) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Base\Stream::create();

                if ($content_type === false)
                    $content_type = \Flexio\Base\ContentType::getMimeType($filename, '');

                $size = 0;

                $stream_info = array();
                $stream_info['name'] = $filename;
                $stream_info['mime_type'] = $content_type;

                $stream->set($stream_info);
                $streamwriter = $stream->getWriter();
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                    $size += strlen($data);
                }
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $streamwriter = false;
                $stream->setSize($size);
                $process->addFile($name, $stream);
                $stream = false;
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params['form.' . $name] = $data;
            }
        });
        fclose($php_stream_handle);

        $process->setParams($form_params);
    }
}
