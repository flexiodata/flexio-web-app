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
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Segment
{
    // TODO: update list of actions; these are some of the actions currently
    // tracked through the UI (using the action description names for the event)
    public const TYPE_UNDEFINED          = '';
    public const TYPE_TEST               = 'action.test';
    public const TYPE_SIGNED_UP          = 'action.signed-up';
    public const TYPE_SIGNED_IN          = 'action.signed-in';
    public const TYPE_SIGNED_OUT         = 'action.signed-out';
    public const TYPE_PASSWORD_CHANGED   = 'action.password.changed';
    public const TYPE_APIKEY_CREATED     = 'action.apikey.created';
    public const TYPE_CONNECTION_CREATED = 'action.connection.created';
    public const TYPE_PIPE_CREATED       = 'action.pipe.created';
    public const TYPE_PIPE_SCHEDULED     = 'action.pipe.scheduled';
    public const TYPE_PIPE_RUN           = 'action.pipe.run';
    public const TYPE_PROCESS_CREATED    = 'action.process.created';

    public static function trackTest(\Flexio\Api\Request $request) : void
    {
        // only allow test tracking on non-production systems
        if (IS_PRODSITE())
            return;

        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // maek sure we have an 'action' query parameter
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'action' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $action = $query_params['action'];
        self::track_request($action, $requesting_user_eid, $query_params);
        return;
    }

    public static function track(string $action, string $user_eid, array $params) : void
    {
        // only allow actual tracking on production
        if (!IS_PRODSITE())
            return;

        self::track_request($action, $user_eid, $params);
    }

    public static function track_request(string $action, string $user_eid, array $params) : void
    {
        // TODO: once we add back server-side analytics, we'll remove
        //       this and begin bringing things back online
        return;

        // only track valid actions
        if (self::isValidAction($action) === false)
            return;

        switch ($action)
        {
            default:
                self::track_internal($action, $user_eid, []);
                break;

            case self::TYPE_SIGNED_IN:
                self::identify_internal($user_eid, []);
                self::track_internal($action, $user_eid, []);
                break;

            case self::TYPE_SIGNED_UP:
                $traits = array(
                    "firstName" => $params['first_name'] ?? '',
                    "lastName" => $params['last_name'] ?? '',
                    "email" => $params['email'] ?? '',
                    "username" => $params['username'] ?? '',
                    "createdAt" => $params['created'] ?? ''
                );

                self::identify_internal($user_eid, $traits);
                self::track_internal($action, $user_eid, $traits);
                break;
        }
    }

    private static function identify_internal(string $user_eid, array $params) : void
    {
        // TODO: once we add back server-side analytics, we'll remove
        //       this and begin bringing things back online
        return;

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

    private static function track_internal(string $action, string $user_eid, array $params) : void
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
            case self::TYPE_CONNECTION_CREATED: return 'Created Connection';
            case self::TYPE_PIPE_CREATED:       return 'Created Pipe';
            case self::TYPE_PIPE_SCHEDULED:     return 'Scheduled Pipe';
            case self::TYPE_PIPE_RUN:           return 'Ran Pipe';
            case self::TYPE_PROCESS_CREATED:    return 'Created Process';
        }
    }
}
