<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        $search_model = \Flexio\Tests\Util::getModel()->search;


        // TEST: \Flexio\Model\Search::exec(); search tests with invalid search path

        // BEGIN TEST
        $actual = '';
        try
        {
            $path = null;
            $result = $search_model->exec($path);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Search::exec(); throw an error with and invalid search parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $path = true;
            $result = $search_model->exec($path);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Model\Search::exec(); throw an error with and invalid search parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $path = "";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.3', '\Flexio\Model\Search::exec(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $path = "->";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.4', '\Flexio\Model\Search::exec(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "$eid->";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.5', '\Flexio\Model\Search::exec(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "->$eid";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.6', '\Flexio\Model\Search::exec(); return false with invalid search path',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Search::exec(); search tests with text parameters that aren't valid eids or edges in appropriate places

        // BEGIN TEST
        $path = "abc";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Model\Search::exec(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $path = "$edge_copied_to";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Model\Search::exec(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $path = "($edge_copied_to)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Model\Search::exec(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);
    }
}
