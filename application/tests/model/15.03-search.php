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
        // TEST: search tests when results for single eid

        // BEGIN TEST
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $path = "$eid";
        $result = \Flexio\Tests\Util::getModel()->search($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Model::search(); search for single eid that exists',  $actual, $expected, $results);
    }
}
