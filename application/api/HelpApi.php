<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-01-29
 *
 * @package flexio
 * @subpackage Api
 */


class HelpApi
{
    public static function createConversation($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'subject' => array('type' => 'string', 'required' => true),
                'message' => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $api_key = isset_or($GLOBALS['g_config']->helpscout_api_key, false);
        if ($api_key === false)
            return $request->getValidator()->fail();

        // TODO: experimental help function; here for simplicity, but should be split
        // out into a help controller

        // note: only allow requesting users to send requests for themselves
        $requesting_user_eid = $request->getRequestingUser();

        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $user_info = $user->get();
        if ($user_info === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // help requestion info
        $email = $user_info['email'];
        $first_name = $user_info['first_name'];
        $last_name = $user_info['last_name'];
        $subject = $params['subject'];
        $mailbox = '64691';  // mailbox desintation set up in Help Scout
        $message = $params['message'];
        $timestamp = Util::formatDate(System::getTimestamp());

        // help request conversation package
        $conversation = "
        {
            \"type\": \"email\",
            \"customer\": {
                \"email\": \"$email\",
                \"firstName\": \"$first_name\",
                \"lastName\": \"$last_name\",
                \"type\": \"user\"
            },
            \"subject\": \"$subject\",
            \"mailbox\": {
                \"id\": $mailbox
            },
            \"tags\": [
            ],
            \"status\": \"active\",
            \"createdAt\": \"$timestamp\",
            \"threads\": [
                {
                    \"type\": \"customer\",
                    \"createdBy\": {
                        \"email\": \"$email\",
                        \"type\": \"customer\"
                    },
                    \"body\": \"$message\",
                    \"status\": \"active\",
                    \"createdAt\": \"$timestamp\"
                }
            ]
        }
        ";

        $api_endpoint = "https://api.helpscout.net/v1/conversations.json";
        $basic_auth = "$api_key:X";
        $result = HttpRequest::exec('POST', $api_endpoint, $basic_auth, $conversation);

        // TODO: build up api result
        return true;
    }
}
