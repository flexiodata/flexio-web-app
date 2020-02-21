<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-10
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class ProcessHandler
{
    public static function incrementProcessCount(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        // try to increment the active process count; if we're unable to, then the user is as the
        // maximum number of processes, so a little and then try again

        $current_attempt = 0;
        $max_attempts = 10*60*5; // allow 5-minutes-worth of attempts (at 10 per second)

        while (true)
        {
            $success = $process_host->getStore()->incrementActiveProcessCount();
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

    public static function decrementProcessCount(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        $process_host->getStore()->decrementActiveProcessCount();
    }

    public static function addMountParams(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        // callback function to add parameters from mounts for functions that
        // are mounted

        // get the process info from the process object; if the process doesn't
        // have a pipe parent, there's no mount info to add
        $process_obj_info = $process_host->getStore()->get();

        // load the pipe parent, which is the mount connection; if we're unable
        // to load the mount because it doesn't exist, there's nothing to
        // populate; note: this will also silently return where parent eids do
        // exist, but they can't be loaded; downstream logic that requires the
        // mount parameters will fail, but may want to cut-it-off here
        try
        {
            $pipe_eid = $process_obj_info['parent']['eid'] ?? '';
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
                $requesting_user_eid = $process_host->getEngine()->getOwner();
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
        $user_variables = $process_host->getEngine()->getParams();
        $process_host->getEngine()->setParams(array_merge($user_variables, $mount_variables));
    }

    public static function saveStdoutToProcessOutputStream(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        // get the stdout stream
        $stdout_stream = $process_host->getEngine()->getStdout();

        // create a stream to store the stdout
        $properties['path'] = \Flexio\Base\Util::generateHandle();
        $properties['owned_by'] = $process_host->getStore()->getOwner();
        $properties = array_merge($stdout_stream->get(), $properties);
        $storable_stream = \Flexio\Object\Stream::create($properties);

        // copy from the input stream to the storable stream
        $streamreader = $stdout_stream->getReader();
        $streamwriter = $storable_stream->getWriter();

        if ($stdout_stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
                $streamwriter->write($row);
        }
        else
        {
            while (($data = $streamreader->read(32768)) !== false)
                $streamwriter->write($data);
        }

        $process_params = array();
        $process_params['output'] = array('stream' => $storable_stream->getEid());
        $process_host->getStore()->set($process_params);
    }

    public static function saveStdoutToStream(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($callback_params, array(
                'stream_eid'  => array('type' => 'eid',     'required' => true)
            ))->hasErrors()) === true)
        {
            // note: parameters are internal, so proper error is write failing
            // as opposed to invalid parameters
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $validated_params = $validator->getParams();
        $stream_eid = $validated_params['stream_eid'];

        // get the stream output
        $stdout_stream = $process_host->getEngine()->getStdout();
        $stdout_stream_info = $stdout_stream->get();

        // copy the stdout stream info to the storable_stream
        $storable_stream = \Flexio\Object\Stream::load($stream_eid);
        $storable_stream_info_updated = array(
            'mime_type' => $stdout_stream_info['mime_type'],
            'structure' => $stdout_stream_info['structure']
        );
        $storable_stream->set($storable_stream_info_updated);

        // copy from the input stream to the storable stream
        $streamreader = $stdout_stream->getReader();
        $streamwriter = $storable_stream->getWriter();

        if ($stdout_stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
                $streamwriter->write($row);
        }
         else
        {
            while (($data = $streamreader->read(32768)) !== false)
                $streamwriter->write($data);
        }
    }

    public static function saveStdoutToElasticSearch(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($callback_params, array(
                'parent_eid' => array('type' => 'eid',    'required' => true), // the parent object (pipe) that the cahce is associated with
                'structure'  => array('type' => 'object', 'required' => true)  // structure of the data for the index
            ))->hasErrors()) === true)
        {
            // note: parameters are internal, so proper error is write failing
            // as opposed to invalid parameters
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $validated_params = $validator->getParams();
        $parent_eid = $validated_params['parent_eid'];
        $structure = $validated_params['structure'];
        $structure = \Flexio\Base\Structure::create($structure);

        // connect to elasticsearch
        $elasticsearch_connection_info = \Flexio\System\System::getSearchCacheConfig();
        if ($elasticsearch_connection_info['type'] !== 'elasticsearch')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Search not available");
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);

        // create a new index; delete any index that's already there
        $elasticsearch->deleteIndex($parent_eid);
        $elasticsearch->createIndex($parent_eid, $structure->get());

        // get the data from stdout; TODO: make this more efficient with memory
        $data_to_write = '';
        $stdout_reader= $process_host->getEngine()->getStdout()->getReader();
        while (($chunk = $stdout_reader->read(32768)) !== false)
        {
            $data_to_write .= $chunk;
        }
        $data_to_write = json_decode($data_to_write, true);

        if (!is_array($data_to_write))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // write the output to elasticsearch
        $params = array(
            'path' => $parent_eid, // service uses path for consistency with other services
            'structure' => $structure
        );
        $field_names = $structure->getNames();
        $elasticsearch->write($params, function() use (&$data_to_write, &$field_names) {
            $row = array_shift($data_to_write);
            if (!$row)
                return false;
            $row = array_combine($field_names, $row); // return key/value
            return $row;
        });
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
