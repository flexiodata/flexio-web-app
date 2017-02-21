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


class Test
{
    public function run(&$results)
    {
        // TEST: more sophisticated object test placeholder

        // BEGIN TEST
        $object = <<<EOD
{
    "type": "flexio.create",
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
        $validschema = \ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = <<<EOD
{
    "type": "flexio.create",
    "params": {
        "columns": [
        ]
    }
}
EOD;
        $template = <<<EOD
{
    "type": "object",
    "required": ["type","params"],
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
        $validschema = \ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = <<<EOD
{
    "type": "flexio.create",
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
    "required": ["type","params"],
    "properties": {
        "type": {
            "type": "string",
            "enum": ["flexio.create"]
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
        $validschema = \ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        TestCheck::assertBoolean('A.3', '\ValidatorSchema::check(); basic object validation test',  $actual, $expected, $results);

        // BEGIN TEST
        $object = array(
            'type'=>'flexio.create',
            'params'=>array(
                'columns'=>array(
                    array('name'=>'','type'=>'character','width'=>1),
                    array('name'=>'','type'=>'character','width'=>1)
                )
            )
        );
        $template = <<<EOD
{
    "type": "object",
    "required": ["type","params"],
    "properties": {
        "type": {
            "type": "string",
            "enum": ["flexio.create"]
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
        $validschema = \ValidatorSchema::checkSchema($template)->hasErrors() === false;
        $validobject = \ValidatorSchema::checkObject($object, $template)->hasErrors() === false;
        $validschemaobject = \ValidatorSchema::check($object, $template)->hasErrors() === false;
        $actual = $validschema === true && $validobject === true && $validschemaobject === true;
        $expected = true;
        TestCheck::assertBoolean('A.4', '\ValidatorSchema::check(); basic object validation test using array object definition',  $actual, $expected, $results);
    }
}
