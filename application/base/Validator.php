<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-03-16
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Validator
{
    const ERROR_NONE               =  'none';
    const ERROR_UNDEFINED          =  'undefined';
    const ERROR_GENERAL            =  'general';
    const ERROR_INVALID_SYNTAX     =  'invalid_syntax';
    const ERROR_MISSING_PARAMETER  =  'missing_parameter';
    const ERROR_INVALID_PARAMETER  =  'invalid_parameter';

    private $errors = array();

    public static function getInstance()
    {
        return (new self);
    }

    public function check($params, $checks)
    {
        // cleans and validates the input parameters against the checks;
        // if the required items in the checks aren't in the parameters,
        // an error is set and the function returns false; if the optional
        // items in the checks aren't in the input parameters, the optional
        // items with the default value are added to the parameters; any
        // items that aren't in the checks aren't included in the result
        // so that the function also acts as an input filter; note: if
        // simple validation is all that's required without the filter,
        // simply use the initial input params rather than the output;

        // "type" may be one of: eid, string, integer, number, boolean
        // "required" is true or false; if false optional "default" specifies
        // what to use if a value isn't supplied
        // "array" may be true or false; if "array" is true, then the
        // the input may be a comma-delimited list of the specified type
        // and the output of the values are supplied as an array
        // "decode" may be true or false; if "unpack" is true, then an
        // input field that is json will be decoded

        // example of how this function might be used

        // if (($params = self::getInstance()->check($params, array(
        //         'eid'   => array('type' => 'eid',    'required' => true),
        //         'name'  => array('type' => 'string', 'required' => false, 'default' => 'sample')
        //     ))) === false)
        //     return false;


        // clear any validator errors
        $this->clearErrors();

        // require input params
        if (!is_array($params))
        {
            $error_code = self::ERROR_GENERAL;
            $error_message = _('No parameters to validate or parameters are in the wrong format');
            $this->setError($error_code, $error_message);
            return false;
        }

        // require input checks
        if (!is_array($checks))
        {
            $error_code = self::ERROR_GENERAL;
            $error_message = _('Parameter checks are in the wrong format');
            $this->setError($error_code, $error_message);
            return false;
        }

        // perform the checks
        $missing_fields = array();
        $invalid_values = array();
        $result = array();

        foreach ($checks as $key => $value)
        {
            // if the value is required, make sure it's present
            if ($value['required'] && !array_key_exists($key, $params))
            {
                $missing_fields[] = $key;
                continue;
            }

            // if the field doesn't exist and the field isn't required and a default
            // value is specified, add the default value to the params and continue
            // on so that the default value can be validated like any other parameter
            if (!array_key_exists($key, $params) && !$value['required'] && isset($value['default']))
            {
                $params[$key] = $value['default'];
            }

            // if the field doesn't exist, there's nothing left to validate; move on
            if (!array_key_exists($key, $params))
                continue;

            // if the field exists, make sure any boolean values that are represented
            // as a string 'true'/'false' are converted to actual boolean values
            if ($value['type'] === 'boolean' && $this->check_bool($params[$key]))
            {
                $result[$key] = toBoolean($params[$key]);
                continue;
            }

            // the field exists, check to see if multiple instances are allowed;
            // if so, check each of the individual members of the array
            $array_output = false;
            $decode_output = false;
            $param_values = array();
            $param_values[] = $params[$key];

            if (isset($value['array']) && $value['array'] == true && is_string($params[$key]))
            {
                $array_output = true;
                $param_values = explode(',', $params[$key]);
            }

            if (isset($value['decode']) && $value['decode'] == true && is_string($params[$key]))
            {
                $decode_output = true;
            }

            foreach ($param_values as $p)
            {
                if ($value['type'] == 'any')
                    continue;

                // the field exists; make sure the field conforms to the type specified
                if ($value['type'] == 'eid' && !$this->check_eid($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'identifier' && !$this->check_identifier($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'json' && !$this->check_json($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'object' && !$this->check_object($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'string' && !$this->check_string($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'integer' && !$this->check_integer($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'number' && !$this->check_number($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
                if ($value['type'] == 'boolean' && !$this->check_bool($p))
                    $invalid_values[] = $key . ":" . self::makeString($p);
            }

            // add the field to the result
            if ($array_output === true)
            {
                $result[$key] = $param_values; // unpack a delimited list
            }
             elseif ($decode_output === true)
            {
                $decoded_param = @json_decode($params[$key],true);
                $result[$key] = is_null($decoded_param) ? false : $decoded_param;
            }
            else
            {
                $result[$key] = $params[$key]; // don't do anything
            }
        }

        // if there are any problems, flag an error
        if (count($missing_fields) > 0)
        {
            $error_code = self::ERROR_MISSING_PARAMETER;
            $error_message = _('Missing parameter(s): ') . join(',', $missing_fields);
            $this->setError($error_code, $error_message);
        }

        if (count($invalid_values) > 0)
        {
            $error_code = self::ERROR_INVALID_PARAMETER;
            $error_message = _('Invalid parameter(s): ') . join(',', $invalid_values);
            $this->setError($error_code, $error_message);
        }

        // if there are any problems, return false
        if ($this->hasErrors())
            return false;

        return $result;
    }

    public function fail($code = null, $message = null)
    {
        if ($code == null)
        {
            // if an error code hasn't already been set, then
            // set a general error, otherwise, leave the last
            // error; useful when wanting to indicate failure
            // in the code when a function, sets an error code
            // and we want to give an indication of failure in
            // the output without setting a new code
            if (!$this->hasErrors())
                $this->setError(self::ERROR_GENERAL);

            return false;
        }

        $this->setError($code, $message);
        return false;
    }

    public function setError($code, $message = null)
    {
        $this->errors[] = array('code' => $code, 'message' => $message);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function clearErrors()
    {
        $this->errors = array();
    }

    private function check_bool($value) : bool
    {
        if (is_bool($value))
            return true;

        // note: for validation, some input parameters are true/false values
        // represented as strings (e.g. maybe they come in through a url param)
        if (is_string($value) && ($value === 'true' || $value === 'false'))
            return true;

        return false;
    }

    private function check_integer($value) : bool
    {
        if (!(is_numeric($value) && intval($value) == floatval($value)))
            return false;

        return true;
    }

    private function check_number($value) : bool
    {
        if (!is_numeric($value))
            return false;

        return true;
    }

    private function check_string($value) : bool
    {
        if (!is_string($value))
            return false;

        return true;
    }

    private function check_eid($value) : bool
    {
        if (!is_string($value))
            return false;

        if (!\Flexio\Base\Eid::isValid($value))
            return false;

        return true;
    }

    private function check_identifier($value) : bool
    {
        if (!is_string($value))
            return false;

        // note: include strlen check so that identifiers can be zero length so they can be set to ''
        if (!\Flexio\Base\Eid::isValid($value) && (strlen($value) > 0 && !\Flexio\Base\Identifier::isValid($value)))
            return false;

        return true;
    }

    private function check_object($value) : bool
    {
        // make sure we have an array or an object
        if (!is_object($value) && !is_array($value))
            return false;

        return true;
    }

    private function check_json($value) : bool
    {
        // our json validation is stricter than normal json; top-level
        // should be an array or an object rather than a primitive
        if (!is_string($value))
            return false;

        if (strlen($value) === 0)
            return false;

        $temp = ltrim($value);
        if ($temp[0] != '{' && $temp[0] != '[')
            return false;

        // see if we can parse the value
        $result = @json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE)
            return true;

        return false;
    }

    private function makeString($s)
    {
        if (is_array($s) || is_object($s))
            return json_encode($s);
        return (string)$s;
    }
}
