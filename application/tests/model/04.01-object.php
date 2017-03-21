<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); invalid type

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid = $model->create('', $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::NO_MODEL
        );
        TestCheck::assertInArray('A.1', '\Model::create(); invalid type should throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid = $model->create(\Model::TYPE_UNDEFINED, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::NO_MODEL
        );
        TestCheck::assertInArray('A.2', '\Model::create(); undefined type should throw an exception',  $actual, $expected, $results);



        // TEST: \Model::create(); valid type

        // BEGIN TEST
        $actual = false;
        try
        {
            $info = array(
            );
            $eid = $model->create(\Model::TYPE_OBJECT, $info);
            $actual = \Flexio\Base\Eid::isValid($eid);
        }
        catch (\Exception $e)
        {
            $actual = TestError::ERROR_EXCEPTION;
        }
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); for object creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);
    }
}
