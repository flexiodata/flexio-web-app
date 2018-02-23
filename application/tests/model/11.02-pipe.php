<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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
        // TEST: \Model::create(); pipe creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); for pipe creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);



        // TEST: \Model::create(); pipe creation with basic name input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'This is a test pipe'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); make sure valid eid is returned when pipe is created',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid_first_time_creation = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $eid_second_time_creation = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && \Flexio\Base\Eid::isValid($eid_second_time_creation));
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Model::create(); allow multiple pipes with the same value',  $actual, $expected, $results);
    }
}
