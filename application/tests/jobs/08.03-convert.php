<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-23
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
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "delimited",
                    "delimiter" => "{tab}",
                    "header" => false,
                    "qualifier" => "{none}"
                ]
            ]
        ]);



        // TEST: Convert; basic content test

        // BEGIN TEST
        $stream = \Flexio\Tests\Util::createStream('/csv/03.02-content-ascii.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '["f1"]';
        \Flexio\Tests\Check::assertArray('A.1', 'Convert CSV; single fieldname should create correctly',  $actual, $expected, $results);


return;

        // TEST: Convert; basic content upload test

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"a1", "b1"
"a2", "b2"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());

        // note: this line uses excel's rules
        //$expected = '[["a1"," \\"b1\\""],["a2"," \\"b2\\""]]';

        // note: this line uses php's fgetcsv rules
        $expected = '[["a1","b1"],["a2","b2"]]';
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Job; basic content upload test',  $actual, $expected, $results);






        // TEST: Convert; content with troublesome characters

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"", ","
"b,", ",b"
"c,d", ","
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());

        // note: this line uses excel's rules
        //$expected = '[[""," \\",\\""],["b,"," \\",b\\""],["c,d"," \\",\\""]]';

        // note: This line uses php's fgetcsv rules
        $expected = '[["",","],["b,",",b"],["c,d",","]]';
        \Flexio\Tests\Check::assertArray('B.1', 'Convert; troublesome characters in content',  $actual, $expected, $results);



        // TEST: CSV Upload Data; content with unicode; note: following tests try
        // various types of unicode combinations based on original problem documented
        // in issue:FLEX-21

        // BEGIN TEST
        $data = <<<EOD
vend_name
SchwÃ¤bische SoftwarelÃ¶sungen AG
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        \Flexio\Tests\Check::assertArray('C.1', 'Convert; troublesome characters in content',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "vend_name"
            "SchwÃ¤bische SoftwarelÃ¶sungen AG"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        \Flexio\Tests\Check::assertArray('C.2', 'Convert; troublesome characters in content',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"vend_name"
"SchwÃ¤bische SoftwarelÃ¶sungen AG"
EOD;
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-16');
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        \Flexio\Tests\Check::assertArray('C.3', 'Convert; troublesome characters in content',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: Convert; content with integer numbers

        // BEGIN TEST
        $data = <<<EOD
"field1"
0
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0"]]';
        \Flexio\Tests\Check::assertArray('D.1', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field1"
            1
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1"]]';
        \Flexio\Tests\Check::assertArray('D.2', 'Convert Job; numeric range check',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-1
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-1"]]';
        \Flexio\Tests\Check::assertArray('D.3', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
9999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["9999"]]';
        \Flexio\Tests\Check::assertArray('D.4', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-9999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-9999"]]';
        \Flexio\Tests\Check::assertArray('D.5', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
999999999999999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["999999999999999"]]';
        \Flexio\Tests\Check::assertArray('D.6', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-999999999999999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-999999999999999"]]';
        \Flexio\Tests\Check::assertArray('D.7', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
9999999999999999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["9999999999999999"]]';
        \Flexio\Tests\Check::assertArray('D.8', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-9999999999999999
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-9999999999999999"]]';
        \Flexio\Tests\Check::assertArray('D.9', 'Convert Job; numeric range check',  $actual, $expected, $results);



        // TEST: Convert; content with decimal numbers

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.0
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0.0"]]';
        \Flexio\Tests\Check::assertArray('E.1', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.00
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0.00"]]';
        \Flexio\Tests\Check::assertArray('E.2', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.0001
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0.0001"]]';
        \Flexio\Tests\Check::assertArray('E.3', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-0.0001
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-0.0001"]]';
        \Flexio\Tests\Check::assertArray('E.4', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.000000000001
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0.000000000001"]]';
        \Flexio\Tests\Check::assertArray('E.5', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-0.000000000001
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["-0.000000000001"]]';
        \Flexio\Tests\Check::assertArray('E.6', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
1.1
0.0001
-0.0001
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1.1000"],["0.0001"],["-0.0001"]]';
        \Flexio\Tests\Check::assertArray('E.7', 'Convert Job; numeric range check',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: Convert; troublesome characters in a numeric field

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1.1"
"0.0001"
"  0  "
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1.1"],["0.0001"],["  0  "]]';
        \Flexio\Tests\Check::assertArray('F.1', 'Convert Job; if non-numeric characters are present, keep field in character format',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1.1"
"0.0001"
"123,456.78"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1.1"],["0.0001"],["123,456.78"]]';
        \Flexio\Tests\Check::assertArray('F.2', 'Convert Job; if non-numeric characters are present, keep field in character format',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"0"
"0"
"1776-07-04"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["0"],["0"],["1776-07-04"]]';
        \Flexio\Tests\Check::assertArray('F.3', 'Convert Job; if dates are mixed in with numbers don\'t confuse them for numerics',  $actual, $expected, $results);



        // TEST: Convert; content with dates, single format

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1999-12-31"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1999-12-31"]]';
        \Flexio\Tests\Check::assertArray('G.1', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"2001-01-01"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["2001-01-01"]]';
        \Flexio\Tests\Check::assertArray('G.2', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1999/12/31"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1999-12-31"]]';
        \Flexio\Tests\Check::assertArray('G.3', 'Convert Job; valid date values should load',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"2001/01/01"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["2001-01-01"]]';
        \Flexio\Tests\Check::assertArray('G.4', 'Convert Job; valid date values should load',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"19991231"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1999-12-31"]]';
        \Flexio\Tests\Check::assertArray('G.5', 'Convert Job; valid date values should load',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"20010101"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["2001-01-01"]]';
        \Flexio\Tests\Check::assertArray('G.6', 'Convert Job; valid date values should load',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: Convert; content with dates, mixed format

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1969-07-20"],["1970-01-01"]]';
        \Flexio\Tests\Check::assertArray('H.1', 'Convert Job; valid date values should load if the format is recognized',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"0"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1969-07-20"],["19700101"],["0"]]';
        \Flexio\Tests\Check::assertArray('H.2', 'Convert Job; mixed date values with blank or zero values should end up as character fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"19690720"
19700101
0
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["19690720"],["19700101"],["0"]]';
        \Flexio\Tests\Check::assertArray('H.3', 'Convert Job; mixed date values with zero values should end up as numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"false"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1969-07-20"],["19700101"],["false"]]';
        \Flexio\Tests\Check::assertArray('H.4', 'Convert Job; mixed date values with invalid date values should be imported as characters',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"January"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1969-07-20"],["19700101"],["January"]]';
        \Flexio\Tests\Check::assertArray('H.5', 'Convert Job; mixed date values with values that can\'t be interpreted correctly should be imported as characters',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"1/89"
EOD;
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = '[["1969-07-20"],["19700101"],["1/89"]]';
        \Flexio\Tests\Check::assertArray('H.6', 'Convert Job; mixed date values with values that can\'t be interpreted correctly should be imported as characters',  $actual, $expected, $results);
    }
}
