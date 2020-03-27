<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-27
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
        $db = \Flexio\System\System::getModel()->getDatabase();


        // TEST: build function

        // BEGIN TEST
        $allowed_items = array();
        $filter = array();
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = '(true)';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Filter::build(); build filter string',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by = 'A'))";
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Model\Filter::build(); build filter string',  $actual, $expected, $results);
    }
}
