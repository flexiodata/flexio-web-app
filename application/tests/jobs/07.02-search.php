<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-03
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
        $create = json_decode('{
            "type": "flexio.create",
            "params": {
                "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE.'",
                "columns": [
                    {"name":"c1","type":"character","width":3,"scale":0},
                    {"name":"c2","type":"character","width":20,"scale":0},
                    {"name":"n1","type":"numeric","width":2,"scale":0},
                    {"name":"n2","type":"numeric","width":10,"scale":2},
                    {"name":"d1","type":"date","width":4,"scale":0},
                    {"name":"b1","type":"boolean","width":1,"scale":0}
                ],
                "content": [
                    ["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
                    ["c a","| \/",null,"0.00","1970-11-22",null],
                    [" -1",":;\"\'","0","0.99","1999-12-31",false],
                    ["0% ",",.?","1","4.56","2000-01-01",null],
                    [null,"~`!@#$%^&*-+_=","2",null,null,true]
                ]
            }
        }',true);



        // TEST: Search Job; search across all columns with no matches

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
		]
        ';
		TestCheck::assertArray('A.1', 'Search Job; return no rows if no search values are specified',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": null,
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
		]
        ';
		TestCheck::assertArray('A.2', 'Search Job; don\'t match the "null" search term to null values in rows',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "sample",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
		]
        ';
		TestCheck::assertArray('A.3', 'Search Job; return no rows if no values match the search term',  $actual, $expected, $results);



        // TEST: Search Job; search across all columns with potential whole-value matches

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "aBC",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true]
		]
        ';
		TestCheck::assertArray('B.1', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "0%",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["0% ",",.?","1","4.56","2000-01-01",null]
		]
        ';
		TestCheck::assertArray('B.2', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "0.00",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["c a","| \/",null,"0.00","1970-11-22",null]
        ]
        ';
		TestCheck::assertArray('B.3', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": ",.?",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["0% ",",.?","1","4.56","2000-01-01",null]
		]
        ';
		TestCheck::assertArray('B.4', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('B.5', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "1",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["0% ",",.?","1","4.56","2000-01-01",null]
		]
        ';
		TestCheck::assertArray('B.6', 'Search Job; return matching rows for match on whole value in one of the fields',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "a",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["c a","| \/",null,"0.00","1970-11-22",null]
		]
        ';
		TestCheck::assertArray('B.7', 'Search Job; by default, don\'t return rows with partially matching values',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "0",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('B.8', 'Search Job; by default, don\'t return rows with partially matching values',  $actual, $expected, $results);



        // TEST: Search Job; search across all columns with potential case-insentivie matches

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "C A",
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["c a","| \/",null,"0.00","1970-11-22",null]
		]
        ';
		TestCheck::assertArray('C.1', 'Search Job; return matching rows for case-insensitive match on whole value in one of the fields',  $actual, $expected, $results);



        // TEST: Search Job; search on single columns with potential whole-value matches

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "aBC",
                "columns": ["c1"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true]
		]
        ';
		TestCheck::assertArray('D.1', 'Search Job; return matching rows for match on whole value in a specified column',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "aBC",
                "columns": ["c2"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
		]
        ';
		TestCheck::assertArray('D.2', 'Search Job; if a column is specified, don\'t return matching rows if the match is for another column',  $actual, $expected, $results);



        // TEST: Search Job; search on combinations of fields

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "aBC",
                "columns": ["c1","c1"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true]
		]
        ';
		TestCheck::assertArray('E.1', 'Search Job; don\'t fail if the same field is listed multiple times, but include field in the search',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["c1","c2"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('E.2', 'Search Job; return rows that have whole-value matches only in the specified columns',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["c2","n1"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true]
		]
        ';
		TestCheck::assertArray('E.3', 'Search Job; return rows that have whole-value matches only in the specified columns',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["c1","n1"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('E.4', 'Search Job; return rows that have whole-value matches only in the specified columns',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["b1","d1","n1","n2","c1"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('E.5', 'Search Job; return rows that have whole-value matches only in the specified columns',  $actual, $expected, $results);

		// BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": "-1",
                "columns": ["n1","*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
			[" -1",":;\"\'","0","0.99","1999-12-31",false]
		]
        ';
		TestCheck::assertArray('E.6', 'Search Job; always allow "*" as a wild-card for all the fields',  $actual, $expected, $results);



        // TEST: Search Job; search on multiple search terms

        // BEGIN TEST
        $task = array($create, json_decode('{
            "type": "flexio.search",
            "params": {
                "search": ["aBC", "c a", "2000"],
                "columns": ["*"]
            }
        }',true));
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $actual = TestUtil::getContent($process->getStdout());
		$expected = '
		[
			["aBC","()[]{}\u003C\u003E","-1","-1.23","1776-07-04",true],
			["c a","| \/",null,"0.00","1970-11-22",null],
			["0% ",",.?","1","4.56","2000-01-01",null]
		]
        ';
		TestCheck::assertArray('F.1', 'Search Job; if multiple search parameters are specified, look for these across the specified columns',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);
    }
}
