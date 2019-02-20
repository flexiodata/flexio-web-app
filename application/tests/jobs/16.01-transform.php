<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-17
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
        // TEST: Transform job type constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_CHANGE_TYPE;
        $expected = 'type';
        \Flexio\Tests\Check::assertString('A.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_CHANGE_CASE;
        $expected = 'case';
        \Flexio\Tests\Check::assertString('A.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_SUBSTRING;
        $expected = 'substring';
        \Flexio\Tests\Check::assertString('A.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_REMOVE_TEXT;
        $expected = 'remove';
        \Flexio\Tests\Check::assertString('A.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_TRIM_TEXT;
        $expected = 'trim';
        \Flexio\Tests\Check::assertString('A.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_PAD_TEXT;
        $expected = 'pad';
        \Flexio\Tests\Check::assertString('A.7', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job case constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_LOWER;
        $expected = 'lower';
        \Flexio\Tests\Check::assertString('B.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_UPPER;
        $expected = 'upper';
        \Flexio\Tests\Check::assertString('B.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_PROPER;
        $expected = 'proper';
        \Flexio\Tests\Check::assertString('B.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_FIRST_LETTER;
        $expected = 'first-letter';
        \Flexio\Tests\Check::assertString('B.5', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job pad constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_LEFT;
        $expected = 'left';
        \Flexio\Tests\Check::assertString('C.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_RIGHT;
        $expected = 'right';
        \Flexio\Tests\Check::assertString('C.3', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job trim constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('D.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_LEADING;
        $expected = 'leading';
        \Flexio\Tests\Check::assertString('D.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_TRAILING;
        $expected = 'trailing';
        \Flexio\Tests\Check::assertString('D.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_LEADING_TRAILING;
        $expected = 'leading-trailing';
        \Flexio\Tests\Check::assertString('D.4', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job substring constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('E.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_LEFT;
        $expected = 'left';
        \Flexio\Tests\Check::assertString('E.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_RIGHT;
        $expected = 'right';
        \Flexio\Tests\Check::assertString('E.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_MID;
        $expected = 'mid';
        \Flexio\Tests\Check::assertString('E.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_PART;
        $expected = 'part';
        \Flexio\Tests\Check::assertString('E.5', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job remove constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('F.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_LEADING;
        $expected = 'leading';
        \Flexio\Tests\Check::assertString('F.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_TRAILING;
        $expected = 'trailing';
        \Flexio\Tests\Check::assertString('F.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_LEADING_TRAILING;
        $expected = 'leading-trailing';
        \Flexio\Tests\Check::assertString('F.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_ANY;
        $expected = 'any';
        \Flexio\Tests\Check::assertString('F.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('F.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SYMBOLS;
        $expected = 'symbols';
        \Flexio\Tests\Check::assertString('F.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST8
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_WHITESPACE;
        $expected = 'whitespace';
        \Flexio\Tests\Check::assertString('F.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_LETTERS;
        $expected = 'letters';
        \Flexio\Tests\Check::assertString('F.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_NUMBERS;
        $expected = 'numbers';
        \Flexio\Tests\Check::assertString('F.10', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_REGEX;
        $expected = 'regex';
        \Flexio\Tests\Check::assertString('F.11', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SELECTED_TEXT;
        $expected = 'selected-text';
        \Flexio\Tests\Check::assertString('F.12', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SELECTED_CHARS;
        $expected = 'selected-chars';
        \Flexio\Tests\Check::assertString('F.13', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job column type constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('G.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_TEXT;
        $expected = 'text';
        \Flexio\Tests\Check::assertString('G.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_CHARACTER;
        $expected = 'character';
        \Flexio\Tests\Check::assertString('G.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        \Flexio\Tests\Check::assertString('G.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_NUMERIC;
        $expected = 'numeric';
        \Flexio\Tests\Check::assertString('G.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DOUBLE;
        $expected = 'double';
        \Flexio\Tests\Check::assertString('G.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_INTEGER;
        $expected = 'integer';
        \Flexio\Tests\Check::assertString('G.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DATE;
        $expected = 'date';
        \Flexio\Tests\Check::assertString('G.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DATETIME;
        $expected = 'datetime';
        \Flexio\Tests\Check::assertString('G.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_BOOLEAN;
        $expected = 'boolean';
        \Flexio\Tests\Check::assertString('G.10', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job character class constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_NONE;
        $expected = '';
        \Flexio\Tests\Check::assertString('H.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ALNUM;
        $expected = 'alnum';
        \Flexio\Tests\Check::assertString('H.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ALPHA;
        $expected = 'alpha';
        \Flexio\Tests\Check::assertString('H.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ASCII;
        $expected = 'ascii';
        \Flexio\Tests\Check::assertString('H.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_BLANK;
        $expected = 'blank';
        \Flexio\Tests\Check::assertString('H.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_CNTRL;
        $expected = 'cntrl';
        \Flexio\Tests\Check::assertString('H.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_DIGIT;
        $expected = 'digit';
        \Flexio\Tests\Check::assertString('H.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_GRAPH;
        $expected = 'graph';
        \Flexio\Tests\Check::assertString('H.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_LOWER;
        $expected = 'lower';
        \Flexio\Tests\Check::assertString('H.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_PRINT;
        $expected = 'print';
        \Flexio\Tests\Check::assertString('H.10', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_PUNCT;
        $expected = 'punct';
        \Flexio\Tests\Check::assertString('H.11', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_SPACE;
        $expected = 'space';
        \Flexio\Tests\Check::assertString('H.12', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_UPPER;
        $expected = 'upper';
        \Flexio\Tests\Check::assertString('H.13', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_WORD;
        $expected = 'word';
        \Flexio\Tests\Check::assertString('H.14', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_XDIGIT;
        $expected = 'xdigit';
        \Flexio\Tests\Check::assertString('H.15', 'Transform job constant',  $actual, $expected, $results);
    }
}
