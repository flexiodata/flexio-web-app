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
        $team_model = \Flexio\Tests\Util::getModel()->team;


        // TEST: a user's list of teams should include the default team for that user

        // BEGIN TEST
        $info = array(
        );
        $eid = $user_model->create($info);
        $actual = $team_model->list(['owned_by' => $eid]);
        $expected = [[
            'member_status' => \Model::TEAM_MEMBER_STATUS_ACTIVE,
            'owned_by' => $eid,
            'created_by' => $eid
        ]];
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Model\Team::list(); a user\'s list of teams should include the default team for that user',  $actual, $expected, $results);
    }
}
