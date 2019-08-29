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
        // FUNCTION: \Flexio\Model\Team::create()
        // FUNCTION: \Flexio\Model\Team::set()


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


        // TEST: basic team member addition

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $user_model->create($info);
        $eid2 = $user_model->create($info);
        $teammember_model->create(['member_eid' => $eid2, 'owned_by' => $eid1, 'created_by' => $eid1]);
        $actual = $teammember_model->list(['owned_by' => $eid1]);
        $expected = [
            [
                "member_eid" => $eid1,
                "member_status" => \Model::TEAM_MEMBER_STATUS_ACTIVE,
                "role"  => \Model::TEAM_ROLE_OWNER,
                "owned_by" => $eid1,
                "created_by" => $eid1
            ],
            [
                "member_eid" => $eid2,
                "member_status" => \Model::TEAM_MEMBER_STATUS_PENDING,
                "role"  => \Model::TEAM_ROLE_USER,
                "owned_by" => $eid1,
                "created_by" => $eid1
            ]
        ];
        \Flexio\Tests\Check::assertInArray('B.1', '\Flexio\Model\Team::create(); basic team member addition',  $actual, $expected, $results);


        // TEST: basic team member update

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $user_model->create($info);
        $eid2 = $user_model->create($info);
        $teammember_model->create(['member_eid' => $eid2, 'owned_by' => $eid1, 'created_by' => $eid1]);
        $teammember_model->set($eid2, $eid1, ['member_status' => \Model::TEAM_MEMBER_STATUS_ACTIVE, 'role' => \Model::TEAM_ROLE_CONTRIBUTOR]);
        $actual = $teammember_model->list(['owned_by' => $eid1]);
        $expected = [
            [
                "member_eid" => $eid1,
                "member_status" => \Model::TEAM_MEMBER_STATUS_ACTIVE,
                "role"  => \Model::TEAM_ROLE_OWNER,
                "owned_by" => $eid1,
                "created_by" => $eid1
            ],
            [
                "member_eid" => $eid2,
                "member_status" => \Model::TEAM_MEMBER_STATUS_ACTIVE,
                "role"  => \Model::TEAM_ROLE_CONTRIBUTOR,
                "owned_by" => $eid1,
                "created_by" => $eid1
            ]
        ];
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Model\Team::set(); basic team member update',  $actual, $expected, $results);
    }
}
