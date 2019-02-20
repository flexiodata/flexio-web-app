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
        // FUNCTION: \Flexio\Model\Token::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->token;


        // TEST: creation with no parameters

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Model\Token::create(); throw an exception with invalid input (unique access token needs to be specified)',  $actual, $expected, $results);


        // TEST: creation with basic input

        // BEGIN TEST
        $access_code = \Flexio\Base\Util::generateHandle(); // access code needs to be unique
        $info = array(
            'eid_status' => \Model::STATUS_PENDING,
            'access_code' => $access_code,
            'owned_by' => 'cxxxxxxxxxxx',
            'created_by' => 'dxxxxxxxxxxx',
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'access_code' => $access_code,
            'owned_by' => 'cxxxxxxxxxxx',
            'created_by' => 'dxxxxxxxxxxx'
        );
        \Flexio\Tests\Check::assertInArray('B.1', '\Flexio\Model\Token::create(); make sure parameters can be set on creation',  $actual, $expected, $results);
    }
}
