<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-11
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
        if (\Flexio\Tests\Base::TEST_STORAGE_AMAZONS3 === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\AmazonS3;
        $actual = get_class($service);
        $expected = 'Flexio\Services\AmazonS3';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\AmazonS3; basic file syntax check',  $actual, $expected, $results);
    }
}
