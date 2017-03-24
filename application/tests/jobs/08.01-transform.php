<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-17
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: Transform job type constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_NONE;
        $expected = '';
        TestCheck::assertString('A.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_CHANGE_TYPE;
        $expected = 'type';
        TestCheck::assertString('A.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_CHANGE_CASE;
        $expected = 'case';
        TestCheck::assertString('A.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_SUBSTRING;
        $expected = 'substring';
        TestCheck::assertString('A.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_REMOVE_TEXT;
        $expected = 'remove';
        TestCheck::assertString('A.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_TRIM_TEXT;
        $expected = 'trim';
        TestCheck::assertString('A.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::OPERATION_PAD_TEXT;
        $expected = 'pad';
        TestCheck::assertString('A.7', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job case constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_NONE;
        $expected = '';
        TestCheck::assertString('B.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_LOWER;
        $expected = 'lower';
        TestCheck::assertString('B.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_UPPER;
        $expected = 'upper';
        TestCheck::assertString('B.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_PROPER;
        $expected = 'proper';
        TestCheck::assertString('B.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CAPITALIZE_FIRST_LETTER;
        $expected = 'first-letter';
        TestCheck::assertString('B.5', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job pad constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_NONE;
        $expected = '';
        TestCheck::assertString('C.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_LEFT;
        $expected = 'left';
        TestCheck::assertString('C.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::PAD_LOCATION_RIGHT;
        $expected = 'right';
        TestCheck::assertString('C.3', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job trim constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_NONE;
        $expected = '';
        TestCheck::assertString('D.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_LEADING;
        $expected = 'leading';
        TestCheck::assertString('D.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_TRAILING;
        $expected = 'trailing';
        TestCheck::assertString('D.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::TRIM_LOCATION_LEADING_TRAILING;
        $expected = 'leading-trailing';
        TestCheck::assertString('D.4', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job substring constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_NONE;
        $expected = '';
        TestCheck::assertString('E.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_LEFT;
        $expected = 'left';
        TestCheck::assertString('E.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_RIGHT;
        $expected = 'right';
        TestCheck::assertString('E.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_MID;
        $expected = 'mid';
        TestCheck::assertString('E.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::SUBSTRING_LOCATION_PART;
        $expected = 'part';
        TestCheck::assertString('E.5', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job remove constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_NONE;
        $expected = '';
        TestCheck::assertString('F.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_LEADING;
        $expected = 'leading';
        TestCheck::assertString('F.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_TRAILING;
        $expected = 'trailing';
        TestCheck::assertString('F.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_LEADING_TRAILING;
        $expected = 'leading-trailing';
        TestCheck::assertString('F.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_LOCATION_ANY;
        $expected = 'any';
        TestCheck::assertString('F.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_NONE;
        $expected = '';
        TestCheck::assertString('F.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SYMBOLS;
        $expected = 'symbols';
        TestCheck::assertString('F.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST8
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_WHITESPACE;
        $expected = 'whitespace';
        TestCheck::assertString('F.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_LETTERS;
        $expected = 'letters';
        TestCheck::assertString('F.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_NUMBERS;
        $expected = 'numbers';
        TestCheck::assertString('F.10', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_REGEX;
        $expected = 'regex';
        TestCheck::assertString('F.11', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SELECTED_TEXT;
        $expected = 'selected-text';
        TestCheck::assertString('F.12', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::REMOVE_TYPE_SELECTED_CHARS;
        $expected = 'selected-chars';
        TestCheck::assertString('F.13', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job column type constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_NONE;
        $expected = '';
        TestCheck::assertString('G.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_TEXT;
        $expected = 'text';
        TestCheck::assertString('G.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_CHARACTER;
        $expected = 'character';
        TestCheck::assertString('G.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_WIDECHARACTER;
        $expected = 'widecharacter';
        TestCheck::assertString('G.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_NUMERIC;
        $expected = 'numeric';
        TestCheck::assertString('G.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DOUBLE;
        $expected = 'double';
        TestCheck::assertString('G.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_INTEGER;
        $expected = 'integer';
        TestCheck::assertString('G.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DATE;
        $expected = 'date';
        TestCheck::assertString('G.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_DATETIME;
        $expected = 'datetime';
        TestCheck::assertString('G.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::COLUMN_TYPE_BOOLEAN;
        $expected = 'boolean';
        TestCheck::assertString('G.10', 'Transform job constant',  $actual, $expected, $results);



        // TEST: Transform job character class constants; these are used as parameters

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_NONE;
        $expected = '';
        TestCheck::assertString('H.1', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ALNUM;
        $expected = 'alnum';
        TestCheck::assertString('H.2', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ALPHA;
        $expected = 'alpha';
        TestCheck::assertString('H.3', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_ASCII;
        $expected = 'ascii';
        TestCheck::assertString('H.4', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_BLANK;
        $expected = 'blank';
        TestCheck::assertString('H.5', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_CNTRL;
        $expected = 'cntrl';
        TestCheck::assertString('H.6', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_DIGIT;
        $expected = 'digit';
        TestCheck::assertString('H.7', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_GRAPH;
        $expected = 'graph';
        TestCheck::assertString('H.8', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_LOWER;
        $expected = 'lower';
        TestCheck::assertString('H.9', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_PRINT;
        $expected = 'print';
        TestCheck::assertString('H.10', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_PUNCT;
        $expected = 'punct';
        TestCheck::assertString('H.11', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_SPACE;
        $expected = 'space';
        TestCheck::assertString('H.12', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_UPPER;
        $expected = 'upper';
        TestCheck::assertString('H.13', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_WORD;
        $expected = 'word';
        TestCheck::assertString('H.14', 'Transform job constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Jobs\Transform::CHARACTER_CLASS_XDIGIT;
        $expected = 'xdigit';
        TestCheck::assertString('H.15', 'Transform job constant',  $actual, $expected, $results);
    }
}
