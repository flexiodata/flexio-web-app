<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-04
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
        // SETUP
        $data_single_col_single_row = [
            ["col1"]
        ];

        $data_single_col = [
            ["col1"],
            ["val1"],
            ["val4"],
            ["val7"]
        ];

        $data_single_row = [
            ["col1","col2","col3"]
        ];

        $data_basic = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];


        // TEST: column count

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_col_single_row);
        $actual = $table->getColCount();
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.1', '\Flexio\Base\Table::getColCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_col);
        $actual = $table->getColCount();
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.2', '\Flexio\Base\Table::getColCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_row);
        $actual = $table->getColCount();
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('A.3', '\Flexio\Base\Table::getColCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getColCount();
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('A.4', '\Flexio\Base\Table::getColCount()',  $actual, $expected, $results);


        // TEST: row count

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_col_single_row);
        $actual = $table->getRowCount();
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.1', '\Flexio\Base\Table::getRowCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_col);
        $actual = $table->getRowCount();
        $expected = 4;
        \Flexio\Tests\Check::assertNumber('A.2', '\Flexio\Base\Table::getRowCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_single_row);
        $actual = $table->getRowCount();
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.3', '\Flexio\Base\Table::getRowCount()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRowCount();
        $expected = 4;
        \Flexio\Tests\Check::assertNumber('A.4', '\Flexio\Base\Table::getRowCount()',  $actual, $expected, $results);


        // TEST: table get row

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRow(0);
        $expected = ["col1","col2","col3"];
        \Flexio\Tests\Check::assertNumber('B.1', '\Flexio\Base\Table::getRow()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRow(1);
        $expected = ["val1","val2","val3"];
        \Flexio\Tests\Check::assertNumber('B.2', '\Flexio\Base\Table::getRow()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRow(2);
        $expected = ["val4","val5","val6"];
        \Flexio\Tests\Check::assertNumber('B.3', '\Flexio\Base\Table::getRow()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRow(3);
        $expected = ["val7","val8","val9"];
        \Flexio\Tests\Check::assertNumber('B.4', '\Flexio\Base\Table::getRow()',  $actual, $expected, $results);


        // TEST: table get column

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getCol(0);
        $expected = ["col1","val1","val4","val7"];
        \Flexio\Tests\Check::assertNumber('C.1', '\Flexio\Base\Table::getCol()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getCol(1);
        $expected = ["col2","val2","val5","val8"];
        \Flexio\Tests\Check::assertNumber('C.2', '\Flexio\Base\Table::getCol()',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getCol(2);
        $expected = ["col3","val3","val6","val9"];
        \Flexio\Tests\Check::assertNumber('C.3', '\Flexio\Base\Table::getCol()',  $actual, $expected, $results);


        // TEST: table getRange

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.1', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(-1,-1,100,100);
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(100,100,-1,-1);
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.3', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(0,0,2,3);
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.4', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(2,3,0,0);
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.5', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(0,0,0,0);
        $expected = [
            ["col1"]
        ];
        \Flexio\Tests\Check::assertArray('D.6', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(null,null,1,1);
        $expected = [
            ["col1","col2"],
            ["val1","val2"],
        ];
        \Flexio\Tests\Check::assertArray('D.7', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(1,1,null,null);
        $expected = [
            ["val2","val3"],
            ["val5","val6"],
            ["val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('D.8', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(0,null,0,null);
        $expected = [
            ["col1"],
            ["val1"],
            ["val4"],
            ["val7"],
        ];
        \Flexio\Tests\Check::assertArray('D.9', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->getRange(null,0,null,0);
        $expected = [
            ["col1","col2","col3"],
        ];
        \Flexio\Tests\Check::assertArray('D.10', '\Flexio\Base\Table::getRange(); basic test',  $actual, $expected, $results);


        // TEST: table setRange

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange()->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.1', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(-1,-1,100,100)->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.2', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(100,100,-1,-1)->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.3', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(0,0,2,3)->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.4', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(2,3,0,0)->getRange();
        $expected = [
            ["col1","col2","col3"],
            ["val1","val2","val3"],
            ["val4","val5","val6"],
            ["val7","val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.5', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(0,0,0,0)->getRange();
        $expected = [
            ["col1"]
        ];
        \Flexio\Tests\Check::assertArray('E.6', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(null,null,1,1)->getRange();
        $expected = [
            ["col1","col2"],
            ["val1","val2"],
        ];
        \Flexio\Tests\Check::assertArray('E.7', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(1,1,null,null)->getRange();
        $expected = [
            ["val2","val3"],
            ["val5","val6"],
            ["val8","val9"],
        ];
        \Flexio\Tests\Check::assertArray('E.8', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(0,null,0,null)->getRange();
        $expected = [
            ["col1"],
            ["val1"],
            ["val4"],
            ["val7"],
        ];
        \Flexio\Tests\Check::assertArray('E.9', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);

        // BEGIN TEST
        $table = \Flexio\Base\Table::create($data_basic);
        $actual = $table->setRange(null,0,null,0)->getRange();
        $expected = [
            ["col1","col2","col3"],
        ];
        \Flexio\Tests\Check::assertArray('E.10', '\Flexio\Base\Table::setRange(); basic test',  $actual, $expected, $results);
    }
}
