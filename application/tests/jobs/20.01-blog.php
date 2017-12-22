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
                "op": "input",
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
                "op": "convert",
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
                "op": "select",
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
                "op": "filter",
                "params": {
                    "where": "strpart(birthday, \"\/\", 1) = \"1980\""
                }
            },
            {
                "op": "execute",
                "params": {
                    "lang": "python",
                    "code": "aW1wb3J0IHN5cwoKaW5wdXQgPSAnJzsKZm9yIGxpbmUgaW4gc3lzLnN0ZGluOgogICAgaW5wdXQgKz0gbGluZS51cHBlcigpOwogICAgCnN5cy5zdGRvdXQud3JpdGUoaW5wdXQpCg=="
                }
            }
        ]
        ',true);

        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getReader()->read(50);
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
                "op": "input",
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
                "op": "convert",
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
                "op": "transform",
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
                "op": "filter",
                "params": {
                    "where": "gender = \"FEMALE\""
                },
                "description": "Filter records based on selected conditions"
            }
        ]
        ',true);

        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getReader()->getRows(1535,1);
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
                "op": "input",
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
                "op": "convert",
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
                "op": "select",
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
                "op": "filter",
                "params": {
                    "where": "contains(lower(concat(title,description,notes,[saas resources])),lower(\'${filter}\'))"
                },
                "eid": "cyfg5rv0lftm"
            },{
                "eid": "gvv8ypwhw9lz",
                "op": "convert",
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
        $process = \Flexio\Jobs\Process::create()->setParams($params)->execute($task);
        $actual = $process->getStdout()->getReader()->getRows(10,122);
        $expected = 'http:\\/\\/saastr.libsyn.com\\/saastr-026-the-benefits-of-bootstrapping-your-saas-startup-with-laura-roeder-founder-ceo-edgar';
        TestCheck::assertString('A.3', 'Blog Entry Job; check near the first part of the JSON returned',  $actual, $expected, $results);
    }
}

