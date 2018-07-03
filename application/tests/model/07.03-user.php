<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: \Flexio\Model\User::create(); multiple unique user creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_user_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create($info);
            $created_eids[$eid] = 1;
            if (!\Flexio\Base\Eid::isValid($eid))
                $failed_user_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_user_creation == 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::create(); creating users should succeed and produce a unique eid for each new user',  $actual, $expected, $results);
    }
}
