<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-14
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



        // TEST: search tests with invalid search path

        // BEGIN TEST
        $actual = '';
        try
        {
            $path = null;
            $result = $model->search($path);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::search(); throw an error with and invalid search parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $path = true;
            $result = $model->search($path);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Model::search(); throw an error with and invalid search parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $path = "";
        $result = $model->search($path);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $path = "->";
        $result = $model->search($path);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "$eid->";
        $result = $model->search($path);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "->$eid";
        $result = $model->search($path);
        $actual = $result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Model::search(); return false with invalid search path',  $actual, $expected, $results);



        // TEST: search tests with text parameters that aren't valid eids or edges in appropriate places

        // BEGIN TEST
        $path = "abc";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.1', '\Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        $edge_owns = \Model::EDGE_OWNS;
        $path = "$edge_owns";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.2', '\Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        $edge_owns = \Model::EDGE_OWNS;
        $path = "($edge_owns)";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.3', '\Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);
    }
}
