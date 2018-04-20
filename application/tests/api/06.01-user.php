<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
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
        // ENDPOINT: POST /signup


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';


        // TEST: create a new user

        // BEGIN TEST
        $username = \Flexio\Base\Identifier::generate();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/signup",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
                "username": "'.$username.'",
                "email": "'.$email.'",
                "password": "'.$password.'",
                "send_email": "true",
                "create_examples": "true"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "eid_type": "'.\Model::TYPE_USER.'",
            "username": "'.$username.'",
            "email": "'.$email.'"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /signup; create a new user',  $actual, $expected, $results);


        // TEST: make sure we can't create a duplicate user on either username or email

        // BEGIN TEST
        $username_original = \Flexio\Base\Identifier::generate();
        $email_original = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();

        for ($i = 1; $i <= 3; ++$i)
        {
            $username = $username_original;
            $email = $email_original;

            // on first loop, create the user; on second loop, check
            // for duplicate username (i.e., change the email); on third
            // loop, check for duplicate email (i.e., change the username)
            if ($i === 2)
            {
                $email = \Flexio\Tests\Util::createEmailAddress();
            }
            if ($i === 3)
                $username = \Flexio\Base\Identifier::generate();

            $params = array(
                'method' => 'POST',
                'url' => "$apibase/signup",
                // 'token' => '', // no token included
                'content_type' => 'application/json',
                'params' => '{
                    "username": "'.$username.'",
                    "email": "'.$email.'",
                    "password": "'.$password.'",
                    "send_email": "true",
                    "create_examples": "true"
                }'
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $actual = $result['response'];

            if ($i === 1)
            {
                $expected = '
                {
                    "eid_type": "'.\Model::TYPE_USER.'",
                    "username": "'.$username.'",
                    "email": "'.$email.'"
                }';
                \Flexio\Tests\Check::assertInArray('B.1', 'POST /signup; create a new user',  $actual, $expected, $results);
            }

            if ($i === 2 || $i === 3)
            {
                $expected = '
                {
                    "error" : {
                        "code": "create-failed"
                    }
                }';
                \Flexio\Tests\Check::assertInArray("B.$i", 'POST /signup; fail if username or email are already taken',  $actual, $expected, $results);
            }
        }
    }
}
