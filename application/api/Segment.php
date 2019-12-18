<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
    public const TYPE_TEST               = 'test';
    public const TYPE_SIGNED_UP          = 'signed-up';
    public const TYPE_SIGNED_IN          = 'signed-in';

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

    private static function track_request(string $action, string $user_eid, array $params) : void
    {
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
                    'firstName' => $params['first_name'] ?? '',
                    'lastName' => $params['last_name'] ?? '',
                    'email' => $params['email'] ?? '',
                    'username' => $params['username'] ?? '',
                    'createdAt' => $params['created'] ?? ''
                );

                self::identify_internal($user_eid, $traits);
                self::track_internal($action, $user_eid, $traits);
                break;
        }
    }

    private static function identify_internal(string $user_eid, array $params) : void
    {
        $segment_key = $GLOBALS['g_config']->segment_key?? false;
        if ($segment_key === false)
            return;

        // see here: https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#identify
        $post_data = array(
            'userId' => $user_eid,
            'traits' => $params,
            'context' => array(),
            'timestamp' => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

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

        // see here: https://segment.com/docs/connections/sources/catalog/libraries/server/http-api/#track
        $action_description = self::getActionDescription($action);
        $post_data = array(
            'userId' => $user_eid,
            'event' => $action_description,
            'properties' => $params,
            'context' => array(),
            'timestamp' => \Flexio\System\System::getTimestamp()
        );
        $post_data = json_encode($post_data, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

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
        }
    }
}
