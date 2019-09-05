<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-05
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
        // TEST: yaml parse

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: test
# ---
EOD;
        $yaml = \Flexio\Base\Yaml::extract($content);
        $actual = \Flexio\Base\Yaml::parse($yaml);
        $expected = '
        {
            "name": "test"
        }
        ';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Yaml::parse(); extract and parse yaml from front matter in comments',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: func-sales
# description: Returns the sales funnel data from a CSV
# deployed: true
# ---



EOD;
        $yaml = \Flexio\Base\Yaml::extract($content);
        $actual = \Flexio\Base\Yaml::parse($yaml);
        $expected = '
        {
            "name": "func-sales",
            "description": "Returns the sales funnel data from a CSV",
            "deployed": true
        }
        ';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Yaml::parse(); extract and parse yaml from front matter in comments',  $actual, $expected, $results);
    }
}
