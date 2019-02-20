<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-15
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
        // FUNCTION: \Flexio\Model\Stream::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->stream;


        // TEST: creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Stream::create(); for stream creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);


        // TEST: creation with basic input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Stream::create(); make sure valid eid is returned when stream is created',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid_first_time_creation = $model->create($info);
        $eid_second_time_creation = $model->create($info);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && \Flexio\Base\Eid::isValid($eid_second_time_creation));
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\Stream::create(); allow multiple streams with the same name',  $actual, $expected, $results);


        // TEST: multiple unique stream creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_stream_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'name' => "Stream $i"
            );
            $eid = $model->create($info);
            $created_eids[$eid] = 1;
            if (!\Flexio\Base\Eid::isValid($eid))
                $failed_stream_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_stream_creation == 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\Stream::create(); creating streams should succeed and produce a unique eid for each new stream',  $actual, $expected, $results);
    }
}
