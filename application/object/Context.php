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
    private $objects;
    private $env;

    public function __construct()
    {
        $this->initialize();
    }

    public function __toString()
    {
        $items = array();
        foreach ($this->objects as $o)
        {
            $items[] = array('eid' => $o->getEid(), 'eid_type' => $o->getType());
        }

        return json_encode($items);
    }

    public static function create() : \Flexio\Object\Context
    {
        return (new static);
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
        // sets the context to the input context
        $this->objects = $context->getStreams();
        return $this;
    }

    public function merge(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        // TODO: merge environmnet variables

        // adds the items in the context to the existing context
        $context_objects = $context->getStreams();
        foreach ($context_objects as $object)
        {
            $this->objects[] = $object;
        }

        return $this;
    }

    public function addStream(\Flexio\Object\Base $object) : \Flexio\Object\Context
    {
        // adds an object onto the end of the context
        $this->objects[] = $object;
        return $this;
    }

    public function getStreams() : array
    {
        // returns the streams in the context
        return $this->objects;
    }

    public function getEnv() : array
    {
        // returns context environment variables
        return $this->env;
    }

    public function setEnv(array $arr)
    {
        $this->env = array_merge($this->env, arr);
    }

    public function clear() : \Flexio\Object\Context
    {
        // removes all items from the context
        $this->initialize();
        return $this;
    }

    private function initialize()
    {
        $this->objects = array();
        $this->env = array();
    }
}

