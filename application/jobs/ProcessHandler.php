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
        // manually in a queue
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
        // are mounted; these param items are the items entered by the user
        // when they configure the mount

        $requesting_user_eid = $process->getOwner();
        $connection_eid = $callback_params['connection_eid'] ?? '';

        // get the mount parameters and set the connection mount
        $mount_variables = self::getMountParams($requesting_user_eid, $connection_eid);
        $process->setMount($connection_eid);

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

        if ($stdout_mime_type === \Flexio\Base\ContentType::CSV)
        {
            // convert csv to ndjson and insert it

            // read the file to get the info
            $process_engine = \Flexio\Jobs\Process::create();
            $process_engine->setStdin($process->getStdout());
            $process_engine->setOwner($process->getOwner());
            $process_engine->queue('\Flexio\Jobs\Convert::run', array('input' => array('format' => 'csv'), 'output' => array('format' => 'ndjson')));
            $process_engine->run();
            $converted_stdout_reader = $process_engine->getStdout()->getReader();

            // write the output to elasticsearch
            $params = array(
                'path' => $index // service uses path for consistency with other services
            );
            $elasticsearch->write($params, function() use (&$converted_stdout_reader) {
                $row = $converted_stdout_reader->readline();
                if ($row === false)
                    return false;
                // TODO: coerce row types?
                $row = json_decode($row, true);
                return $row;
            });
        }

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

    public static function getMountAuthenticator(string $requesting_user_eid, string $connection_eid) : ?string
    {
        // return any authenticating function callbacks from the mount setup info
        try
        {
            $connection = \Flexio\Object\Connection::load($connection_eid);
            $connection_info = $connection->get();

            $setup_template = $connection_info['setup_template'];
            if (isset($setup_template['authentication']))
            {
                // TODO: need to get the name of the pipe created from the manifest,
                // so we can run that, not just the path in the template; however,
                // these names, by convention, are almost always the same (e.g
                // funcname.py vs funcname) so as a shortcut for now, trim off
                // any trailing extension
                $filename = $setup_template['authentication'];
                $funcname = \Flexio\Base\File::getFilename($filename);
                return $funcname;
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            // fall through
        }

        return null;
    }

    public static function getMountParams(string $requesting_user_eid, string $connection_eid) : array
    {
        // load the pipe parent, which is the mount connection; if we're unable
        // to load the mount because it doesn't exist, there's nothing to
        // populate; note: this will also silently return where parent eids do
        // exist, but they can't be loaded; downstream logic that requires the
        // mount parameters will fail, but may want to cut-it-off here

        $mount_variables = array();
        $connection_info = array();

        // STEP 1: get the connection info
        try
        {
            $connection = \Flexio\Object\Connection::load($connection_eid);
            $connection_info = $connection->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
            return array();
        }

        // STEP 2: add the configuration information from the setup template;
        // these are the fixed configuraton items defined in a mount/integration
        // manifest file
        $setup_template = $connection_info['setup_template'];
        if (isset($setup_template['config']) && is_array($setup_template['config']))
        {
            $config_items = $setup_template['config'];
            foreach ($config_items as $c)
            {
                $name = $c['name'] ?? false;
                $value = $c['value'] ?? false;

                if ($name === false || $value === false)
                    continue;

                $mount_variables[$name] = $value;
            }
        }

        // STEP 3: add the configuration information from the setup config;
        // these are the user-defined configuration items entered in the prompts
        // created by running the setup template when adding a mount or
        // integration
        $setup_config = $connection_info['setup_config'];
        if (is_array($setup_config))
        {
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

                // if we have an eid, try to load the appropriate connection and pass
                // on the connection info
                try
                {
                    $connection = \Flexio\Object\Connection::load($mount_item_eid);
                    if ($connection->getStatus() !== \Model::STATUS_AVAILABLE)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
                    if ($connection->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

                    $service = $connection->getService(); // getting the service refreshes tokens
                    $connection_info = $service->get();
                    $mount_variables[$key] = $connection_info;
                }
                catch (\Flexio\Base\Exception $e)
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
                }
            }
        }

        return $mount_variables;
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
