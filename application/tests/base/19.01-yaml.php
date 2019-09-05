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
        // TEST: yaml parse

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


        // TEST: front matter extract

        // BEGIN TEST
        $content = <<<EOD
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\Yaml::extract(); basic test', $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: test
# ---
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
#
# ---
# name: test
# ---
#
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
  #
  # ---
  # name: test
  # ---
  #
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
//
// ---
// name: test
// ---
//
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
  //
  // ---
  // name: test
  // ---
  //
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
/*
---
name: test
---
*/
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
/*
* ---
* name: test
* ---
*/
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.8', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
/*
 * ---
 * name: test
 * ---
*/
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.9', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
/*
---
name: test
---
*/
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: test
EOD;
        \Flexio\Tests\Check::assertString('B.10', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);


        // BEGIN TEST
        $content = <<<EOD
/*
---
colors:
  - red
  - green
  - blue
---
*/
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
colors:
  - red
  - green
  - blue
EOD;
        \Flexio\Tests\Check::assertString('B.11', '\Flexio\Base\Yaml::extract(); basic test',  $actual, $expected, $results);
    }
}

