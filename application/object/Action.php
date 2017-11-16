<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-17
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Action
{
    const TYPE_SIGNUP   = 'action.signup';


    public static function trackTest(string $user_eid, array $params)
    {
        // note: for testing use; does not contain production check
        self::track_internal($user_eid, $params);
    }

    public static function track(string $user_eid, string $event, array $params)
    {
        // note: for actual use in Api functions; contains production check
        if (!IS_PRODSITE())
            return;

        switch ($event)
        {
            default:
                self::track_internal($user_eid, $event, $params);
                break;

            case self::TYPE_SIGNUP:
                self::identify_internal($user_eid, $params);
                self::track_internal($user_eid, $event, $params);
                break;
        }
    }

    private static function identify_internal(string $user_eid, array $params)
    {
        $segment_key = $GLOBALS['g_config']->googledrive_client_id ?? false;
        if ($segment_key === false)
            return;

        $post_data = array(
            "userId" => $user_eid,
            "traits" => $params,
            "context" => array(),
            "timestamp" => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data);

        $basic_auth = 'Basic ' . base64_encode($segment_key . ':');
        $content_type = 'application/json';

        $headers = array();
        $headers['Authorization'] = $basic_auth;
        $headers['Content-Type'] = $content_type;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.segment.io/v1/identify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    private static function track_internal(string $user_eid, string $event, array $params)
    {
        $segment_key = $GLOBALS['g_config']->googledrive_client_id ?? false;
        if ($segment_key === false)
            return;

        $post_data = array(
            "userId" => $user_eid,
            "event" => $event,
            "properties" => $params,
            "context" => array(),
            "timestamp" => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data);

        $basic_auth = 'Basic ' . base64_encode($segment_key . ':');
        $content_type = 'application/json';

        $headers = array();
        $headers['Authorization'] = $basic_auth;
        $headers['Content-Type'] = $content_type;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.segment.io/v1/track');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
