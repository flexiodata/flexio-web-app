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
        $search_model = \Flexio\Tests\Util::getModel()->search;
        $pipe_model = \Flexio\Tests\Util::getModel()->pipe;


        // TEST: make sure that duplicate edges and eids are removed in search

        // BEGIN TEST
        $info = array(
        );
        $eid = $pipe_model->create($info);
        $path = "$eid,$eid";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $pipe_model->create($info);
        $path = "($eid,$eid)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $pipe_model->create($info);
        $path = "($eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        \Flexio\Tests\Check::assertArray('A.3', '\Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);
    }
}
