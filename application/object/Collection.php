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


namespace Flexio\Object;


class Collection
{
    private $objects;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create()
    {
        return (new static);
    }

    public function copy()
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

    public function set($collection)
    {
        // sets the collection to the input collection
        if (!($collection instanceof \Flexio\Object\Collection))
            return false;

        $this->objects = $collection->enum();
        return $this;
    }

    public function merge($collection)
    {
        // adds the items in the collection to the existing collection
        if (!($collection instanceof \Flexio\Object\Collection))
            return false;

        $collection_objects = $collection->enum();
        foreach ($collection_objects as $object)
        {
            $this->objects[] = $object;
        }

        return $this;
    }

    public function push($object)
    {
        // adds an object onto the end of the collection

        // if we have an object, add it
        if (is_object($object) && is_subclass_of($object,'\Flexio\Object\Base'))
        {
            $this->objects[] = $object;
            return $this;
        }

        // if we have an eid, try to load it and then add it
        if (\Flexio\Base\Eid::isValid($object))
        {
            $object_eid = $object;
            $object = \Flexio\Object\Store::load($object_eid);

            if (is_subclass_of($object,'\Flexio\Object\Base'))
            {
                $this->objects[] = $object;
                return $this;
            }
        }

        // input is invalid
        return false;
    }

    public function pop()
    {
        // removes an item from the end of the collection
        array_pop($this->objects);
        return $this;
    }

    public function enum()
    {
        // returns the items in the collection
        return $this->objects;
    }

    public function find($name)
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

    public function clear()
    {
        // removes all items from the collection
        $this->initialize();
        return $this;
    }

    private function initialize()
    {
        $this->objects = array();
    }
}

