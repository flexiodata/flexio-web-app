<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-01
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: job constant tests

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\CalcField::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\CalcField::MIME_TYPE && $mime_type == 'flexio.calc';
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Jobs\CalcField definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Convert::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Convert::MIME_TYPE && $mime_type == 'flexio.convert';
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Jobs\Convert definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Copy::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Copy::MIME_TYPE && $mime_type == 'flexio.copy';
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Jobs\Copy definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\CreateJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\CreateJob::MIME_TYPE && $mime_type == 'flexio.create';
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Jobs\CreateJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\DistinctJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\DistinctJob::MIME_TYPE && $mime_type == 'flexio.distinct';
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\Jobs\DistinctJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\DuplicateJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\DuplicateJob::MIME_TYPE && $mime_type == 'flexio.duplicate';
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Flexio\Jobs\DuplicateJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\EmailSendJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\EmailSendJob::MIME_TYPE && $mime_type == 'flexio.email';
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Flexio\Jobs\EmailSendJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\FilterJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\FilterJob::MIME_TYPE && $mime_type == 'flexio.filter';
        $expected = true;
        TestCheck::assertBoolean('A.9', '\Flexio\Jobs\FilterJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\FindReplaceJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\FindReplaceJob::MIME_TYPE && $mime_type == 'flexio.replace';
        $expected = true;
        TestCheck::assertBoolean('A.10', '\Flexio\Jobs\FindReplaceJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\GrepJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\GrepJob::MIME_TYPE && $mime_type == 'flexio.grep';
        $expected = true;
        TestCheck::assertBoolean('A.11', '\Flexio\Jobs\GrepJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\GroupJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\GroupJob::MIME_TYPE && $mime_type == 'flexio.group';
        $expected = true;
        TestCheck::assertBoolean('A.12', '\Flexio\Jobs\GroupJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\InputJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\InputJob::MIME_TYPE && $mime_type == 'flexio.input';
        $expected = true;
        TestCheck::assertBoolean('A.13', '\Flexio\Jobs\InputJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\LimitJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\LimitJob::MIME_TYPE && $mime_type == 'flexio.limit';
        $expected = true;
        TestCheck::assertBoolean('A.14', '\Flexio\Jobs\LimitJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\MergeJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\MergeJob::MIME_TYPE && $mime_type == 'flexio.merge';
        $expected = true;
        TestCheck::assertBoolean('A.15', '\Flexio\Jobs\MergeJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\NopJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\NopJob::MIME_TYPE && $mime_type == 'flexio.nop';
        $expected = true;
        TestCheck::assertBoolean('A.16', '\Flexio\Jobs\NopJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\OutputJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\OutputJob::MIME_TYPE && $mime_type == 'flexio.output';
        $expected = true;
        TestCheck::assertBoolean('A.17', '\Flexio\Jobs\OutputJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\ExecuteJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\ExecuteJob::MIME_TYPE && $mime_type == 'flexio.execute';
        $expected = true;
        TestCheck::assertBoolean('A.18', '\Flexio\Jobs\ExecuteJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\PromptJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\PromptJob::MIME_TYPE && $mime_type == 'flexio.prompt';
        $expected = true;
        TestCheck::assertBoolean('A.19', '\Flexio\Jobs\PromptJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\RenameColumnJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\RenameColumnJob::MIME_TYPE && $mime_type == 'flexio.rename';
        $expected = true;
        TestCheck::assertBoolean('A.20', '\Flexio\Jobs\RenameColumnJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\RenameFileJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\RenameFileJob::MIME_TYPE && $mime_type == 'flexio.rename-file';
        $expected = true;
        TestCheck::assertBoolean('A.21', '\Flexio\Jobs\RenameFileJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\SearchJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\SearchJob::MIME_TYPE && $mime_type == 'flexio.search';
        $expected = true;
        TestCheck::assertBoolean('A.22', '\Flexio\Jobs\SearchJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\SelectColumnJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\SelectColumnJob::MIME_TYPE && $mime_type == 'flexio.select';
        $expected = true;
        TestCheck::assertBoolean('A.23', '\Flexio\Jobs\SelectColumnJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\SleepJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\SleepJob::MIME_TYPE && $mime_type == 'flexio.sleep';
        $expected = true;
        TestCheck::assertBoolean('A.24', '\Flexio\Jobs\SleepJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\SortJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\SortJob::MIME_TYPE && $mime_type == 'flexio.sort';
        $expected = true;
        TestCheck::assertBoolean('A.25', '\Flexio\Jobs\SortJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\TransformJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\TransformJob::MIME_TYPE && $mime_type == 'flexio.transform';
        $expected = true;
        TestCheck::assertBoolean('A.26', '\Flexio\Jobs\TransformJob definition type constant',  $actual, $expected, $results);
    }
}
