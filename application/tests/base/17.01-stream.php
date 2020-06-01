<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
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
        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $actual = 'Flexio\Base\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Stream::create(); return the object if it\'s successfully created',  $actual, $expected, $results);




        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $object = $object->set([]);
        $actual =  'Flexio\Base\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Stream::set(); return the object',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Base\Stream::create();
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
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);
    }
}
