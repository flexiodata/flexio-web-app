<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // FUNCTION: \Flexio\Model\Model::getDatabase()


        // TODO: check connection failure


        // TEST: model database connection

        // BEGIN TEST
        $db = \Flexio\Tests\Util::getModel()->getDatabase();
        $actual = is_object($db);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Model::getDatabase(); basic connection with default credentials',  $actual, $expected, $results);


        // TEST: model database check connection caching

        // BEGIN TEST
        $actual = true;
        for ($i = 0; $i < 10000; $i++)
        {
            $db = \Flexio\Tests\Util::getModel()->getDatabase();
            if (!$db)
            {
                $actual = false;
                break;
            }
        }
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Model::getDatabase(); connection caching',  $actual, $expected, $results);
    }
}
