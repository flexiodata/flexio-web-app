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
    public static function callbackAddMountParams(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        // callback function to add parameters from mounts for functions that
        // are mounted;

        try
        {
            // get the process info from the process object; if the process doesn't
            // have a pipe parent, there's no mount info to add
            $process_obj_info = $process_host->getStore()->get();
            if (!isset($process_obj_info['parent']['eid']))
                return;

            // load the pipe parent, which is the mount connection
            $pipe_eid = $process_obj_info['parent']['eid'];
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            $pipe_info = $pipe->get();

            if (!isset($pipe_info['parent']['eid']))
                return;

            $connection_eid = $pipe_info['parent']['eid'];
            $connection = \Flexio\Object\Connection::load($connection_eid);
            $connection_info = $connection->get();

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
                    $requesting_user_eid = $this->engine->getOwner();
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
                    // don't do anything
                }
            }

            // merge the mount variables into the existing parameters
            $user_variables = $process_host->getEngine()->getParams();
            $process_host->getEngine()->setParams(array_merge($user_variables, $mount_variables));
        }
        catch (\Exception $e)
        {
        }
    }

    public static function callbackStreamLoad(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($callback_params, array(
                'stream_eid'  => array('type' => 'eid',     'required' => true)
            ))->hasErrors()) === true)
        {
            // TODO: set process error
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
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

    public static function callbackElasticSearchLoad(\Flexio\Jobs\ProcessHost $process_host, array $callback_params) : void
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($callback_params, array(
                'structure'  => array('type' => 'object',     'required' => true)
            ))->hasErrors()) === true)
        {
            // TODO: set process error
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        $validated_params = $validator->getParams();
        $structure = $validated_params['structure'];
        $structure = \Flexio\Base\Structure::create($structure);

        // get the stream output
        $stdout_stream = $process_host->getEngine()->getStdout();
        $stdout_stream_info = $stdout_stream->get();

        // connect to elasticsearch
        $elasticsearch_connection_info = array(
            'host'     => $GLOBALS['g_config']->experimental_cache_host ?? '',
            'port'     => $GLOBALS['g_config']->experimental_cache_port ?? '',
            'username' => $GLOBALS['g_config']->experimental_cache_username ?? '',
            'password' => $GLOBALS['g_config']->experimental_cache_password ?? ''
        );
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);
        $structure = $callback_params['structure'];
        $field_names = $structure = $structure->getNames();

        $stdout_reader= $process_host->getEngine()->getStdout()->getReader();
        $data = $stdout_reader->read(32768);
        $data = json_decode($data, true);

        $data_to_write = array();
        foreach ($data as $d)
        {
            $row = array_combine($field_names, $d);
            $data_to_write[] = $row;
        }

        $index = \Flexio\Base\Util::generateHandle();
        $type = 'row';
        $elasticsearch->writeRows($index, $type, $data_to_write);
    }
}
