<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-07-05
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
        // FUNCTION: \Flexio\Model\Right::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->right;


        // TEST: creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Right::create(); for right creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);


        // TEST: creation with basic input

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $actions = json_encode(array(\Flexio\Object\Right::TYPE_READ));
        $info = array(
            'eid_status' => \Model::STATUS_PENDING,
            'object_eid' => $object_eid,
            'access_type' => 'a',
            'access_code' => 'b',
            'actions' => $actions,
            'owned_by' => 'cxxxxxxxxxxx',
            'created_by' => 'dxxxxxxxxxxx',
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'object_eid' => $object_eid,
            'access_type' => 'a',
            'access_code' => 'b',
            'actions' => $actions,
            'owned_by' => 'cxxxxxxxxxxx',
            'created_by' => 'dxxxxxxxxxxx'
        );
        \Flexio\Tests\Check::assertInArray('B.1', '\Flexio\Model\Right::create(); make sure parameters can be set on creation',  $actual, $expected, $results);
    }
}
