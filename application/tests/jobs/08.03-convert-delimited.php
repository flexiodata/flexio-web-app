<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-23
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function createConvertTask(string $delimiter, string $qualifier, bool $header)
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "convert",
                "input" => [
                    "format" => "delimited",
                    "delimiter" => "$delimiter",
                    "qualifier" => "$qualifier",
                    "header" => $header,
                ],
                "output" => [
                    "format" => "table"
                ]
            ]
        ]);

        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert Delimited; basic content conversion

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.02-content-ascii.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","a"],["2","b"],["3","A"],["4","B"],["5",""],["6","  abcd"],["7","abcd"],["8","abCD"],["9","ABcd"],["10","ab cd"],["11","ab CD"],["12","AB cd"]];
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.03-content-unicode.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1",""],["2","á é í ó ú ý Á É Í Ó Ú Ý"],["3","à è ì ò ù À È Ì Ò Ù"],["4","ä ë ï ö ü ÿ Ä Ë Ï Ö Ü Ÿ"],["5","â ê î ô û ð Â Ê Î Ô Û Ð"],["6","ã ñ õ Ã Ñ Õ"],["7","æ œ Æ Œ ß"],["8","å Å"],["9","ç Ç"],["10","ø Ø"],["11","¿ ¡"],["12","€ £ § º ©"]];
        \Flexio\Tests\Check::assertArray('A.2', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.04-content-integer.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","0"],["2","-1"],["3","1"],["4","9"],["5","10"],["6","-100000000"],["7","100000000"],["8","-100000001"],["9","100000001"],["10",""],["11","-999999999"],["12","999999999"]];
        \Flexio\Tests\Check::assertArray('A.3', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.05-content-decimal.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","0"],["2","-0.000000000001"],["3","0.000000000001"],["4","-0.4"],["5","0.4"],["6","-0.5"],["7","0.5"],["8","-0.9"],["9","0.9"],["10",""],["11","-9999999999999.9"],["12","9999999999999.9"]];
        \Flexio\Tests\Check::assertArray('A.4', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.06-content-number.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","123,456"],["2","  1.2345678"],["3","1,2345678"],["4","+123,456.78"],["5","-123.456,78"],["6","99999999999999999999"],["7","0.00000000000000000001"],["8","1/2"],["9","1/0"],["10","NaN"],["11","123."],["12","- 123"]];
        \Flexio\Tests\Check::assertArray('A.5', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.07-content-scientific.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","0e10"],["2","+10E-1"],["3","1.23e+2"],["4","1,23e02"],["5","  1.23 E0"],["6","-.23e+1"],["7","123,456.78E-5"],["8","1.e02"],["9","1E"],["10","E1"],["11","1e-99"],["12","1e99"]];
        \Flexio\Tests\Check::assertArray('A.6', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.08-content-amount.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","0%"],["2","  1.2345%"],["3",".1%"],["4","-98.6%"],["5","100,01 %"],["6","( 49% )"],["7","+7%"],["8",""],["9","NET WT. 20 OZ."],["10","1 LB. 4 OZ."],["11","(1 LB 4 OZ)"],["12","567g"]];
        \Flexio\Tests\Check::assertArray('A.7', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.09-content-financial.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","$1"],["2","  $10"],["3","-$0.01"],["4","$ 1,234.56"],["5","$1,234,567.89"],["6","($1234567.89)"],["7","€1.234,56"],["8","€ 1.234.567,89 EUR"],["9","EUR 1.234.567,89"],["10","£ 1.234,56"],["11","£1.234.567,89"],["12","GBP 1.234.567,89"]];
        \Flexio\Tests\Check::assertArray('A.8', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.10-content-identifier.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","123456"],["2","000123456"],["3","123456000"],["4","001234560"],["5","000-123-456"],["6","000 123 456"],["7","000.123.456"],["8","(123) 456-7890"],["9","a123b456C"],["10","A01230B4560C"],["11","#98"],["12",""]];
        \Flexio\Tests\Check::assertArray('A.9', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.11-content-null.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","null"],["2","NULL"],["3","Null"],["4",null],["5","\\\\N"],["6","\\\\\\\\N"],["7","\\n"],["8","\\\\n"],["9","\\\\\\\\n"],["10","\\0"],["11","\\\\0"],["12","\\\\\\\\0"]];
        \Flexio\Tests\Check::assertArray('A.10', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.12-content-escape.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","\\"],["2","\\\\"],["3","\\\\\\"],["4","\\\\\\\\"],["5","\\n"],["6","\\r"],["7","\\r\\n"],["8","\\t"],["9","\\/"],["10","\\\\/"],["11","\\'"],["12","\\\""]];
        \Flexio\Tests\Check::assertArray('A.11', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.13-content-symbol.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","?"],["2","!"],["3","@"],["4","#"],["5","$"],["6","%"],["7","^"],["8","&"],["9","*"],["10","-"],["11","="],["12","+"]];
        \Flexio\Tests\Check::assertArray('A.12', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.14-content-separator.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1",";"],["2",","],["3","."],["4","|"],["5",":"],["6","~"],["7","_"],["8","\" \""],["9","\"  \""],["10","\\t"],["11","\\f"],["12",""]];
        \Flexio\Tests\Check::assertArray('A.13', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.15-content-bracket.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","("],["2",")"],["3","["],["4","]"],["5","{"],["6","}"],["7","<"],["8",">"],["9","'"],["10","\""],["11","`"],["12","/"]];
        \Flexio\Tests\Check::assertArray('A.14', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.16-content-code.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1",'";break;$b="'],["2",'\';$a=1/0;$b=\''],["3","true;"],["4","throw new Exception('Problem.');"],["5",'" or "a" != "'],["6","' or 'a' != '"],["7","//  /*"],["8",".*"],["9","/.*/"],["10","' || true || '"],["11","' || true;"],["12","''; while (1) {}"]];
        \Flexio\Tests\Check::assertArray('A.15', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.17-content-date.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","0000-00-00"],["2","1582-10-05"],["3","1582-10-15"],["4","1969-12-31"],["5","1970-01-01"],["6",""],["7","1999-12-31"],["8","2000-01-01"],["9","2001-02-03"],["10","2016-02-29"],["11","2038-01-19"],["12","2038-01-20"]];
        \Flexio\Tests\Check::assertArray('A.16', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.18-content-date-format.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","20011225"],["2","  2001-12-25"],["3","12-25-2001"],["4","December 25, 2001"],["5","Dec 25, 2001"],["6","25 December 2001"],["7","25 Dec 2001"],["8","12.25.01"],["9","11/12/99"],["10","12/11/99"],["11","12/13/1999"],["12","13/12/1999"]];
        \Flexio\Tests\Check::assertArray('A.17', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.19-content-datetime.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","1969-12-31 23:59:59"],["2","1970-01-01 00:00:00"],["3","1970-01-01 00:00:01"],["4",""],["5","1999-12-31 23:59:59"],["6","2000-01-01 00:00:00"],["7","2000-01-01 00:00:01"],["8","2001-02-03 04:05:06"],["9","2016-02-29 00:00:00"],["10","2038-01-19 03:14:06"],["11","2038-01-19 03:14:07"],["12","2038-01-19 03:14:08"]];
        \Flexio\Tests\Check::assertArray('A.18', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.20-content-datetime-format.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","24 Dec 2001 23:59:60Z"],["2","25 Dec 2001 00:00:00Z"],["3","Dec 31, 1999 10:01:02"],["4","December 31, 1999 10:01:02 PM"],["5","Friday, December 31, 1999"],["6","Fri, 31 Dec 1999 23:59:59 +0000"],["7","25 December 2001 01:02:03"],["8","25 Dec 2001 04:05:06Z"],["9","2001-12-25T16:56:42+00:00"],["10","20011225T12:00:00"],["11","  2001-12-25 01:02:03"],["12","2016-02-29  01:02:03.4  +0005"]];
        \Flexio\Tests\Check::assertArray('A.19', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.21-content-bool.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","true"],["2","true"],["3",""],["4","false"],["5","false"],["6","false"],["7","true"],["8","true"],["9","true"],["10","true"],["11","false"],["12","false"]];
        \Flexio\Tests\Check::assertArray('A.20', 'Convert Delimited; content conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $task = self::createConvertTask("\t", "", false);
        $stream = \Flexio\Tests\Util::createStream('/text/03.22-content-bool-format.tsv');
        $process = \Flexio\Jobs\Process::create()->setStdin($stream)->execute($task);
        $actual = \Flexio\Tests\Content::getRows($process->getStdout());
        $expected = [["1","True"],["2","False"],["3","TRUE"],["4","FALSE"],["5","t"],["6","f"],["7","T"],["8","F"],["9","1"],["10","0"],["11","Y"],["12","N"]];
        \Flexio\Tests\Check::assertArray('A.21', 'Convert Delimited; content conversion',  $actual, $expected, $results);
    }
}
