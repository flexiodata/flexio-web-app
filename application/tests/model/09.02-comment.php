<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); comment creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); for comment creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);



        // TEST: \Model::create(); comment creation with basic parameter input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => 'This is a test comment'
        );
        $eid = $model->create(\Model::TYPE_COMMENT, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); make sure valid eid is returned when comment is created',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid_first_time_creation = $model->create(\Model::TYPE_COMMENT, $info);
        $eid_second_time_creation = $model->create(\Model::TYPE_COMMENT, $info);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && \Flexio\Base\Eid::isValid($eid_second_time_creation));
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Model::create(); allow multiple comments with the same value',  $actual, $expected, $results);
    }
}
