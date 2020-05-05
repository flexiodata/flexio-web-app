<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-04
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
        // TEST: table create

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\Table::create(); throw an exception if two-dimensional array of scalars isn\'t passed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create([]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\Table::create(); throw an exception if two-dimensional array of scalars isn\'t passed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create(["a","b"]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Base\Table::create(); throw an exception if two-dimensional array of scalars isn\'t passed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create([[]]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\Table::create(); throw an exception if two-dimensional array of scalars isn\'t passed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create([[1,2],[3]]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Base\Table::create(); if two-dimensional array has different number of columsn, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create([[1],[2,3]]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Base\Table::create(); if two-dimensional array has different number of columsn, throw an exception',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Flexio\Base\Table::create([[1,2],[3],[4,5]]);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Base\Table::create(); if two-dimensional array has different number of columsn, throw an exception',  $actual, $expected, $results);



    }
}
