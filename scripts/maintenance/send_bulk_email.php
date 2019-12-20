<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-12-20
 *
 * @package flexio
 * @subpackage Maintenance
 */


include_once __DIR__.'/../stub.php';


try
{
    $list = [
        ['email' => '', 'first_name' => ''],
        ['email' => '', 'first_name' => ''],
    ];

    echo ("Starting\n\n");
    foreach ($list as $item)
    {
        $success = \Flexio\Api\Message::sendTrialHalfwayEmail($item);
        usleep(50000); // sleep 50ms

        $email = $item['email'];
        $status = $success ? "Success" : "Fail";
        echo ("$status: $email\n");
    }
    echo ("\nFinished.\n\n");
}
catch (\Exception $e)
{
    echo('Aborting');
    exit(0);
}
