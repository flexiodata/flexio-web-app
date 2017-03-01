<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-30
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: make sure there are no errors in the job schemas

        // BEGIN TEST
        $schema = \Flexio\Jobs\CalcField::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Jobs\CalcField schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Convert::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Jobs\Convert schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Copy::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\Jobs\Copy schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Create::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\Jobs\Create schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Distinct::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\Jobs\Distinct schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Duplicate::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\Jobs\Duplicate schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\EmailSend::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Flexio\Jobs\EmailSend schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Filter::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\Jobs\Filter schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\FindReplace::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\Jobs\FindReplace schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Grep::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Flexio\Jobs\Grep schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Group::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Flexio\Jobs\Group schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Input::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Flexio\Jobs\Input schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Limit::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Flexio\Jobs\Limit schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Merge::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.15', '\Flexio\Jobs\Merge schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Nop::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.16', '\Flexio\Jobs\Nop schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Output::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.17', '\Flexio\Jobs\Output schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Execute::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.18', '\Flexio\Jobs\Execute schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Prompt::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.19', '\Flexio\Jobs\Prompt schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\RenameColumnJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.20', '\Flexio\Jobs\RenameColumnJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\RenameFileJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.21', '\Flexio\Jobs\RenameFileJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\SearchJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.22', '\Flexio\Jobs\SearchJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\SelectColumnJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.23', '\Flexio\Jobs\SelectColumnJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\SleepJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.24', '\Flexio\Jobs\SleepJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\SortJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.25', '\Flexio\Jobs\SortJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\TransformJob::SCHEMA;
        $actual = \Flexio\System\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.26', '\Flexio\Jobs\TransformJob schema format',  $actual, $expected, $results);
    }
}
