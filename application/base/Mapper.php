<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-22
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Mapper
{
    // class for converting between hierarchical objects and flat objects,
    // such as when converting json to a table

    private const DEFAULT_KEY_NAME = "field";
    private const KEY_DELIMITER = ".";

    public static function flatten($data, $schema = null, $key_delimiter = self::KEY_DELIMITER)
    {
        // if the input data is a string, try to convert it to an object;
        // if it can't be converted, use the string
        if (is_string($data))
        {
            $temp_data = json_decode($data);
            if (isset($temp_data))
                $data = $temp_data;
        }

        // if the schema is a string, try to convert it to an object
        if (is_string($schema))
        {
            $temp_schema = json_decode($schema);
            if (isset($temp_schema))
                $schema = $temp_schema;
        }

        // fail if the schema is defined and isn't an object
        if (isset($schema) && !is_object($schema))
            return false;

        $mapper = new Mapper;
        $flattened_object = $mapper->flatten_internal($data, $schema, $key_delimiter);
        return json_decode(json_encode($flattened_object),true);
    }

    private function flatten_internal($data, $schema, $key_delimiter, $node = null)
    {
        if (is_array($data))
            return $this->flatten_array($data, $schema, $key_delimiter, $node);

        if (is_object($data))
            return $this->flatten_object($data, $schema, $key_delimiter, $node);

        return $this->flatten_value($data, $schema, $key_delimiter, $node);
    }

    private function flatten_array($data, $schema, $key_delimiter, $node)
    {
        $flattened_array = array();

        if (!is_array($data))
            return $flattened_array;

        if (isset($node) && count($data) === 0)
        {
            $flattened_array[] = array($node => null); // similar to left join; key is preserved with a null
            return $flattened_array;
        }

        // iterate over the indexes
        foreach ($data as $value)
        {
            $flattened_objects = $this->flatten_internal($value, $schema, $key_delimiter, $node);
            foreach ($flattened_objects as $object)
            {
                $flattened_array[] = $object;
            }
        }

        return $flattened_array;
    }

    private function flatten_object($data, $schema, $key_delimiter, $node)
    {
        if (!is_object($data))
            return array();

        // if the object is empty, return a single row with an empty object
        if ($data == new \stdClass())
            return array($data);

        // iterate over the keys
        $flattened_object = array();
        foreach ($data as $key => $value)
        {
            // if a parent node is specified, prepend the node name to the key
            $new_node = $key;
            if (is_string($node))
                $new_node = $node . $key_delimiter . $key;

            // flatten the value
            $flattened_object_value = $this->flatten_internal($value, $schema, $key_delimiter, $new_node);

            // merge the flattened value in with the key/values of the object that have already been flattened
            // this is an equivalent to an outer join between the rows of objects from the flattened
            // object up to this point and the rows of objects from the currently flattened object value
            $flattened_object = self::outer_join($flattened_object, $flattened_object_value);
        }

        return $flattened_object;
    }

    private function flatten_value($data, $schema, $key_delimiter, $node)
    {
        $key = self::DEFAULT_KEY_NAME; // default key name

        // if the parent node is set, get the name of the field from the parent node
        if (isset($node) && is_string($node))
            $key = $node;

        // we have a single value; we need to convert it into an object
        // with a default key
        $flattened_object = new \stdClass();
        $flattened_object->$key = $data;

        $result = array();
        $result[] = $flattened_object;

        return $result;
    }

    private static function outer_join($flattened_object1, $flattened_object2)
    {
        // join two flattened objects (array of object values) together by
        // creating an array that merges every object in the first array with
        // every object in in the second array

        if (count($flattened_object1) === 0 && count($flattened_object2) === 0)
            return array();

        if (count($flattened_object1) === 0)
            return $flattened_object2;

        if (count($flattened_object2) === 0)
            return $flattened_object1;

        $result = array();

        foreach ($flattened_object1 as $obj1)
        {
            foreach ($flattened_object2 as $obj2)
            {
                $result[] = self::object_merge($obj1, $obj2);
            }
        }

        return $result;
    }

    private static function object_merge($object1, $object2)
    {
        $merged_object = new \stdClass;

        foreach ($object1 as $key => $value)
        {
            $merged_object->$key = $value;
        }

        foreach ($object2 as $key => $value)
        {
            $merged_object->$key = $value;
        }

        return $merged_object;
    }
}
