<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-25
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: more sophisticated object test placeholder

        // BEGIN TEST
        $object = <<<EOD
{
    "op": "create",
    "params": {
        "columns": [
        ]
    }
}
EOD;
        $template = <<<EOD
{
    "type": "object"
}
EOD;
        $object = json_decode($object);
        $template = json_decode($template);
        $validschema = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \Flexio\Base\ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \Flexio\Base\ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = <<<EOD
{
    "op": "create",
    "params": {
        "columns": [
        ]
    }
}
EOD;
        $template = <<<EOD
{
    "type": "object",
    "required": ["op","params"],
    "properties": {
        "type": {
            "type": "string"
        },
        "params": {
            "type": "object"
        }
    }
}
EOD;
        $object = json_decode($object);
        $template = json_decode($template);
        $validschema = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \Flexio\Base\ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \Flexio\Base\ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = <<<EOD
{
    "op": "create",
    "params": {
        "columns": [
            {"name": "", "type":"character", "width": 1},
            {"name": "", "type":"character", "width": 1}
        ]
    }
}
EOD;
        $template = <<<EOD
{
    "type": "object",
    "required": ["op","params"],
    "properties": {
        "type": {
            "type": "string",
            "enum": ["create"]
        },
        "params": {
            "type": "object",
            "required": ["columns"],
            "properties": {
                "columns" : {
                    "type": "array",
                    "minItems": 1,
                    "items": {
                        "type": "object",
                        "required": ["name"],
                        "properties": {
                            "name": {
                                "type": "string"
                            },
                            "type": {
                                "enum": ["character","number","integer","double","date","datetime","boolean"]
                            },
                            "width": {
                                "type": "integer",
                                "minimum": 1,
                                "maximum": 10000
                            },
                            "scale": {
                                "type": "integer",
                                "minimum": 1,
                                "maximum": 12
                            }
                        }
                    }
                }
            }
        }
    }
}
EOD;
        $object = json_decode($object);
        $template = json_decode($template);
        $validschema = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \Flexio\Base\ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \Flexio\Base\ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = array(
            "op"=>"create",
            "params"=>array(
                "columns"=>array(
                    array("name"=>"","type"=>"character","width"=>1),
                    array("name"=>"","type"=>"character","width"=>1)
                )
            )
        );
        $template = <<<EOD
{
    "type": "object",
    "required": ["op","params"],
    "properties": {
        "type": {
            "type": "string",
            "enum": ["create"]
        },
        "params": {
            "type": "object",
            "required": ["columns"],
            "properties": {
                "columns" : {
                    "type": "array",
                    "minItems": 1,
                    "items": {
                        "type": "object",
                        "required": ["name"],
                        "properties": {
                            "name": {
                                "type": "string"
                            },
                            "type": {
                                "enum": ["character","number","integer","double","date","datetime","boolean"]
                            },
                            "width": {
                                "type": "integer",
                                "minimum": 1,
                                "maximum": 10000
                            },
                            "scale": {
                                "type": "integer",
                                "minimum": 1,
                                "maximum": 12
                            }
                        }
                    }
                }
            }
        }
    }
}
EOD;
        $validschema = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \Flexio\Base\ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \Flexio\Base\ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\ValidatorSchema::check(); basic object validation test using array object definition',  $actual, $expected, $results);
    }
}
