<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-26
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
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = \Flexio\Services\ElasticSearch::create(null);
        $actual = get_class($service);
        $expected = 'Flexio\Services\ElasticSearch';
        TestCheck::assertString('A.1', '\Flexio\Services\ElasticSearch::create(); create an instance of the class',  $actual, $expected, $results);
    }
}
