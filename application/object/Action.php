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
    // TODO: update list of actions; these are some of the actions currently
    // tracked through the UI (using the action description names for the event)
    const TYPE_UNDEFINED          = '';
    const TYPE_TEST               = 'action.test';
    const TYPE_SIGNED_UP          = 'action.signed-up';
    const TYPE_SIGNED_IN          = 'action.signed-in';
    const TYPE_SIGNED_OUT         = 'action.signed-out';
    const TYPE_PASSWORD_CHANGED   = 'action.password.changed';
    const TYPE_APIKEY_CREATED     = 'action.apikey.created';
    const TYPE_PROJECT_CREATED    = 'action.project.created';
    const TYPE_CONNECTION_CREATED = 'action.connection.created';
    const TYPE_PIPE_CREATED       = 'action.pipe.created';
    const TYPE_PIPE_SCHEDULED     = 'action.pipe.scheduled';
    const TYPE_PIPE_RUN           = 'action.pipe.run';
    const TYPE_PROCESS_CREATED    = 'action.process.created';

    public static function trackTest(string $user_eid, array $params)
    {
        // only allow test tracking on non-production systems
        if (IS_PRODSITE())
            return;

        $action = $params['action'] ?? false;
        if ($action === false)
            return;
        unset($params['action']);

        // note: for testing use; does not contain production check
        self::track_request($action, $user_eid, $params);
    }

    public static function track(string $action, string $user_eid, array $params)
    {
        // only allow actual tracking on production
        if (!IS_PRODSITE())
            return;

        self::track_request($action, $user_eid, $params);
    }

    public static function track_request(string $action, string $user_eid, array $params)
    {
        // only track valid actions
        if (self::isValidAction($action) === false)
            return false;

        switch ($action)
        {
            default:
                self::track_internal($action, $user_eid, $params);
                break;

            case self::TYPE_SIGNED_UP:
                self::identify_internal($user_eid, $params);
                self::track_internal($action, $user_eid, $params);
                break;
        }
    }

    private static function identify_internal(string $user_eid, array $params)
    {
        $segment_key = $GLOBALS['g_config']->segment_key?? false;
        if ($segment_key === false)
            return;

        $post_data = array(
            "userId" => $user_eid,
            "traits" => $params,
            "context" => array(),
            "timestamp" => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data, JSON_FORCE_OBJECT);

        $headers = array();
        $headers[] = 'Authorization: Basic ' . base64_encode($segment_key . ':');
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.segment.io/v1/identify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    private static function track_internal(string $action, string $user_eid, array $params)
    {
        $segment_key = $GLOBALS['g_config']->segment_key?? false;
        if ($segment_key === false)
            return;

        $action_description = self::getActionDescription($action);
        $post_data = array(
            "userId" => $user_eid,
            "event" => $action_description,
            "properties" => $params,
            "context" => array(),
            "timestamp" => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data, JSON_FORCE_OBJECT);

        $headers = array();
        $headers[] = 'Authorization: Basic ' . base64_encode($segment_key . ':');
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.segment.io/v1/track');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    private static function isValidAction(string $action) : bool
    {
        switch ($action)
        {
            default:
                return false;

            case self::TYPE_TEST:
            case self::TYPE_SIGNED_UP:
            case self::TYPE_SIGNED_IN:
            case self::TYPE_SIGNED_OUT:
            case self::TYPE_PASSWORD_CHANGED:
            case self::TYPE_APIKEY_CREATED:
            case self::TYPE_PROJECT_CREATED:
            case self::TYPE_CONNECTION_CREATED:
            case self::TYPE_PIPE_CREATED:
            case self::TYPE_PIPE_SCHEDULED:
            case self::TYPE_PIPE_RUN:
            case self::TYPE_PROCESS_CREATED:
                return true;
        }
    }

    private static function getActionDescription(string $action) : string
    {
        switch ($action)
        {
            default:
                return self::TYPE_UNDEFINED;

            case self::TYPE_TEST:               return 'Test';
            case self::TYPE_SIGNED_UP:          return 'Signed Up';
            case self::TYPE_SIGNED_IN:          return 'Signed In';
            case self::TYPE_SIGNED_OUT:         return 'Signed Out';
            case self::TYPE_PASSWORD_CHANGED:   return 'Changed Password';
            case self::TYPE_APIKEY_CREATED:     return 'Created API Key';
            case self::TYPE_PROJECT_CREATED:    return 'Created Project';
            case self::TYPE_CONNECTION_CREATED: return 'Created Connection';
            case self::TYPE_PIPE_CREATED:       return 'Created Pipe';
            case self::TYPE_PIPE_SCHEDULED:     return 'Scheduled Pipe';
            case self::TYPE_PIPE_RUN:           return 'Ran Pipe';
            case self::TYPE_PROCESS_CREATED:    return 'Created Process';
        }
    }
}
