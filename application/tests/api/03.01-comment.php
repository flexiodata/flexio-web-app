<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-27
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $parent = \Flexio\Object\Object::create();
        $parent_eid = $parent->getEid();

        $params = json_decode('
        {
            "parent_eid": "'.$parent_eid.'",
            "comment": "Test comment"
        }
        ',true);
        $request = Request::create()->setRequestingUser(\Flexio\Object\User::USER_SYSTEM);
        $actual = \Flexio\Api\CommentApi::create($params, $request);

        $expected = '
        {
            "eid_type": "'.Model::TYPE_COMMENT.'",
            "comment": "Test comment"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\CommentApi::create(); return the object',  $actual, $expected, $results);
    }
}
