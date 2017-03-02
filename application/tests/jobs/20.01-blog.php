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
        // Pipe Description: Convert all text in a CSV file to upper case and filter rows
        // Pipe Link: https://www.flex.io/app/project?eid=f3zsl6lkgzdp
        // Blog Link: https://www.flex.io/blog/we-are-reinventing-the-query-builder-well-kinda/

        // BEGIN TEST
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



        // TEST: public blog entry pipe
        // Pipe Name: Import information about the SaaStr podcast and filter it
        // Pipe Link: https://www.flex.io/app/project?eid=fkw7c18kp544
        // Pipe API Link: https://www.flex.io/api/v1/pipes/podcast-search-v1
        // Blog Link: https://www.flex.io/blog/adding-dynamic-content-static-web-page/

        // BEGIN TEST
        $task = \Flexio\Object\Task::create('
        [
            {
                "type": "flexio.input",
                "params": {
                    "items": [
                        {
                            "path": "https:\/\/raw.githubusercontent.com\/flexiodata\/sample-data\/master\/saastr-podcast-20170205.csv"
                        }
                    ]
                },
                "metadata": {
                    "connection_type": "http.api"
                },
                "eid": "k01ckt2m9d9n"
            },
            {
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "format": "delimited",
                        "delimiter": "{comma}",
                        "qualifier": "{double-quote}",
                        "header": true
                    },
                    "output": {
                        "format": "table"
                    }
                },
                "eid": "d8wkhh3v25xy"
            },
            {
                "type": "flexio.select",
                "params": {
                    "columns": [
                        "url",
                        "title",
                        "description",
                        "date",
                        "guest",
                        "position",
                        "company",
                        "category",
                        "biggest challenge",
                        "saas resources",
                        "notes"
                    ]
                },
                "eid": "nhmghbyxdtvc"
            },
            {
                "type": "flexio.filter",
                "params": {
                    "where": "contains(lower(concat(title,description,notes,[saas resources])),lower(\'${filter}\'))"
                },
                "eid": "cyfg5rv0lftm"
            },{
                "eid": "gvv8ypwhw9lz",
                "type": "flexio.convert",
                "params": {
                    "output": {
                        "format": "json"
                    }
                }
            }
        ]
        ')->get();

        $params = [
            "filter" => "bootstrap"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $result = TestUtil::getProcessResult($process,10,122);
        $actual = is_array($result) ? isset_or($result[0],'') : '';
        $expected = 'http:\\/\\/saastr.libsyn.com\\/saastr-026-the-benefits-of-bootstrapping-your-saas-startup-with-laura-roeder-founder-ceo-edgar';
        TestCheck::assertString('A.2', 'Blog Entry Job; check near the first part of the JSON returned',  $actual, $expected, $results);
    }
}
