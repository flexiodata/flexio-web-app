<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-01
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
        // TEST: front matter parsing

        // BEGIN TEST
        $content = <<<EOD
EOD;
        $actual = \Flexio\Base\Yaml::parse($content);
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.1', '\Flexio\Base\Yaml::parse(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
name: test
EOD;
        $actual = \Flexio\Base\Yaml::parse($content);
        $expected = '
        {
            "name" : "test"
        }
        ';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Yaml::parse(); basic test',  $actual, $expected, $results);
    }
}
