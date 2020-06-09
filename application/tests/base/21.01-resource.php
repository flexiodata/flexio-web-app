<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-06-02
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
        // TEST: resource creation

        // BEGIN TEST
        $object = \Flexio\Base\Resource::create();
        $actual = 'Flexio\Base\Resource';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Resource::create(); return the object if it\'s successfully created',  $actual, $expected, $results);


        // TEST: resource property setting

        // BEGIN TEST
        $object = \Flexio\Base\Resource::create();
        $object = $object->set([]);
        $actual =  'Flexio\Base\Resource';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.1', 'Resource::set(); return the object',  $actual, $expected, $results);


        // TEST: resource property retrieval

        // BEGIN TEST
        $object = \Flexio\Base\Resource::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('C.1', 'Resource::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Base\Resource::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : {
            },
            "file_created" : null,
            "file_modified" : null,
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('D.2', 'Resource::get(); return the properties as an array',  $actual, $expected, $results);
    }
}
