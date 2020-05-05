<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
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
        if (\Flexio\Tests\Base::TEST_SERVICE_ELASTICSEARCH === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\ElasticSearch;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\ElasticSearch';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\ElasticSearch; basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\ElasticSearch;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\ElasticSearch; instance of IConnection ',  $actual, $expected, $results);
    }
}
