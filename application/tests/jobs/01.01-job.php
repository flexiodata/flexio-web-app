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


declare(strict_types=1);
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
        $definition = json_decode(\Flexio\Jobs\Create::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Create::MIME_TYPE && $mime_type == 'flexio.create';
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Flexio\Jobs\Create definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Distinct::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Distinct::MIME_TYPE && $mime_type == 'flexio.distinct';
        $expected = true;
        TestCheck::assertBoolean('A.6', '\Flexio\Jobs\Distinct definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Duplicate::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Duplicate::MIME_TYPE && $mime_type == 'flexio.duplicate';
        $expected = true;
        TestCheck::assertBoolean('A.7', '\Flexio\Jobs\Duplicate definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\EmailSend::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\EmailSend::MIME_TYPE && $mime_type == 'flexio.email';
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Flexio\Jobs\EmailSend definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Filter::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Filter::MIME_TYPE && $mime_type == 'flexio.filter';
        $expected = true;
        TestCheck::assertBoolean('A.9', '\Flexio\Jobs\Filter definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Replace::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Replace::MIME_TYPE && $mime_type == 'flexio.replace';
        $expected = true;
        TestCheck::assertBoolean('A.10', '\Flexio\Jobs\Replace definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Grep::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Grep::MIME_TYPE && $mime_type == 'flexio.grep';
        $expected = true;
        TestCheck::assertBoolean('A.11', '\Flexio\Jobs\Grep definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Group::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Group::MIME_TYPE && $mime_type == 'flexio.group';
        $expected = true;
        TestCheck::assertBoolean('A.12', '\Flexio\Jobs\Group definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Input::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Input::MIME_TYPE && $mime_type == 'flexio.input';
        $expected = true;
        TestCheck::assertBoolean('A.13', '\Flexio\Jobs\Input definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Limit::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Limit::MIME_TYPE && $mime_type == 'flexio.limit';
        $expected = true;
        TestCheck::assertBoolean('A.14', '\Flexio\Jobs\Limit definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Merge::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Merge::MIME_TYPE && $mime_type == 'flexio.merge';
        $expected = true;
        TestCheck::assertBoolean('A.15', '\Flexio\Jobs\Merge definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Nop::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Nop::MIME_TYPE && $mime_type == 'flexio.nop';
        $expected = true;
        TestCheck::assertBoolean('A.16', '\Flexio\Jobs\Nop definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Output::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Output::MIME_TYPE && $mime_type == 'flexio.output';
        $expected = true;
        TestCheck::assertBoolean('A.17', '\Flexio\Jobs\Output definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Execute::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Execute::MIME_TYPE && $mime_type == 'flexio.execute';
        $expected = true;
        TestCheck::assertBoolean('A.18', '\Flexio\Jobs\Execute definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Prompt::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Prompt::MIME_TYPE && $mime_type == 'flexio.prompt';
        $expected = true;
        TestCheck::assertBoolean('A.19', '\Flexio\Jobs\Prompt definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\RenameColumn::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\RenameColumn::MIME_TYPE && $mime_type == 'flexio.rename';
        $expected = true;
        TestCheck::assertBoolean('A.20', '\Flexio\Jobs\RenameColumn definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\RenameFile::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\RenameFile::MIME_TYPE && $mime_type == 'flexio.rename-file';
        $expected = true;
        TestCheck::assertBoolean('A.21', '\Flexio\Jobs\RenameFile definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Search::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Search::MIME_TYPE && $mime_type == 'flexio.search';
        $expected = true;
        TestCheck::assertBoolean('A.22', '\Flexio\Jobs\Search definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Select::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Select::MIME_TYPE && $mime_type == 'flexio.select';
        $expected = true;
        TestCheck::assertBoolean('A.23', '\Flexio\Jobs\Select definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Sleep::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Sleep::MIME_TYPE && $mime_type == 'flexio.sleep';
        $expected = true;
        TestCheck::assertBoolean('A.24', '\Flexio\Jobs\Sleep definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Sort::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Sort::MIME_TYPE && $mime_type == 'flexio.sort';
        $expected = true;
        TestCheck::assertBoolean('A.25', '\Flexio\Jobs\Sort definition type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $definition = json_decode(\Flexio\Jobs\Transform::TEMPLATE,true);
        $mime_type = $definition['type'];
        $actual = $mime_type == \Flexio\Jobs\Transform::MIME_TYPE && $mime_type == 'flexio.transform';
        $expected = true;
        TestCheck::assertBoolean('A.26', '\Flexio\Jobs\Transform definition type constant',  $actual, $expected, $results);
    }
}
