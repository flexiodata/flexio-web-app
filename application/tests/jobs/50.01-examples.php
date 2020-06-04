<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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
        // TEST: example pipes

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "url" => "https://raw.githubusercontent.com/flexiodata/data/master/contact-samples/contacts-ltd1.csv"
            ],
            [
                "op" => "convert",
                "input" => ["format" => "delimited", "delimiter" => "{comma}", "header" => true],
                "output" => ["format" => "ndjson"]
            ],
            [
                "op" => "execute",
                "lang" => "python",
                "code" => "aW1wb3J0IGpzb24NCmRlZiBmbGV4aW9faGFuZGxlcihjb250ZXh0KToNCiAgICBmb3Igcm93IGluIGNvbnRleHQuaW5wdXQ6DQogICAgICAgIGNvbnRleHQub3V0cHV0LndyaXRlKGpzb24uZHVtcHMocm93KS51cHBlcigpICsgIlxuIikNCg=="
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "{\"ID\": \"1\", \"GENDER\": \"FEMALE\", \"GIVENNAME\": \"LYNN\", \"MIDDLEINITIAL\": \"S\", \"SURNAME\": \"KUHL\", \"STREE";
        \Flexio\Tests\Check::assertString('A.1', 'Example Pipe; test for chaining',  $actual, $expected, $results);


        // TEST: logic similar to demo video pipe

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "url" => "https://raw.githubusercontent.com/flexiodata/data/master/contact-samples/contacts-ltd2.csv"
            ],
            [
                "op" => "convert",
                "input" => ["format" => "delimited", "delimiter" => "{comma}", "header" => true],
                "output" => ["format" => "ndjson"]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $actual = json_decode($process->getStdout()->getReader()->readline(),true);
        $expected = json_decode('
        {
            "id":"1",
            "gender":"female",
            "firstname":"Lynn",
            "middle":"S",
            "lastname":"Kuhl",
            "address":"1690 Mill Street",
            "city":"Greenville",
            "state":"SC",
            "zipcode":"29615",
            "telephone":"864-266-3412",
            "birthday":"1983/03/20",
            "email":
            "Lynn.S.Kuhl@mailinator.com"
        }
        ',true);
        \Flexio\Tests\Check::assertArray('B.1', 'Example; test for pipe similar to demo video',  $actual, $expected, $results);


        // TEST: public blog entry pipe
        // Pipe Description: Convert all text in a CSV file to upper case and filter rows
        // Blog Link: https://www.flex.io/blog/we-are-reinventing-the-query-builder-well-kinda/
        // DEPRECATED: note, the following logic differs slightly from the blog entry now,
        // (e.g. the sample data set varies a little, and the input uses the 'request' job instead of the
        // old 'input' job; however the the following is still useful as a close test for the blog entry)

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "url" => "https://raw.githubusercontent.com/flexiodata/data/master/contact-samples/contacts-ltd1.csv"
            ],
            [
                "op" => "convert",
                "input" => ["format" => "delimited", "delimiter" => "{comma}", "header" => true, "qualifier" => "{double_quote}"]
            ]
        ]);
        $process = \Flexio\Jobs\Process::create()->execute($task);
        $stdout_reader = $process->getStdout()->getReader();

        $actual = [{}];

        $idx = 0;
        while (true)
        {
            $row = $stdout_reader->readline();
            if ($row === false)
                break;

            if ($idx === 1535)
            {
                $actual = $row;
                break;
            }

            $idx++;
        }

        $expected = json_decode('
        {
            "id":"1536",
            "gender":"female",
            "givenname":"Elizabeth",
            "middleinitial":"S",
            "surname":"Marino",
            "streetaddress":"3469 Saints Alley",
            "city":"Tampa",
            "state":"FL",
            "zipcode":"33614",
            "country":"US",
            "emailaddress":"Elizabeth.S.Marino@mailinator.com",
            "telephonenumber":"813-756-4552",
            "mothersmaiden":"Glass",
            "birthday":"12/20/1968"
        }
        ',true);
        \Flexio\Tests\Check::assertArray('C.1', 'Blog Entry Job; check the last row produced by the job',  $actual, $expected, $results);

/*
        // note: example repository is now private since the examples rely on the old
        // JS SDK with "chained JSON jobs", some of which are no longer available; in
        // addition, the following logic includes some of these jobs that are no longer
        // available (e.g. "select" and "filter"); in addition, the saastr podcast data
        // is part of this example repo, so the "request" job will also fail because the
        // repo is private

        // TEST: public blog entry pipe
        // Pipe Name: Saastr Podcast Search
        // Pipe Link: https://www.flex.io/app/pipe/fm11vqhlrljj
        // Pipe API Link: https://www.flex.io/api/v1/pipes/flexio-saastr-podcast-search-v1
        // Blog Link: https://www.flex.io/blog/adding-dynamic-content-static-web-page/
        // Repository: https://github.com/flexiodata/examples/tree/master/saastr-podcast-search
        // Note: updated 20170322; use new data input path
        // Note: updated 20180717; use new data input path in test below

        // BEGIN TEST
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "request",
                "url" => "https://raw.githubusercontent.com/flexiodata/examples/master/demo-saastr-podcast-search/source-data/saastr-podcast-20170205.csv"
            ],
            [
                "op" => "convert",
                "input" => ["format" => "delimited", "delimiter" => "{comma}", "qualifier" => "{double-quote}", "header" => true],
                "output" => ["format" => "ndjson"]
            ],
            [
                "op" => "select",
                "columns" => [
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
            ],
            [
                "op" => "filter",
                "where" => "contains(lower(concat(title,description,notes,[saas resources])),lower('\${filter}'))"
            ],
            [
                "op" => "convert",
                "output" => ["format" => "json"]
            ]
        ]);
        $params = [
            "filter" => "bootstrap"
        ];
        $process = \Flexio\Jobs\Process::create()->setParams($params)->execute($task);
        $actual = $process->getStdout()->getReader()->read(100);
        $expected = "[\n{\"url\":\"http://saastr.libsyn.com/saastr-026-the-benefits-of-bootstrapping-your-saas-startup-with-l";
        \Flexio\Tests\Check::assertString('D.1', 'Blog Entry Job; check near the first part of the JSON returned',  $actual, $expected, $results);
*/
    }
}

