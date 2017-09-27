<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-06
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Context
{
    private $stdin;
    private $stdout;
    private $params;
    private $env;
    private $streams;

    public function __construct()
    {
        $this->initialize();
    }

    public function __toString()
    {
        $items = array();
        foreach ($this->streams as $s)
        {
            $items[] = array('eid' => $s->getEid(), 'eid_type' => $s->getType());
        }

        return json_encode($items);
    }

    public static function create(array $properties = null) : \Flexio\Object\Context
    {
        return (new static);
    }

    public static function copy(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        $object = (new static);
        $object->stdin = $context->getStdin();
        $object->stdout = $context->getStdout();
        $object->params = $context->getParams();
        $object->env = $context->getEnv();
        $object->streams = $context->getStreams();
        return $object;
    }

    public static function stringifyCollectionEids(\Flexio\Object\Context $context) : string
    {
        $result = array();
        $stream_objects = $context->getStreams();
        foreach ($stream_objects as $stream)
        {
            $stream_eid = $stream->getEid();
            if ($stream_eid === false)
                continue;

            $result[] = array('eid' => $stream_eid);
        }

        return json_encode($result);
    }

    public static function unstringifyCollectionEids(string $string) : \Flexio\Object\Context
    {
        $context = \Flexio\Object\Context::create();
        $items = json_decode($string,true);
        if (!is_array($items))
            return $context;

        foreach ($items as $i)
        {
            $stream = \Flexio\Object\Stream::load($i['eid']);
            if ($stream === false)
                continue;

            $context->addStream($stream);
        }

        return $context;
    }

    public function set(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        $this->streams = $context->getStreams();
        $this->environment = $context->getEnv();
        return $this;
    }

    public function merge(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        // merge the streams
        $context_streams = $context->getStreams();
        foreach ($context_objects as $stream)
        {
            $this->streams[] = $stream;
        }

        // merge the environment variables
        $this->environment = array_merge($this->environment, $context->getEnv());

        return $this;
    }

    public function setStdin(\Flexio\Object\Base $stream) : \Flexio\Object\Context
    {
        $this->stdin = $stream;
        return $this;
    }

    public function getStdin(\Flexio\Object\Base $stream) // TODO: when we get memory streams, initialize stdin to stdin and always return a stream
    {
        return $this->stdin;
    }

    public function setStdout(\Flexio\Object\Base $stream) : \Flexio\Object\Context
    {
        $this->stdout = $stream;
        return $this;
    }

    public function getStdout(\Flexio\Object\Base $stream) // TODO: when we get memory streams, initialize stdin to stdin and always return a stream
    {
        return $this->stdout;
    }

    public function setParams(array $arr) : \Flexio\Object\Context
    {
        $this->params = $arr;
        return $this;
    }

    public function getParams() : array
    {
        return $this->params;
    }

    public function setEnv(array $arr) : \Flexio\Object\Context
    {
        $this->environment = $arr;
        return $this;
    }

    public function getEnv() : array
    {
        return $this->environment;
    }

    public function addStream(\Flexio\Object\Base $stream) : \Flexio\Object\Context
    {
        $this->streams[] = $stream;
        return $this;
    }

    public function getStreams() : array
    {
        // returns the streams in the context
        return $this->streams;
    }

    public function clear() : \Flexio\Object\Context
    {
        $this->initialize();
        return $this;
    }

    private function initialize()
    {
        $this->stdin = false;
        $this->stdout = false;
        $this->params = array();
        $this->environment = array();
        $this->streams = array();
    }
}

