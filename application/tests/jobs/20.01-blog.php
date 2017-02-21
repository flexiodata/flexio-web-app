<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-16
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: public blog entry pipe
        // PIPE NAME: Convert all text in a CSV file to upper case and filter rows
        // PIPE LINK: https://www.flex.io/app/project?eid=f3zsl6lkgzdp
        // BLOG LINK: https://www.flex.io/blog/we-are-reinventing-the-query-builder-well-kinda/

        // SETUP
        $task = \Flexio\Object\Task::create('
        [
            {
                "eid": "p1zsvtts2gr7",
                "name": "Input",
                "type": "flexio.input",
                "params": {
                    "items": [
                        {
                            "name": "https:\/\/webapps.goldprairie.com\/filetrans\/?download=kfjgssycsm",
                            "path": "https:\/\/webapps.goldprairie.com\/filetrans\/?download=kfjgssycsm"
                        }
                    ],
                    "connection": {
                        "connection_type": "http.api"
                    }
                },
                "description": "Add source files and tables for pipe processing."
            },
            {
                "eid": "d7hzg2mnpx2x",
                "name": "Convert File",
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "format": "delimited_text",
                        "delimiter": "{comma}",
                        "header_row": true,
                        "text_qualifier": "{double_quote}"
                    }
                },
                "description": "Transform text files into tables"
            },
            {
                "eid": "gdj722l8426y",
                "name": "Transform",
                "type": "flexio.transform",
                "params": {
                    "columns": [
                        "*"
                    ],
                    "operations": [
                        {
                            "case": "upper",
                            "operation": "case"
                        }
                    ]
                },
                "description": "Change the data type and transform values of selected columns"
            },
            {
                "eid": "d5gg6qmf9bt8",
                "name": "Filter",
                "type": "flexio.filter",
                "params": {
                    "condition": {
                        "items": [
                            {
                                "left": [
                                    "gender"
                                ],
                                "right": "FEMALE",
                                "operator": "eq"
                            }
                        ]
                    }
                },
                "description": "Filter records based on selected conditions"
            },
            {
                "eid": "htq3vk5z3t47",
                "name": "Output",
                "type": "flexio.output",
                "params": {
                    "path": "",
                    "connection": {
                        "connection_type": "download.api"
                    }
                },
                "description": "Add a location in which to output pipe results."
            }
        ]
        ')->get();



        // TEST: Blog Entry Job

        // BEGIN TEST
        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $result = TestUtil::getProcessSingleOutputResult($process,true,1535,1); // 1536 rows in output; get the last one
        $actual = isset_or($result['rows'][0],array());
        $expected = json_decode('
        {
            "number": "3000",
            "gender": "FEMALE",
            "givenname": "EFFIE",
            "middleinitial": "S",
            "surname": "BRADBERRY",
            "streetaddress": "4065 CHICAGO AVENUE",
            "city": "FRESNO",
            "state": "CA",
            "zipcode": "93721",
            "country": "US",
            "emailaddress": "EFFIE.S.BRADBERRY@POOKMAIL.COM",
            "telephonenumber": "559-619-7731",
            "mothersmaiden": "SCHNEIDER",
            "birthday": "12/29/1959",
            "cctype": "VISA",
            "ccnumber": "4532620905918045",
            "cvv2": "812",
            "ccexpires": "10/2012",
            "nationalid": "610-07-3447",
            "ups": "1Z 3W5 6V5 59 2261 267 6"
        }
        ',true);
        TestCheck::assertArray('A.1', 'Blog Entry Job; check the last row produced by the job',  $actual, $expected, $results);
    }
}
