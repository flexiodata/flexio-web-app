<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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


        // TEST: tests for sending a content response

        // BEGIN TEST
        $content = array(
        );
        ob_start();
        \Flexio\Api\Response::sendContent($content);
        $result = ob_get_clean();
        $actual = json_decode($result,true);
        $expected = $content;
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Api\Response::sendContent(); basic content response',  $actual, $expected, $results);

        // BEGIN TEST
        $content = array(
            'success' => true
        );
        ob_start();
        \Flexio\Api\Response::sendContent($content);
        $result = ob_get_clean();
        $actual = json_decode($result,true);
        $expected = $content;
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Api\Response::sendContent(); basic content response',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $content = array(
            'eid' => $object->getEid(),
            'eid_type' => $object->getType(),
            'eid_status' => $object->getStatus()
        );
        ob_start();
        \Flexio\Api\Response::sendContent($content);
        $result = ob_get_clean();
        $actual = json_decode($result,true);
        $expected = $content;
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Api\Response::sendContent(); basic content response',  $actual, $expected, $results);
    }
}
