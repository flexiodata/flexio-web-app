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
# name: hello-world
# description: Basic "Hello, World" function
# deployed: true
# ---

# basic hello world example
def flex_handler(flex):
    flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])

EOD;
        $yaml = \Flexio\Base\Yaml::extract($content);
        $actual = \Flexio\Base\Yaml::parse($yaml);
        $expected = '
        {
            "name": "hello-world",
            "description": "Basic \"Hello, World\" function",
            "deployed": true
        }
        ';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Yaml::parse(); extract and parse yaml from front matter in comments',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: hello-world
# description: |
#     ### Description
#     Basic "Hello, World" function
# deployed: false
# ---

# basic hello world example
def flex_handler(flex):
    flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])

EOD;
        $yaml = \Flexio\Base\Yaml::extract($content);
        $actual = \Flexio\Base\Yaml::parse($yaml);
        $expected = '
        {
            "name": "hello-world",
            "description": "### Description\nBasic \"Hello, World\" function\n",
            "deployed": false
        }
        ';
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\Yaml::parse(); extract and parse yaml from front matter in comments',  $actual, $expected, $results);

        // BEGIN TEST
        $content = <<<EOD
# ---
# name: currency-converter
# description: |2
#
#   ### Description
#   Converts a value to another currency using the https://exchangeratesapi.io API.
#
#   ### Sample Usage
#
#   =FLEX(":team/currency-convert",A10,\$A$1,\$A$2)<br>
#   =FLEX(":team/currency-convert",A10,\$A$1,\$A$2,"2019-01-31)<br>
#   =FLEX(":team/currency-convert",100,"USD","EUR","2019-01-31")<br>
#
#   ### Syntax
#
#   FLEX(":team/currency-convert", amt, cur1, cur2, [date])
#
#   Property | Type | Description
#   ---------- | ---------- | ----------
#   `amt` | number | The value to convert from one currency to another
#   `cur1` | string | The currency to convert from (e.g. "USD", "EUR")
#   `cur2` | string | The currency to convert to (e.g. "USD", "EUR")
#   `[date]` | string | (optional) The exchange rate date in YYYY-DD-MM format.
#
# deployed: false
# ---

# main entry point
def flex_handler(flex):
    # TODO: implement
    flex.end('')

EOD;
        $yaml = \Flexio\Base\Yaml::extract($content);
        $pipe_info = \Flexio\Base\Yaml::parse($yaml);
        $actual = $pipe_info['description'];
        $expected = <<<EOD

### Description
Converts a value to another currency using the https://exchangeratesapi.io API.

### Sample Usage

=FLEX(":team/currency-convert",A10,\$A$1,\$A$2)<br>
=FLEX(":team/currency-convert",A10,\$A$1,\$A$2,"2019-01-31)<br>
=FLEX(":team/currency-convert",100,"USD","EUR","2019-01-31")<br>

### Syntax

FLEX(":team/currency-convert", amt, cur1, cur2, [date])

Property | Type | Description
---------- | ---------- | ----------
`amt` | number | The value to convert from one currency to another
`cur1` | string | The currency to convert from (e.g. "USD", "EUR")
`cur2` | string | The currency to convert to (e.g. "USD", "EUR")
`[date]` | string | (optional) The exchange rate date in YYYY-DD-MM format.

EOD;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\Yaml::parse(); extract and parse yaml from front matter in comments',  $actual, $expected, $results);
    }
}
