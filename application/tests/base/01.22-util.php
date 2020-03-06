<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-02
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
        // TEST: \Flexio\Base\Util::coerceToParams()

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToQueryParams("col1=a");
        $expected = ["col1"=>"a"];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::coerceToQueryParams(); coerce input to an array of keys and associated match params',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Util::coerceToQueryParams("col1=a&col2=b");
        $expected = ["col1"=>"a","col2"=>"b"];
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Util::coerceToQueryParams(); coerce input to an array of keys and associated match params',  $actual, $expected, $results);
    }
}
