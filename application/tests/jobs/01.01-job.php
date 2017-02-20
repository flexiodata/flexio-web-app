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


class Test
{
    public function run(&$results)
    {
        // TEST: job constant tests

        // BEGIN TEST
        $definition = json_decode(CalcFieldJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == CalcFieldJob::MIME_TYPE && $mime_type == 'flexio.calc';
        $expected = true;
        TestCheck::assertBoolean('A.1', 'CalcFieldJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(ConvertJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == ConvertJob::MIME_TYPE && $mime_type == 'flexio.convert';
        $expected = true;
        TestCheck::assertBoolean('A.2', 'ConvertJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(CopyJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == CopyJob::MIME_TYPE && $mime_type == 'flexio.copy';
        $expected = true;
        TestCheck::assertBoolean('A.4', 'CopyJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(CreateJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == CreateJob::MIME_TYPE && $mime_type == 'flexio.create';
        $expected = true;
        TestCheck::assertBoolean('A.5', 'CreateJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(DistinctJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == DistinctJob::MIME_TYPE && $mime_type == 'flexio.distinct';
        $expected = true;
        TestCheck::assertBoolean('A.6', 'DistinctJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(DuplicateJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == DuplicateJob::MIME_TYPE && $mime_type == 'flexio.duplicate';
        $expected = true;
        TestCheck::assertBoolean('A.7', 'DuplicateJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(EmailSendJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == EmailSendJob::MIME_TYPE && $mime_type == 'flexio.email';
        $expected = true;
        TestCheck::assertBoolean('A.8', 'EmailSendJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(FilterJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == FilterJob::MIME_TYPE && $mime_type == 'flexio.filter';
        $expected = true;
        TestCheck::assertBoolean('A.9', 'FilterJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(FindReplaceJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == FindReplaceJob::MIME_TYPE && $mime_type == 'flexio.replace';
        $expected = true;
        TestCheck::assertBoolean('A.10', 'FindReplaceJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(GrepJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == GrepJob::MIME_TYPE && $mime_type == 'flexio.grep';
        $expected = true;
        TestCheck::assertBoolean('A.11', 'GrepJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(GroupJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == GroupJob::MIME_TYPE && $mime_type == 'flexio.group';
        $expected = true;
        TestCheck::assertBoolean('A.12', 'GroupJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(InputJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == InputJob::MIME_TYPE && $mime_type == 'flexio.input';
        $expected = true;
        TestCheck::assertBoolean('A.13', 'InputJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(LimitJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == LimitJob::MIME_TYPE && $mime_type == 'flexio.limit';
        $expected = true;
        TestCheck::assertBoolean('A.14', 'LimitJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(MergeJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == MergeJob::MIME_TYPE && $mime_type == 'flexio.merge';
        $expected = true;
        TestCheck::assertBoolean('A.15', 'MergeJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(NopJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == NopJob::MIME_TYPE && $mime_type == 'flexio.nop';
        $expected = true;
        TestCheck::assertBoolean('A.16', 'NopJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(OutputJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == OutputJob::MIME_TYPE && $mime_type == 'flexio.output';
        $expected = true;
        TestCheck::assertBoolean('A.17', 'OutputJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(ExecuteJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == ExecuteJob::MIME_TYPE && $mime_type == 'flexio.execute';
        $expected = true;
        TestCheck::assertBoolean('A.18', 'ExecuteJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(PromptJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == PromptJob::MIME_TYPE && $mime_type == 'flexio.prompt';
        $expected = true;
        TestCheck::assertBoolean('A.19', 'PromptJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(RenameColumnJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == RenameColumnJob::MIME_TYPE && $mime_type == 'flexio.rename';
        $expected = true;
        TestCheck::assertBoolean('A.20', 'RenameColumnJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(RenameFileJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == RenameFileJob::MIME_TYPE && $mime_type == 'flexio.rename-file';
        $expected = true;
        TestCheck::assertBoolean('A.21', 'RenameFileJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(SearchJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == SearchJob::MIME_TYPE && $mime_type == 'flexio.search';
        $expected = true;
        TestCheck::assertBoolean('A.22', 'SearchJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(SelectColumnJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == SelectColumnJob::MIME_TYPE && $mime_type == 'flexio.select';
        $expected = true;
        TestCheck::assertBoolean('A.23', 'SelectColumnJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(SleepJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == SleepJob::MIME_TYPE && $mime_type == 'flexio.sleep';
        $expected = true;
        TestCheck::assertBoolean('A.24', 'SleepJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(SortJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == SortJob::MIME_TYPE && $mime_type == 'flexio.sort';
        $expected = true;
        TestCheck::assertBoolean('A.25', 'SortJob definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(TransformJob::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == TransformJob::MIME_TYPE && $mime_type == 'flexio.transform';
        $expected = true;
        TestCheck::assertBoolean('A.26', 'TransformJob definition type constant',  $actual, $expected, $results);
    }
}
