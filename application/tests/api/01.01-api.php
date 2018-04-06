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
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // BEGIN TEST
        $object = new \Flexio\Api\Api;
        $actual = 'Flexio\Api\Api';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Api\Api; return the object if it\'s successfully created',  $actual, $expected, $results);
    }
}
