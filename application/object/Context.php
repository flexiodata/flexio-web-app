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
        $result = array();

        $result['stdin'] = null;
        if (isset($this->stdin))
            $result['stdin'] = array('eid' => $this->stdin->getEid(), 'eid_type' => $this->stdin->getType());

        $result['stdout'] = null;
        if (isset($this->stdout))
            $result['stdout'] = array('eid' => $this->stdout->getEid(), 'eid_type' => $this->stdout->getType());

        $result['params'] = $this->params;
        $result['env'] = $this->env;
        $result['streams'] = array();

        foreach ($this->streams as $s)
        {
            $result['streams'][] = array('eid' => $s->getEid(), 'eid_type' => $s->getType());
        }

        return json_encode($result);
    }

    public static function fromString(string $str = null) : \Flexio\Object\Context
    {
        $context = self::create();
        if (!isset($str))
            return $context;

        $arr = json_decode($str, true);
        if ($arr === false)
            return $context;

        if (isset($arr['stdin']) && isset($arr['stdin']['eid']))
        {
            $stream = \Flexio\Object\Stream::load($arr['stdin']['eid']);
            if ($stream !== false)
                $context->setStdin($stream);
        }

        if (isset($arr['stdout']) && isset($arr['stdout']['eid']))
        {
            $stream = \Flexio\Object\Stream::load($arr['stdout']['eid']);
            if ($stream !== false)
                $context->setStdout($stream);
        }

        if (isset($arr['params']) && is_array($arr['params']))
            $context->setParams($arr['params']);

        if (isset($arr['env']) && is_array($arr['env']))
            $context->setEnv($arr['env']);

        if (isset($arr['streams']) && is_array($arr['streams']))
        {
            foreach ($arr['streams'] as $info)
            {
                $stream = \Flexio\Object\Stream::load($info['eid']);
                if ($stream !== false)
                    $context->addStream($stream);
            }
        }

        return $context;
    }

    public static function toString(\Flexio\Object\Context $context) : string
    {
        return $context->__toString();
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

    public function set(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        $this->streams = $context->getStreams();
        $this->environment = $context->getEnv();
        return $this;
    }

    public function setStdin(\Flexio\Object\Base $stream = null) : \Flexio\Object\Context
    {
        $this->stdin = $stream;
        return $this;
    }

    public function getStdin() // TODO: when we get memory streams, initialize stdin to stdin and always return a stream
    {
        return $this->stdin;
    }

    public function setStdout(\Flexio\Object\Base $stream = null) : \Flexio\Object\Context
    {
        $this->stdout = $stream;
        return $this;
    }

    public function getStdout() // TODO: when we get memory streams, initialize stdin to stdin and always return a stream
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

    public function clearStreams() : \Flexio\Object\Context
    {
        $this->streams = array();
        return $this;
    }

    public function clear() : \Flexio\Object\Context
    {
        $this->initialize();
        return $this;
    }

    private function initialize()
    {
        $this->stdin = null;
        $this->stdout = null;
        $this->params = array();
        $this->environment = array();
        $this->streams = array();
    }
}

