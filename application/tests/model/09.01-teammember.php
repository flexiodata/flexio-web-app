<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-29
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
        // FUNCTION: \Flexio\Model\Team::list()


        // SETUP
        $user_model = \Flexio\Tests\Util::getModel()->user;
        $teammember_model = \Flexio\Tests\Util::getModel()->teammember;


        // TEST: a user should be a member of their own team

        // BEGIN TEST
        $info = array(
        );
        $eid = $user_model->create($info);
        $actual = $teammember_model->list(['owned_by' => $eid]);
        $expected = [[
            "member_eid" => $eid,
            "member_status" => \Model::TEAM_MEMBER_STATUS_ACTIVE,
            "role"  => \Model::TEAM_ROLE_OWNER,
            "owned_by" => $eid,
            "created_by" => $eid
        ]];
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Model\Team::list(); a user should be a member of their own team',  $actual, $expected, $results);
    }
}
