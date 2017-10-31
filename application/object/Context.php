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


require_once dirname(__DIR__) . '/object/Abstract.php';


class Context
{
    private $stdin;
    private $stdout;
    private $params;
    private $streams;
    private $exit_code = null;

    public function __construct()
    {
        $this->initialize();
    }

    public function __toString()
    {
        $result = array();

        $result['stdin'] = null;
        if (isset($this->stdin))
        {
            $result['stdin'] = $this->stdin->get();
            if ($this->stdin instanceof \Flexio\Object\StreamMemory)
                $result['stdin']['buffer'] = $this->stdin->bufferToString();
        }

        $result['stdout'] = null;
        if (isset($this->stdout))
        {
            $result['stdout'] = $this->stdout->get();
            if ($this->stdout instanceof \Flexio\Object\StreamMemory)
                $result['stdout']['buffer'] = $this->stdout->bufferToString();
        }

        $result['params'] = $this->params;
        $result['streams'] = array();

        foreach ($this->streams as $s)
        {
            $properties = $s->get();

            if ($s instanceof \Flexio\Object\StreamMemory)
                $properties['buffer'] = $s->bufferToString();

            $result['streams'][] = $properties;
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

        if (isset($arr['stdin']))
        {
            if (isset($arr['stdin']['eid']))
            {
                // if we have an eid, we have a stream object so, load the info from the eid
                $stream = \Flexio\Object\Stream::load($arr['stdin']['eid']);
                if ($stream !== false)
                    $context->setStdin($stream);
            }
             else
            {
                // if we don't have an eid, we have a memory stream; load the info from what's saved
                $stream = \Flexio\Object\StreamMemory::create($arr['stdin']);
                $buffer = $arr['stdin']['buffer'];
                if ($stream !== false)
                {
                    if ($buffer !== false)
                        $stream->bufferFromString($buffer);
                    $context->setStdin($stream);
                }
            }
        }

        if (isset($arr['stdout']))
        {
            if (isset($arr['stdout']['eid']))
            {
                // if we have an eid, we have a stream object so, load the info from the eid
                $stream = \Flexio\Object\Stream::load($arr['stdout']['eid']);
                if ($stream !== false)
                    $context->setStdout($stream);
            }
             else
            {
                // if we don't have an eid, we have a memory stream; load the info from what's saved
                $stream = \Flexio\Object\StreamMemory::create($arr['stdout']);
                $buffer = $arr['stdout']['buffer'];
                if ($stream !== false)
                {
                    if ($buffer !== false)
                        $stream->bufferFromString($buffer);
                    $context->setStdout($stream);
                }
            }
        }

        if (isset($arr['params']) && is_array($arr['params']))
            $context->setParams($arr['params']);

        if (isset($arr['streams']) && is_array($arr['streams']))
        {
            foreach ($arr['streams'] as $info)
            {
                if (isset($info['eid']))
                {
                    // if we have an eid, we have a stream object so, load the info from the eid
                    $stream = \Flexio\Object\Stream::load($info['eid']);
                    if ($stream !== false)
                        $context->addStream($stream);
                }
                 else
                {
                    // if we don't have an eid, we have a memory stream, so load the info from what's saved
                    $stream = \Flexio\Object\StreamMemory::create($info);
                    $buffer = $info['buffer'];
                    if ($stream !== false)
                    {
                        if ($buffer !== false)
                            $stream->bufferFromString($buffer);
                        $context->addStream($stream);
                    }
                }
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
        $object->streams = $context->getStreams();
        return $object;
    }

    public function set(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        $this->streams = $context->getStreams();
        $this->params = $context->getParams();
        return $this;
    }

    public function setStdin(\Flexio\Object\IStream $stream = null) : \Flexio\Object\Context
    {
        $this->stdin = $stream;
        return $this;
    }

    public function getStdin() // TODO: when we get memory streams, initialize stdin to stdin and always return a stream
    {
        return $this->stdin;
    }

    public function setStdout(\Flexio\Object\IStream $stream = null) : \Flexio\Object\Context
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

    public function setExitCode(int $code)
    {
        $this->exit_code = $code;
    }

    public function getExitCode()
    {
        return $this->exit_code;
    }

    public function addStream(\Flexio\Object\IStream $stream) : \Flexio\Object\Context
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
        $this->streams = array();
        $this->exit_code = null;
    }
}

