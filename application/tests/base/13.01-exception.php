<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
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
        // TEST: basic exception test

        // BEGIN TEST
        $expected = false;
        try
        {
            $code = 'a';
            $message = 'b';
            throw new \Flexio\Base\Exception($code, $message);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $expected = $e->getMessage();
        }
        $actual = array('code' => $code, 'message' => $message);
        \Flexio\Tests\Check::assertArray('A.19', '\Flexio\Base\Error; constant check', $actual, $expected, $results);
    }
}
