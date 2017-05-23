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

            $context->push($stream);
        }

        return $context;
    }

    public function copy() : \Flexio\Object\Context
    {
        // creates a new context with new objects for each of the
        // original objects (i.e., we'll have a context with new objects
        // and eids but with the same data as the copied objects)

        $context_copy = \Flexio\Object\Context::create();
        foreach ($this->objects as $object)
        {
            // try to copy the object; note: some objects can't be copied, so if
            // a context contains one of these objects, don't allow the context
            // to be copied
            $object_copy = $object->copy();
            $result = $context_copy->push($object_copy);
            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }

        return $context_copy;
    }

    public function set(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        // sets the context to the input context
        $this->objects = $context->getStreams();
        return $this;
    }

    public function merge(\Flexio\Object\Context $context) : \Flexio\Object\Context
    {
        // adds the items in the context to the existing context
        $context_objects = $context->getStreams();
        foreach ($context_objects as $object)
        {
            $this->objects[] = $object;
        }

        return $this;
    }

    public function push(\Flexio\Object\Base $object) : \Flexio\Object\Context
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

