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
    // TODO: add callback handlers to configure processes

    public static function callbackStreamLoad(\Flexio\Jobs\ProcessHost $process_host, array $callback_params)
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

    public static function callbackElasticSearchLoad(\Flexio\Jobs\ProcessHost $process_host, array $callback_params)
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
