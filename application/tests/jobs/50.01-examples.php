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
        // TEST: example pipes installed for new users

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "op": "request",
                "description": "Read CSV file from GitHub url",
                "params": {
                    "url": "https:\/\/raw.githubusercontent.com\/flexiodata\/data\/master\/contact-samples\/contacts-ltd1.csv"
                }
            },
            {
                "op": "convert",
                "description": "Convert from CSV to table",
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
                "description": "Select only the relevant columns",
                "params": {
                    "columns": [
                        "streetaddress"
                    ]
                }
            },
            {
                "op": "execute",
                "description": "Make all text uppercase for consistency",
                "params": {
                    "lang": "python",
                    "code": "aW1wb3J0IGpzb24NCmRlZiBmbGV4aW9faGFuZGxlcihjb250ZXh0KToNCiAgICBmb3Igcm93IGluIGNvbnRleHQuaW5wdXQ6DQogICAgICAgIGNvbnRleHQub3V0cHV0LndyaXRlKGpzb24uZHVtcHMocm93KS51cHBlcigpICsgIlxuIikNCg=="
                }
            }
        ]
        ',true);

        $process = \Flexio\Jobs\Process::create();
        foreach ($task as $t)
        {
            $process->execute($t);
        }
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "{\"STREETADDRESS\": \"1690 MILL STREET\"}\n{\"STREETADDRESS\": \"783 ELK AVENUE\"}\n{\"STREETADDRESS\": \"1748 HE";
        \Flexio\Tests\Check::assertString('A.1', 'Example Pipe; test for pipe installed for new users',  $actual, $expected, $results);

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "op": "request",
                "description": "Read repository commit histories from GitHub API",
                "params": {
                    "url": "https:\/\/api.github.com\/repos\/flexiodata\/docs-api\/commits"
                }
            },
            {
                "op": "convert",
                "description": "Convert from json to table",
                "params": {
                    "input": {
                        "format": "json"
                    },
                    "output": {
                        "format": "table"
                    }
                }
            },
            {
                "op": "calc",
                "description": "Create new field \'repository\' which concatenates two different parts of the \'commit.tree.url\' field to isolate \'user/repo\'",
                "params": {
                    "name": "repository",
                    "expression": "concat(strpart([commit.tree.url],\'\/\',5),\'\/\',strpart([commit.tree.url],\'\/\',6))"
                }
            },
            {
                "op": "select",
                "description": "Select only the relevant columns",
                "params": {
                    "columns": [
                        "repository",
                        "commit.author.date",
                        "commit.author.name",
                        "commit.author.email",
                        "commit.committer.name",
                        "commit.committer.email",
                        "commit.message"
                    ]
                }
            }
        ]
        ',true);
        $process = \Flexio\Jobs\Process::create();
        foreach ($task as $t)
        {
            $process->execute($t);
        }
        $actual = $process->getStdout()->getReader()->getRows(0,1);
        $expected = json_decode('[{"repository":"flexiodata\/docs-api","commit.author.date":"2017-11-18T12:16:39Z","commit.author.name":"Ben Williams","commit.author.email":"ben@flex.io","commit.committer.name":"Ben Williams","commit.committer.email":"ben@flex.io","commit.message":"missing comma"}]',true);
        \Flexio\Tests\Check::assertArray('A.2', 'Example Pipe; test for pipe installed for new users',  $actual, $expected, $results);



        // TEST: logic similar to demo video pipe

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "op": "request",
                "params": {
                    "url": "https:\/\/raw.githubusercontent.com\/flexiodata\/data\/master\/contact-samples\/contacts-ltd2.csv"
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
            }
        ]
        ',true);

        $process = \Flexio\Jobs\Process::create();
        foreach ($task as $t)
        {
            $process->execute($t);
        }
        $actual = $process->getStdout()->getReader()->readRow(0);
        $expected = json_decode('{"city":"Jackson","state":"MS","zipcode":"39201","birthday":"1980\/12\/29"}',true);
        \Flexio\Tests\Check::assertArray('B.1', 'Example; test for pipe similar to demo video',  $actual, $expected, $results);



        // TEST: public blog entry pipe
        // Pipe Description: Convert all text in a CSV file to upper case and filter rows
        // Blog Link: https://www.flex.io/blog/we-are-reinventing-the-query-builder-well-kinda/
        // DEPRECATED: note, the following logic differs slightly from the blog entry now,
        // (e.g. the input uses the 'request' job instead of the old 'input' job; however the
        // the following is still useful as a close test for the blog entry)

        // BEGIN TEST
        $task = json_decode('
        [
            {
                "op": "request",
                "params": {
                    "url": "https:\/\/webapps.goldprairie.com\/filetrans\/?download=kfjgssycsm"
                }
            },
            {
                "op": "convert",
                "params": {
                    "input": {
                        "format": "delimited_text",
                        "delimiter": "{comma}",
                        "header_row": true,
                        "text_qualifier": "{double_quote}"
                    }
                }
            },
            {
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
                }
            },
            {
                "op": "filter",
                "params": {
                    "where": "gender = \"FEMALE\""
                }
            }
        ]
        ',true);

        $process = \Flexio\Jobs\Process::create();
        foreach ($task as $t)
        {
            $process->execute($t);
        }
        $rows = $process->getStdout()->getReader()->getRows(1535,1);
        $actual = $rows[0];
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
        \Flexio\Tests\Check::assertArray('C.1', 'Blog Entry Job; check the last row produced by the job',  $actual, $expected, $results);



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
                "op": "request",
                "params": {
                    "url": "https:\/\/raw.githubusercontent.com\/flexiodata\/examples\/master\/saastr-podcast-search\/saastr-podcast-20170205.csv"
                }
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
                }
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
                }
            },
            {
                "op": "filter",
                "params": {
                    "where": "contains(lower(concat(title,description,notes,[saas resources])),lower(\'${filter}\'))"
                }
            },{
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
        $process = \Flexio\Jobs\Process::create()->setParams($params);
        foreach ($task as $t)
        {
            $process->execute($t);
        }
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "[\n{\"url\":\"http:\/\/saastr.libsyn.com\/saastr-026-the-benefits-of-bootstrapping-your-saas-startup-wit";
        \Flexio\Tests\Check::assertString('D.1', 'Blog Entry Job; check near the first part of the JSON returned',  $actual, $expected, $results);
    }
}

