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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $user_eid = TestUtil::getDefaultTestUser();
        $parent = \Flexio\Object\Object::create();
        $parent->setOwner($user_eid);
        $parent_eid = $parent->getEid();

        $params = json_decode('
        {
            "parent_eid": "'.$parent_eid.'",
            "comment": "Test comment"
        }
        ',true);
        $request = \Flexio\Api\Request::create()->setRequestingUser($user_eid);
        $actual = \Flexio\Api\Comment::create($params, $request);

        $expected = '
        {
            "eid_type": "'.\Model::TYPE_COMMENT.'",
            "comment": "Test comment"
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Api\Comment::create(); return the object',  $actual, $expected, $results);
    }
}
