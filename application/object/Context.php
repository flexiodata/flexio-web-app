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


class Collection
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

    public static function create() : \Flexio\Object\Collection
    {
        return (new static);
    }

    public function copy() : \Flexio\Object\Collection
    {
        // creates a new collection with new objects for each of the
        // original objects (i.e., we'll have a collection with new objects
        // and eids but with the same data as the copied objects)

        $collection_copy = \Flexio\Object\Collection::create();
        foreach ($this->objects as $object)
        {
            // try to copy the object; note: some objects can't be copied, so if
            // a collection contains one of these objects, don't allow the collection
            // to be copied
            $object_copy = $object->copy();
            $result = $collection_copy->push($object_copy);
            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }

        return $collection_copy;
    }

    public function set(\Flexio\Object\Collection $collection) : \Flexio\Object\Collection
    {
        // sets the collection to the input collection
        $this->objects = $collection->enum();
        return $this;
    }

    public function merge(\Flexio\Object\Collection $collection) : \Flexio\Object\Collection
    {
        // adds the items in the collection to the existing collection
        $collection_objects = $collection->enum();
        foreach ($collection_objects as $object)
        {
            $this->objects[] = $object;
        }

        return $this;
    }

    public function push(\Flexio\Object\Base $object) : \Flexio\Object\Collection
    {
        // adds an object onto the end of the collection
        $this->objects[] = $object;
        return $this;
    }

    public function pop() : \Flexio\Object\Collection
    {
        // removes an item from the end of the collection
        array_pop($this->objects);
        return $this;
    }

    public function enum() : array
    {
        // returns the items in the collection
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

    public function find(string $name) // TODO: add return type
    {
        // returns the first object with a given name; if no object are found, returns false
        foreach ($this->objects as $object)
        {
            $properties = $object->get();
            if (isset($properties['name']))
            {
                if ($properties['name'] === $name)
                    return $object;
            }
        }

        return false;
    }

    public function clear() : \Flexio\Object\Collection
    {
        // removes all items from the collection
        $this->initialize();
        return $this;
    }

    private function initialize()
    {
        $this->objects = array();
        $this->env = array();
    }
}

