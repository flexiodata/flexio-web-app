<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-17
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ValidatorSchema
{
    // this class is a partial implementation of the draft 4 JSON schema
    // validation specification:
    // http://tools.ietf.org/html/draft-fge-json-schema-validation-00

    // for additional information, see here:
    // https://github.com/json-schema/json-schema
    // http://json-schema.org/

    // TODO: implement:
    // additionalItems
    // uniqueItems
    // maxProperties
    // minProperties
    // additionalProperties
    // patternProperties
    // dependencies
    // allOf
    // anyOf
    // oneOf
    // not
    // definitions
    // default

    public const ERROR_UNDEFINED          =  '';
    public const ERROR_GENERAL            =  'general';
    public const ERROR_INVALID_SYNTAX     =  'invalid-syntax';
    public const ERROR_MISSING_PARAMETER  =  'missing-parameter';
    public const ERROR_INVALID_PARAMETER  =  'invalid-parameter';

    private $errors = array();

    public static function check($data, $schema) // TODO: add return type
    {
        // note: function to validate a schema and an object against a
        // schema; useful for validating both the data and schema when
        // the schema is not already validated

        // convert string schema to an object
        if (is_string($schema))
            $schema = json_decode($schema);

        $validator = new self;

        // check the schema; if we have a bad schema, we're done
        $validator->testSchema($schema);
        if ($validator->hasErrors())
            return $validator;

        // check the node and return the results
        $validator->testObject(null, $data, $schema);
        return $validator;
    }

    public static function checkObject($data, $schema) // TODO: add return type
    {
        // note: function to validate an object against a schema; useful
        // for validating data and schema when a schema is already validated

        // convert string schema to an object
        if (is_string($schema))
            $schema = json_decode($schema);

        // check the node and return the results
        $validator = new self;
        $validator->testObject(null, $data, $schema);
        return $validator;
    }

    public static function checkSchema($schema) // TODO: add return type
    {
        // note: function to validate a schema; useful for validating
        // a schema once before using it to check multiple objects

        // convert string schema to an object
        if (is_string($schema))
            $schema = json_decode($schema);

        // check the schema
        $validator = new self;
        $validator->testSchema($schema);
        return $validator;
    }

    public function setError($code, $message = null) : void
    {
        $this->errors[] = array('code' => $code, 'message' => $message);
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function hasErrors() : bool
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function clearErrors() : void
    {
        $this->errors = array();
    }

    private function testSchema($schema) : bool
    {
        // make sure schema is an object
        if (!is_object($schema))
        {
            $error_code = self::ERROR_INVALID_SYNTAX;
            $error_message = _('Validator schema is not an object type');
            $this->setError($error_code, $error_message);
            return false;
        }

        // check each of the properties
        foreach ($schema as $key => $value)
        {
            if ($key === 'title' && !is_string($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'title\' parameter is not a string'));

            if ($key === 'description' && !is_string($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'description\' parameter is not a string'));

            if ($key === 'type' && !is_string($value) && !is_array($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'type\' parameter is not a string or array'));

            if ($key === 'enum' && !is_array($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'enum\' parameter is not an array'));

            if ($key === 'minimum' && !is_numeric($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minimum\' parameter is not a number'));

            if ($key === 'maximum' && !is_numeric($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maximum\' parameter is not a number'));

            if ($key === 'multipleOf' && !is_numeric($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'multipleOf\' parameter is not a number'));

            if ($key === 'multipleOf' && is_numeric($value) && $value <= 0)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'multipleOf\' parameter must not be less than or equal to zero'));

            if ($key === 'exclusiveMinimum' && !is_bool($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'exclusiveMinimum\' parameter is not boolean'));

            if ($key === 'exclusiveMaximum' && !is_bool($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'exclusiveMaximum\' parameter is not boolean'));

            if ($key === 'minLength' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minLength\' parameter is not an integer'));

            if ($key === 'maxLength' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maxLength\' parameter is not an integer'));

            if ($key === 'pattern' && !is_string($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'pattern\' parameter is not a string'));

            if ($key === 'format' && !is_string($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'format\' parameter is not a string'));

            if ($key === 'minItems' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minItems\' parameter is not an integer'));

            if ($key === 'minItems' && is_integer($value) && $value < 0)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minItems\' parameter must not be less than zero'));

            if ($key === 'maxItems' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maxItems\' parameter is not an integer'));

            if ($key === 'maxItems' && is_integer($value) && $value < 0)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maxItems\' parameter must not be less than zero'));

            if ($key === 'items' && !is_object($value) && !is_array($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'items\' parameter must be an object or an array'));

            if ($key === 'items' && is_object($value))
                $this->testSchema($value); // if items value is an object, it must be a valid schema

            if ($key === 'items' && is_array($value))
            {
                foreach ($value as $v)
                    $this->testSchema($v); // if items value is an array, each element of the array must be a valid schema
            }

            if ($key === 'minProperties' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minProperties\' parameter is not an integer'));

            if ($key === 'minProperties' && is_integer($value) && $value < 0)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'minProperties\' parameter must not be less than zero'));

            if ($key === 'maxProperties' && !is_integer($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maxProperties\' parameter is not an integer'));

            if ($key === 'maxProperties' && is_integer($value) && $value < 0)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'maxProperties\' parameter must not be less than zero'));

            if ($key === 'required' && !is_array($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'required\' parameter is not an array'));

            if ($key === 'required' && is_array($value) && count($value) < 1)
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'required\' parameter must contain at least one element'));

            if ($key === 'required' && is_array($value))
            {
                // make sure the required array contains unique strings
                $arr_keys = array();
                foreach ($value as $v)
                {
                    // required parameter needs to be a string
                    if (!is_string($v))
                    {
                        $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'required\' parameter must only contain string values'));
                        continue;
                    }

                    // required parameter needs to be unique in the list
                    // of required parameters
                    if (isset($arr_keys[$v]))
                    {
                        $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'required\' parameter must contain unique values'));
                        continue;
                    }

                    $arr_keys[$v] = 1;
                }
            }

            if ($key === 'properties' && !is_object($value))
                $this->setError(self::ERROR_INVALID_SYNTAX, _('Schema \'properties\' parameter must be an object'));

            if ($key === 'properties' && is_object($value))
            {
                foreach ($value as $v)
                    $this->testSchema($v); // each of the property values must be a valid schema
            }
        }

        // if we have any errors, the schemtesttesta isn't valid
        if ($this->hasErrors())
            return false;

        return true;
    }

    private function testObject($property, $value, $schema) : bool
    {
        $this->testTitle($property, $value, $schema);
        $this->testDescription($property, $value, $schema);
        $this->testType($property, $value, $schema);
        $this->testEnum($property, $value, $schema);
        $this->testNumericMinimum($property, $value, $schema);
        $this->testNumericMaximum($property, $value, $schema);
        $this->testNumericMultipleOf($property, $value, $schema);
        $this->testStringMinimumLength($property, $value, $schema);
        $this->testStringMaximumLength($property, $value, $schema);
        $this->testStringPattern($property, $value, $schema);
        $this->testStringFormat($property, $value, $schema);
        $this->testArrayMinimumItemCount($property, $value, $schema);
        $this->testArrayMaximumItemCount($property, $value, $schema);
        $this->testObjectMinimumPropertyCount($property, $value, $schema);
        $this->testObjectMaximumPropertyCount($property, $value, $schema);
        $this->testObjectRequiredProperties($property, $value, $schema);

        // contain recursive calls to self::testObject():
        $this->testArrayItems($property, $value, $schema);
        $this->testObjectProperties($property, $value, $schema);

        if ($this->hasErrors())
            return false;

        return true;
    }

    private function testTitle($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'title'))
            return;

        // note: the title in the schema is descriptive, so there's no
        // corresponding data validation

        return;
    }

    private function testDescription($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'description'))
            return;

        // note: the description in the schema is descriptive, so there's no
        // corresponding data validation

        return;
    }

    private function testType($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'type'))
            return;

        // the type parameter has to be either a string or an array
        if (is_string($schema->type))
        {
            $type_match = self::isTypeMatch($data, $schema->type);
            if ($type_match === false)
            {
                $type = self::getType($data);
                $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid type of value ($type); should be a $schema->type"));
            }

            return;
        }

        if (is_array($schema->type))
        {
            $child_nodes = $schema->type;
            foreach ($child_nodes as $cn)
            {
                if (self::isTypeMatch($data, $cn))
                    return;
            }

            // value didn't match any of the types
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid type of value") . self::example_list($child_nodes));
            return;
        }

        // type has to be string or array; validated in schema test

        // TODO: fill out
        return;
    }

    private function testEnum($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'enum'))
            return;

        // an enumeration exists; compare the values in the enumeration
        // against the object
        foreach ($schema->enum as $en)
        {
            if (self::isValueEqual($data, $en))
                return;
        }

        // none of the objects match
        $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a value that isn't allowed"));
        return;
    }

    private function testNumericMinimum($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'minimum'))
            return;

        if (!is_numeric($data))
            return;

        $minimum = (float)$schema->minimum;

        $exclusive = false;
        if (property_exists($schema, 'exclusiveMinimum'))
            $exclusive = $schema->exclusiveMinimum;

        if ($exclusive === true && $data <= $minimum)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a value that's too small; should be greater than $minimum"));

        if ($exclusive !== true && $data < $minimum)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a value that's too small; should be greater than or equal to $minimum"));

        return;
    }

    private function testNumericMaximum($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'maximum'))
            return;

        if (!is_numeric($data))
            return;

        $maximum = (float)$schema->maximum;

        $exclusive = false;
        if (property_exists($schema, 'exclusiveMaximum'))
            $exclusive = $schema->exclusiveMaximum;

        if ($exclusive === true && $data >= $maximum)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a value that's too large; should be less than $maximum"));

        if ($exclusive !== true && $data > $maximum)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a value that's too large; should be less than or equal to $maximum"));

        return;
    }

    private function testNumericMultipleOf($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'multipleOf'))
            return;

        if (!is_numeric($data))
            return;

        $multipleof = $schema->multipleOf;

        // if the data is zero, it's always a multiple
        if ($data == 0)
            return;

        // for straight integer comparison, test for integer remainder
        if (is_integer($data) && is_integer($multipleof))
        {
            if ($data % $multipleof != 0)
                $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid value; should be a multiple of $multipleof"));

            return;
        }

        // one of the values is floating point; note: fmod() returns unreliable
        // results in some case (for example fmod(1.2,0.2) returns 0.2, so
        // to find out of $data is a multiple of $multipleof, use an epsilon
        // comparison for small values
        $data = abs($data);

        if ($data < $multipleof)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid value; should be a multiple of $multipleof"));

        if ($data >= $multipleof && abs($data/$multipleof-round($data/$multipleof)) > 2e-15)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid value; should be a multiple of $multipleof"));

        return;
    }

    private function testStringMinimumLength($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'minLength'))
            return;

        if (!is_string($data))
            return;

        if (strlen($data) < $schema->minLength)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a length that's too small; should have at least $schema->minLength characters"));

        return;
    }

    private function testStringMaximumLength($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'maxLength'))
            return;

        if (!is_string($data))
            return;

        if (strlen($data) > $schema->maxLength)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has a length that's too large; should have at most $schema->maxLength characters"));

        return;
    }

    private function testStringPattern($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'pattern'))
            return;

        if (!is_string($data))
            return;

        // enclose the regex expression with forward slash
        $regex = '/' . str_replace('/', '\\/', $schema->pattern) . '/';
        $match_result = @preg_match($regex,$data); // suppress errors

        if ($match_result === false) // error trying to match
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid format"));

        if ($match_result === 0) // no match
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid format"));

        return;
    }

    private function testStringFormat($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'format'))
            return;

        if (!is_string($data))
            return;

        // if the value a string, validate it against a known format, and if
        // it doesn't match, return false
        $result = true;
        switch ($schema->format)
        {
            // general formats
            case 'date-time': $result = \Flexio\Base\Util::isValidDateTime($data); break;
            case 'email':     $result = \Flexio\Base\Util::isValidEmail($data);    break;
            case 'hostname':  $result = \Flexio\Base\Util::isValidHostName($data); break;
            case 'ipv4':      $result = \Flexio\Base\Util::isValidIPV4($data);     break;
            case 'ipv6':      $result = \Flexio\Base\Util::isValidIPV6($data);     break;
            case 'uri':       $result = \Flexio\Base\Util::isValidUrl($data);      break; // TODO: expand to uri?

            // custom formats
            case 'fx.eid':        $result = \Flexio\Base\Eid::isValid($data);                      break;
            case 'fx.identifier': $result = \Flexio\Base\Identifier::isValid($data);               break;
            case 'fx.fieldname':  $result = \Flexio\Services\Postgres::isValidFieldName($data); break;
        }

        if ($result === false)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has an invalid format"));

        // the format isn't recognized; in this case, consider the value
        // valid and return true
        return;
    }

    private function testArrayMinimumItemCount($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'minItems'))
            return;

        if (!self::is_jsonarray($data))
            return;

        if (count($data) < $schema->minItems)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has too few items; should have at least $schema->minItems items"));

        return;
    }

    private function testArrayMaximumItemCount($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'maxItems'))
            return;

        if (!self::is_jsonarray($data))
            return;

        if (count($data) > $schema->maxItems)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has too many items; should have at most $schema->maxItems items"));

        return;
    }

    private function testObjectMinimumPropertyCount($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'minProperties'))
            return;

        if (!self::is_jsonobject($data))
            return;

        $prop_count = 0;
        foreach ($data as $prop)
            $prop_count++;

        if ($prop_count < $schema->minProperties)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has too few properties; should have at least $schema->minProperties items"));

        return;
    }

    private function testObjectMaximumPropertyCount($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'maxProperties'))
            return;

        if (!self::is_jsonobject($data))
            return;

        $prop_count = 0;
        foreach ($data as $prop)
            $prop_count++;

        if ($prop_count > $schema->maxProperties)
            $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has too many properties; should have at least $schema->maxProperties items"));

        return;
    }

    private function testObjectRequiredProperties($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'required'))
            return;

        if (!self::is_jsonobject($data))
            return;

        // make sure that the required property exists in the value
        // that's being tested
        foreach ($schema->required as $key)
        {
            if (!self::json_property_exists($data, $key))
                $this->setError(self::ERROR_MISSING_PARAMETER, _("Property " . json_encode($key) . " is required, but is missing"));
        }

        return;
    }

    private function testArrayItems($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'items'))
            return;

        if (!self::is_jsonarray($data))
            return;

        $schema_items = $schema->items;

        // if the items node is an object, then it's a schema;
        // compare it against each of the items in the data array
        if (is_object($schema_items))
        {
            foreach ($data as $value)
            {
                $this->testObject(null, $value, $schema_items);
            }

            return;
        }

        // if the schema items node is an array, compare the elements of
        // the schema items array to the items in the data array by
        // cycling through the elements of the schema items array as
        // we cycle through the data items
        if (is_array($schema_items))
        {
            // if 'additionalItems' isn't specified or is false, the array value
            // size must be less than or equal to the number of items
            // TODO: implement 'additionalItems'
            $max_item_count = count($schema_items);
            if (count($data) > $max_item_count)
            {
                $this->setError(self::ERROR_INVALID_PARAMETER, self::example($property, $data) . _(" has too many items; should have at most $max_item_count items"));
                return;
            }

            // each of the items in the array value needs to validate against the
            // corresponding items in the schema array
            $idx = 0;
            foreach ($schema_items as $item)
            {
                $this->testObject(null, $data[$idx], $schema_items);
                $idx++;
            }

            return;
        }

        // type has to be an array or an object; validated in schema test
    }

    private function testObjectProperties($property, $data, $schema) : void
    {
        if (!property_exists($schema, 'properties'))
            return;

        if (!self::is_jsonobject($data))
            return;

        $schema_properties = $schema->properties;

        foreach ($data as $key => $value)
        {
            if (!property_exists($schema_properties, $key))
            {
                // TODO: add "additionalProperties" schema item
                continue;
            }

            // we have schema defined for the child node, so validate against it
            $this->testObject($key, $value, $schema_properties->$key);
        }
    }

    private static function isValueEqual($value1, $value2) : bool
    {
        // values are equal if they are both primitive types (not array
        // and not object) and they have the same equivalent value
        if (self::isPrimitiveValueEqual($value1, $value2))
            return true;

        // nodes are equal if they are both json arrays and have the
        // same number of elements with equivalent values
        if (self::is_jsonarray($value1) && self::is_jsonarray($value2))
        {
            $value1_childcount = count($value1);
            $value2_childcount = count($value2);

            // different number elements
            if ($value1_childcount != $value2_childcount)
                return false;

            // handle "no element" case
            if ($value1_childcount == 0 && $value2_childcount)
                return true;

            $idx = 0;
            foreach ($value1 as $value1_child)
            {
                $value2_child = $value2[$idx];
                if (!self::isValueEqual($value1_child, $value2_child))
                    return false;

                $idx++;
            }

            // same number of array elements and values in the same position
            return true;
        }

        // nodes are equal if they are both objects and have the
        // same number of elements with equivalent values
        if (self::is_jsonobject($value1) && self::is_jsonobject($value2))
        {
            // test to see if the objects have teh same number of elements
            $value1_count = count(get_object_vars($value1));
            $value2_count = count(get_object_vars($value2));

            if ($value1_count != $value2_count)
                return false;

            foreach ($value1 as $value1_childkey => $value1_childvalue)
            {
                // if we can't find the node1 key in node2, objects aren't the same
                if (!property_exists($value2, $value1_childkey))
                    return false;

                // key exists in both objects; values must now be the same
                if (!self::isValueEqual($value1_childvalue, $value2->$value1_childkey))
                    return false;
            }

            // same object keys and child values
            return true;
        }

        // some other case; shouldn't happen
        return false;
    }

    private static function isPrimitiveValueEqual($value1, $value2) : bool
    {
        // if either node is an array or object, it's not a primitive
        if (is_array($value1) || is_array($value2))
            return false;
        if (is_object($value1) || is_object($value2))
            return false;

        if (is_null($value1) && is_null($value2))
            return true;

        // we're comparing two types, at least one of which is either a
        // boolean, number or string; if the other is null or undefined,
        // they're not equal
        if (is_null($value1) || is_null($value2))
            return false;

        // if values and types match, they are equivalent
        if ($value1 === $value2)
            return true;

        // if both values are numeric, they are the same if their values
        // are the same, even if the types differ (i.e., 1 is equal to 1.0)
        if (is_numeric($value1) && is_numeric($value2))
            return $value1 == $value2;

        // values don't match
        return false;
    }

    private static function isTypeMatch($data, $type) : bool
    {
        if ($type === 'null' && is_null($data))
            return true;
        if ($type === 'boolean' && is_bool($data))
            return true;
        if ($type === 'integer' && is_integer($data))
            return true;
        if ($type === 'number' && is_numeric($data))
            return true;
        if ($type === 'string' && is_string($data))
            return true;
        if ($type === 'object' && self::is_jsonobject($data))
            return true;
        if ($type === 'array' && self::is_jsonarray($data))
            return true;

        // unrecognized type; return false
        return false;
    }

    private static function isPrimitiveType($type) : bool
    {
        if ($type === 'null')
            return true;
        if ($type === 'boolean')
            return true;
        if ($type === 'integer')
            return true;
        if ($type === 'number')
            return true;
        if ($type === 'string')
            return true;
        if ($type === 'object')
            return true;
        if ($type === 'array')
            return true;

        // unrecognized type; return false
        return false;
    }

    private static function getType($data) : string
    {
        if (is_null($data))
            return 'null';
        if (is_bool($data))
            return 'boolean';
        if (is_integer($data))
            return 'integer';
        if (is_numeric($data))
            return 'number';
        if (is_string($data))
            return 'string';
        if (self::is_jsonobject($data))
            return 'object';
        if (self::is_jsonarray($data))
            return 'array';

        // unrecognized type
        return '';
    }

    private static function json_property_exists($value, $key) : bool
    {
        // returns true if the key exists in a given array or object;
        // false otherwise

        if (is_object($value))
            return property_exists($value, $key);
        if (is_array($value))
            return array_key_exists($key, $value);

        return false;
    }

    private static function is_jsonobject($value) : bool
    {
        // a json object is a set of key-value associations; returns
        // true if the value is a json array; false otherwise

        if (is_object($value))
            return true;

        if (is_array($value))
        {
            $str_item_count = count(array_filter(array_keys($value), 'is_string'));
            return ($str_item_count > 0) ? true : false;
        }

        return false;
    }

    private static function is_jsonarray($value) : bool
    {
        // a json array is an indexed array without string key-value
        // associations; returns true if the value is a json array;
        // false otherwise

        if (!is_array($value))
            return false;

        $str_item_count = count(array_filter(array_keys($value), 'is_string'));
        return ($str_item_count === 0) ? true : false;
    }

    private static function example($key, $value) // TODO: add return type
    {
        // create a value that can be displayed
        if (is_array($value) || is_object($value))
            $value = json_encode($value);
        $value = ''.$value;
        if (strlen($value) > 20)
            $value = substr($value,0,20) . '...';

        // the key might be null (top level values), in which case,
        // just return the value
        if (!is_string($key))
            return $value;

        $pair = array($key => $value);
        return json_encode($pair);
    }

    private static function example_list($data) // TODO: add return type
    {
        $count = count($data);

        if ($count === 0)
            return '';

        if ($count === 1)
            return $data[0];

        if ($count === 2)
            return $data[0] . ' or ' . $data[1];

        $result = '';
        $idx = 0;
        foreach ($data as $d)
        {
            if ($idx !== 0)
                $result .= ', ';
            if ($idx === ($count - 1))
                $result .= 'or ';

            $result .= $d;
            $idx++;
        }

        return $result;
    }
}
