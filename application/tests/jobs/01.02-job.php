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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: make sure there are no errors in the job schemas

        // BEGIN TEST
        $schema = \Flexio\Jobs\CalcField::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\Jobs\CalcField schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Convert::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\Jobs\Convert schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Create::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\Jobs\Create schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Distinct::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\Jobs\Distinct schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Duplicate::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\Jobs\Duplicate schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\EmailSend::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Flexio\Jobs\EmailSend schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Filter::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\Jobs\Filter schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Replace::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\Jobs\Replace schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Grep::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Flexio\Jobs\Grep schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Group::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Flexio\Jobs\Group schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Input::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Flexio\Jobs\Input schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Limit::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Flexio\Jobs\Limit schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Merge::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.15', '\Flexio\Jobs\Merge schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Nop::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.16', '\Flexio\Jobs\Nop schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Output::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.17', '\Flexio\Jobs\Output schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Execute::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.18', '\Flexio\Jobs\Execute schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Prompt::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.19', '\Flexio\Jobs\Prompt schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\RenameColumn::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.20', '\Flexio\Jobs\RenameColumn schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\RenameFile::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.21', '\Flexio\Jobs\RenameFile schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Search::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.22', '\Flexio\Jobs\Search schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Select::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.23', '\Flexio\Jobs\Select schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Sleep::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.24', '\Flexio\Jobs\Sleep schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Sort::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.25', '\Flexio\Jobs\Sort schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = \Flexio\Jobs\Transform::SCHEMA;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.26', '\Flexio\Jobs\Transform schema format',  $actual, $expected, $results);
    }
}
