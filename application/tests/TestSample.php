<?php
/**
 *
 * Copyright (c) 2015-2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-01
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class TestSample
{
    public static function createProjectSample($user_count = 4, $project_count = 2, $pipe_count = 2)
    {
        for ($user = 1; $user <= $user_count; ++$user)
        {
            if (($user % 10) == 0)
                echo "<br>$user";

            $user_name = "user$user";
            $email = "user$user@flex.io";
            $password = 'test99';

            if (\Flexio\Base\Eid::isValid(TestUtil::getModel()->user->getEidFromIdentifier($user_name)))
            {
                // this user already exists
                continue;
            }

            $user_eid = TestUtil::createUser($user_name, $email, $password);

            for ($project = 1; $project <= $project_count; ++$project)
            {
                $project_name = "Project$project";
                $project_eid = TestUtil::createProject($user_eid, $project_name);

                for ($pipe = 1; $pipe <= $pipe_count; ++$pipe)
                {
                    $pipe_name = "Pipe$pipe";
                    $pipe_eid = TestUtil::createPipe($user_eid, $project_eid, $pipe_name);
                }
            }
        }

        return true;
    }
}
