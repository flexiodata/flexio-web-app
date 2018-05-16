<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-25
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Query
{
    public static function exec($eid, $query) : ?array
    {
        // \Query::exec() is an alias for \Query::get()
        return self::get($eid, $query);
    }

    public static function get($eid, $query) : ?array
    {
        // this function puts together information about the eid based
        // on the form of information specified in the query

        // for example, the following query would return the basic properties
        // for the specified comment as well as replies and owner info

        // $query = '
        // {
        //     "eid" : null,
        //     "eid_type" : "'.\Model::TYPE_COMMENT.'",
        //     "eid_status" : null,
        //     "comment" : null,
        //     "replies='.\Model::EDGE_HAS_COMMENT.'" : [{
        //         "eid" : null,
        //         "eid_type" : "'.\Model::TYPE_COMMENT.'",
        //         "comment" : null,
        //         "created" : null,
        //         "updated" : null
        //     }],
        //     "owned_by" : {
        //         "eid" : null,
        //         "eid_type" : "'.\Model::TYPE_USER.'",
        //         "username" : null,
        //         "first_name" : null,
        //         "last_name" : null,
        //         "email_hash" : null
        //     },
        //     "created" : null,
        //     "updated" : null
        // }
        // ';


        // make sure input query is an object
        if (!is_object($query))
            return null;

        if (!\Flexio\Base\Eid::isValid($eid))
            return null;

        return self::get_internal($eid, $query);
    }

    private static function get_internal($eid, $query) : ?array
    {
        // return object
        $result = array();
        $object = array();

        // get the properties for the object; match against any specified
        // conditions and return the specified fields; if the eid_type is
        // specified, save time by specifying the model

        $eid_type = '';
        $model = self::getModel();

        if (isset($query->eid_type) && !is_null($query->eid_type))
            $eid_type = $query->eid_type;
             else
            $eid_type = $model->getType($eid);

        if ($eid_type === \Model::TYPE_UNDEFINED)
        {
            $object = false; // equivalent to what Model::get() returns if an object isn't found
        }
         else
        {
            $model_str = self::getModelNameFromType($eid_type);
            $object = $model->$model_str->get($eid);
        }

        foreach($query as $property_name => $property_value)
        {
            // STEP 1: take a look at the property name; if there's an "equals"
            // sign, then the property name used in the output is an alias
            // for the property name as it stands in the database; otherwise,
            // it's the same
            $property_output_name = $property_name;
            $property_input_name = $property_name;
            $property_output_arr = explode('=',$property_name);

            if (count($property_output_arr) > 1 && strlen($property_output_arr[0]) > 0)
            {
                $property_output_name = $property_output_arr[0];
                $property_input_name = $property_output_arr[1];
            }

            // STEP 2: if the query property value isn't an object, then we
            // want the object we're querying to match the query property value;
            // if the query property value is "null", then the object matches on
            // this particular property; if the query property value isn't null,
            // then the object only matches on this property if the object property
            // value is the same as the query property value; if the object matches
            // on this value, add the property; if a property is specified that
            // doesn't exist, don't fail, but include the parameter as specified,
            // and keep trying to match on other properties

            if (!is_object($property_value) && !is_array($property_value))
            {
                    // if the property doesn't exist on the object, we're done
                if (!isset($object[$property_input_name]))
                {
                    $result[$property_output_name] = $property_value;
                    continue;
                }

                // if the property value is specified and it doesn't equal the value
                // that's set for the object, we're done
                $value = $object[$property_input_name];
                if (!is_null($property_value) && $property_value != $value)
                    return null;

                $result[$property_output_name] = $value;
                continue;
            }


            // STEP 3: if we're requesting an object, treat the property
            // value as an eid or an edge and try to get the object properties
            // for the corresponding eid or edge
            if (is_object($property_value) && !is_array($property_value))
            {
                if (isset($object[$property_input_name]))
                {
                    // STEP 3a: the input name exists as a property on the object;
                    // treat it as an eid and get the object properties

                    // set the object properties if we can load it
                    $value = self::get($object[$property_input_name], $property_value);
                    if (isset($value))
                        $result[$property_output_name] = $value;
                         else
                        $result[$property_output_name] = json_decode(json_encode($property_value),true);

                    continue;
                }
                else
                {
                    // STEP 3b: the input name doesn't exist as a property on the object;
                    // treat the input name as an edge and try to find the object via
                    // the edge; only take the first member
                    $edges = self::getModel()->assoc_range($eid, $property_input_name);
                    foreach($edges as $item)
                    {
                        // build the results for the child; append any object
                        // returned by the query
                        $child = self::get_internal($item['eid'], $property_value);

                        if (!is_null($child))
                            $result[$property_output_name] = $child;

                        // only take the first one
                        break;
                    }
                }
            }


            // STEP 4: handle array query parameter; if the query property value
            // is an array, we're looking for a set of associated edges
            if (is_array($property_value))
            {
                $result[$property_output_name] = array();

                // TODO: for now, only allow one child pattern match; take
                // the first element of the array as the template
                if (empty($property_value))
                    continue;

                $property_value = $property_value[0];
                $edges = self::getModel()->assoc_range($eid, $property_input_name);
                foreach($edges as $item)
                {
                    // build the results for the child; append any object
                    // returned by the query
                    $arr = &$result[$property_output_name];
                    $child = self::get_internal($item['eid'], $property_value);

                    if (!is_null($child))
                        $arr[] = $child;
                }
            }
        }

        return $result;
    }

    private static function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }

    private static function getModelNameFromType(string $type) : string
    {
        switch ($type)
        {
            default:
            case \Model::TYPE_UNDEFINED:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_MODEL);

            case \Model::TYPE_USER           : return 'user';
            case \Model::TYPE_PIPE           : return 'pipe';
            case \Model::TYPE_STREAM         : return 'stream';
            case \Model::TYPE_CONNECTION     : return 'connection';
            case \Model::TYPE_COMMENT        : return 'comment';
            case \Model::TYPE_PROCESS        : return 'process';
            case \Model::TYPE_TOKEN          : return 'token';
            case \Model::TYPE_RIGHT          : return 'right';
            case \Model::TYPE_ACTION         : return 'action';
        }
    }
}
