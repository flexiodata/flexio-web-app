<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-04
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
        // TEST: yaml extract

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


        // TEST: yaml extract

        // BEGIN TEST
        $content = <<<EOD
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments', $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.8', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.9', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.10', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('B.11', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);


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
        \Flexio\Tests\Check::assertString('B.12', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);


        // BEGIN TEST
        $content = <<<EOD
/*
---
colors:
  - red
  - green
  - blue
---

---
colors:
  - yellow
  - orange
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
        \Flexio\Tests\Check::assertString('B.13', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);


        // BEGIN TEST
        $content = <<<EOD
/*
---
name: hello-world
description: Basic hello world
---
*/

// basic hello world example
exports.flex_handler = function(flex) {
  flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])
}

EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: hello-world
description: Basic hello world
EOD;
        \Flexio\Tests\Check::assertString('B.14', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: currency-converter
# description: |
#   ### Description
#
#   ### Sample Usage
#
#   ### Syntax
#
# deployed: false
# ---
EOD;
        $actual = \Flexio\Base\Yaml::extract($content);
        $expected = <<<EOD
name: currency-converter
description: |
  ### Description

  ### Sample Usage

  ### Syntax

deployed: false
EOD;
        \Flexio\Tests\Check::assertString('B.14', '\Flexio\Base\Yaml::extract(); extract yaml from front matter in comments',  $actual, $expected, $results);
    }
}

