<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-01-24
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
        // TEST: SDK template tests

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .foreach(Flexio.pipe().set('result', '${result}${input.name}'))
    .echo("${result}")
.run({data:[{"name":"111"},{"name":"222"},{"name":"333"}]}, function(err, response) {
	console.log(response.text)
})
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111222333\n";
        \Flexio\Tests\Check::assertString('A.1', 'SDK; for loop receiving information from post stdin json',  $actual, $expected, $results);


        // TEST: SDK template tests

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .echo([111,333,555])
    .foreach(Flexio.pipe().dump('${item}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111333555\n";
        \Flexio\Tests\Check::assertString('A.2', 'SDK; for loop iterating over json array with default iterator name "item"',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .echo([111,333,555])
    .foreach('moo : input', Flexio.pipe().dump('${moo}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111333555\n";
        \Flexio\Tests\Check::assertString('A.3', 'SDK; for loop iterating over json array with custom iterator name "moo"',  $actual, $expected, $results);


        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .echo("col1,col2,col3\nRow1Col1,Row1Col2,Row1Col3\nRow2Col1,Row2Col2,Row2Col3\nRow3Col1,Row3Col2,Row3Col3\n")
    .convert('csv','table')
    .foreach('moo : input', Flexio.pipe().dump('${moo.col2}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "Row1Col2Row2Col2Row3Col2\n";
        \Flexio\Tests\Check::assertString('A.4', 'SDK; for loop iterating over a table with custom iterator name "moo"',  $actual, $expected, $results);


        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .set('my_array', [111,333,555])
    .foreach('moo : my_array', Flexio.pipe().dump('${moo}'))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "111333555\n";
        \Flexio\Tests\Check::assertString('A.5', 'SDK; for loop iterating over non-input json array with custom iterator name "moo"',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .set('my_multidimensional_array', [
        [ 'aaa', 'bbb', 'ccc' ],
        [ 'ddd', 'eee', 'fff' ],
        [ 'ggg', 'hhh', 'iii' ]
    ])
    .foreach('row : my_multidimensional_array',
        Flexio.pipe().foreach('value : row',
            Flexio.pipe().dump('${value}')))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "aaabbbcccdddeeefffggghhhiii\n";
        \Flexio\Tests\Check::assertString('A.6', 'SDK; for loop iterating over multi-dimensional array',  $actual, $expected, $results);

        // BEGIN TEST
        $script = \Flexio\Tests\Util::getTestSDKSetup() . <<<'EOD'
Flexio.pipe()
    .set('my_multidimensional_array', [
        { data: [ 'aaa', 'bbb', 'ccc' ] },
        { data: [ 'ddd', 'eee', 'fff' ] },
        { data: [ 'ggg', 'hhh', 'iii' ] }
    ])
    .foreach('row : my_multidimensional_array',
        Flexio.pipe().foreach('value : row.data',
            Flexio.pipe().dump('${value}')))
.run().then(response => console.log(response.text))
EOD;
        $actual = \Flexio\Tests\Util::execSDKJS($script);
        $expected = "aaabbbcccdddeeefffggghhhiii\n";
        \Flexio\Tests\Check::assertString('A.7', 'SDK; for loop iterating over multi-dimensional array of objects',  $actual, $expected, $results);

    }
}

