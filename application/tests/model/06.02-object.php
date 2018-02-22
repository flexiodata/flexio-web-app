<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-14
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
        // TEST: \Model::getType(); tests for when object doesn't exist

        // BEGIN TEST
        $type = \Flexio\Tests\Util::getModel()->getType('x');
        $actual = $type;
        $expected = \Model::TYPE_UNDEFINED;
        \Flexio\Tests\Check::assertString('A.1', '\Model::getType(); return undefined type if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $type = \Flexio\Tests\Util::getModel()->getType($eid);
        $actual = $type;
        $expected = \Model::TYPE_UNDEFINED;
        \Flexio\Tests\Check::assertString('A.2', '\Model::getType(); return undefined type if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getType(); tests for when object exists

        // BEGIN TEST
        $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        try
        {
            $info = array(
            );
            $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
            $type = \Flexio\Tests\Util::getModel()->getType($eid);
            $actual = $type;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('B.1', '\Model::getType(); return correct object type',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object doesn't exist

        // BEGIN TEST
        $status = \Flexio\Tests\Util::getModel()->getStatus('x');
        $actual = $status;
        $expected = \Model::STATUS_UNDEFINED;
        \Flexio\Tests\Check::assertString('C.1', '\Model::getStatus(); return undefined type if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $status = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_UNDEFINED;
        \Flexio\Tests\Check::assertString('C.2', '\Model::getStatus(); return undefined type if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object exists

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $status = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_PENDING;
        \Flexio\Tests\Check::assertString('D.1', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $status = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_AVAILABLE;
        \Flexio\Tests\Check::assertString('D.2', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_DELETED
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $status = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_DELETED;
        \Flexio\Tests\Check::assertString('D.3', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);



        // TEST: \Model::setStatus(); tests for when object doesn't exist

        // BEGIN TEST
        $result = \Flexio\Tests\Util::getModel()->setStatus('x', \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.1', '\Model::setStatus(); return false if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $result = \Flexio\Tests\Util::getModel()->setStatus($eid, \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.2', '\Model::setStatus(); return false if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object exists

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $result = \Flexio\Tests\Util::getModel()->setStatus($eid, \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.1', '\Model::setStatus(); return true if status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid_status1 = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $result = \Flexio\Tests\Util::getModel()->setStatus($eid, \Model::STATUS_AVAILABLE);
        $eid_status2 = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = ($eid_status1 === \Model::STATUS_PENDING && $eid_status2 === \Model::STATUS_AVAILABLE);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', '\Model::setStatus(); make sure status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'eid_status' => \Model::STATUS_DELETED
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid_status1 = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $result = \Flexio\Tests\Util::getModel()->setStatus($eid, \Model::STATUS_AVAILABLE);
        $eid_status2 = \Flexio\Tests\Util::getModel()->getStatus($eid);
        $actual = ($eid_status1 === \Model::STATUS_DELETED && $eid_status2 === \Model::STATUS_AVAILABLE);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.3', '\Model::setStatus(); make sure status is recoverable from \'deleted\'',  $actual, $expected, $results);
    }
}
