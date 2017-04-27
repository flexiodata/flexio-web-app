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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add tests for sample project pipes


        // TEST: demo video pipe
        // Note: updated 20170322; use new data input path

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "eid": "ls3nzjhkkbgx",
                "type": "flexio.input",
                "params": {
                    "items": [
                        {
                            "path": "https:\/\/raw.githubusercontent.com\/flexiodata\/data\/master\/contact-samples\/contacts-ltd2.csv"
                        }
                    ]
                },
                "metadata": {
                    "connection_type": "http"
                }
            },
            {
                "eid": "q3yhx2y611l4",
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "format": "delimited",
                        "delimiter": "{comma}",
                        "header": true
                    },
                    "output": {
                        "format": "table"
                    }
                }
            },
            {
                "eid": "fnk06dsykhfg",
                "type": "flexio.select",
                "params": {
                    "columns": [
                        "givenname",
                        "surname",
                        "streetaddress",
                        "city",
                        "state",
                        "zipcode",
                        "birthday"
                    ]
                }
            },
            {
                "type": "flexio.filter",
                "params": {
                    "where": "strpart(birthday, \"\/\", 1) = \"1980\""
                },
                "eid": "m8065fhls7lv"
            },
            {
                "eid": "drmxq9f8zl4g",
                "type": "flexio.execute",
                "params": {
                    "lang": "python",
                    "code": "aW1wb3J0IHN5cwoKaW5wdXQgPSAnJzsKZm9yIGxpbmUgaW4gc3lzLnN0ZGluOgogICAgaW5wdXQgKz0gbGluZS51cHBlcigpOwogICAgCnN5cy5zdGRvdXQud3JpdGUoaW5wdXQpCg=="
                }
            }
        ]
        ',true);

        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $result = TestUtil::getProcessResult($process,0,50);
        $actual = is_array($result) && isset($result[0]) ? $result[0] : '';
        $expected = 'GIVENNAME,SURNAME,STREETADDRESS,CITY,STATE,ZIPCODE';
        TestCheck::assertString('A.1', 'Demo Video; pipe for a demo video',  $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);



        // TEST: public blog entry pipe
        // Pipe Description: Convert all text in a CSV file to upper case and filter rows
        // Pipe Link: https://www.flex.io/app/project?eid=f3zsl6lkgzdp
        // Blog Link: https://www.flex.io/blog/we-are-reinventing-the-query-builder-well-kinda/
        // DEPRECATED: note, the following logic is the old logic, but is maintained here because
        // it's a useful test; the new blog pipe at the same location follows the standard
        // 'contact-refinement' example that's installed in the demo data

        // BEGIN TEST
        $task = json_decode('
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
                        "connection_type": "http"
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
                "name": "Filter",
                "type": "flexio.filter",
                "params": {
                    "where": "gender = \"FEMALE\""
                },
                "description": "Filter records based on selected conditions"
            }
        ]
        ',true);

        $process = \Flexio\Object\Process::create()->setTask($task)->run(false);
        $result = TestUtil::getProcessSingleOutputResult($process,true,1535,1); // 1536 rows in output; get the last one
        $actual = $result['content'][0] ?? array();
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
        TestCheck::assertArray('A.2', 'Blog Entry Job; check the last row produced by the job',  $actual, $expected, $results);



        // TEST: public blog entry pipe
        // Pipe Name: Saastr Podcast Search
        // Pipe Link: https://www.flex.io/app/pipe/fm11vqhlrljj
        // Pipe API Link: https://www.flex.io/api/v1/pipes/flexio-saastr-podcast-search-v1
        // Blog Link: https://www.flex.io/blog/adding-dynamic-content-static-web-page/
        // Repository: https://github.com/flexiodata/examples/tree/master/saastr-podcast-search
        // Note: updated 20170322; use new data input path

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "type": "flexio.input",
                "params": {
                    "items": [
                        {
                            "path": "https:\/\/raw.githubusercontent.com\/flexiodata\/examples\/master\/saastr-podcast-search\/saastr-podcast-20170205.csv"
                        }
                    ]
                },
                "metadata": {
                    "connection_type": "http"
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
        ',true);

        $params = [
            "filter" => "bootstrap"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $result = TestUtil::getProcessResult($process,10,122);
        $actual = is_array($result) && isset($result[0]) ? $result[0] : '';
        $expected = 'http:\\/\\/saastr.libsyn.com\\/saastr-026-the-benefits-of-bootstrapping-your-saas-startup-with-laura-roeder-founder-ceo-edgar';
        TestCheck::assertString('A.3', 'Blog Entry Job; check near the first part of the JSON returned',  $actual, $expected, $results);
    }
}

