<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2011-04-27
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class System
{
    public static function about(\Flexio\Api\Request $request) : void
    {
        // return basic information

        $package_info = \Flexio\System\System::getPackageInfo();
        $git_version = \Flexio\System\System::getGitRevision();

        $result = array();
        $result['name'] = $package_info['name'] ?? '';
        $result['version'] = $package_info['version'] ?? '';
        $result['sha'] = $git_version;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function session(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        // TODO: track?

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true), // allow string here to accomodate username/email
                'password' => array('type' => 'string', 'required' => true)  // allow string here to fall through to general error message below
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $username = $validated_post_params['username'];
        $password = $validated_post_params['password'];

        // default error message
        $error_message = _('Invalid username or password.');

        // return a token that can be used to login
        try
        {
            // try to get the user eid from username
            $current_user_eid = \Flexio\Object\User::getEidFromIdentifier($username);
            if ($current_user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
            {
                $error_message = _('Account not verified.  Please verify your account.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // verify the user with their credentials
            if ($current_user->checkPassword($password) === false)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
            }

            $user_info_basic = array();
            $user_info_basic['timestamp'] = \Flexio\Base\Util::getCurrentTimestamp();
            $user_info_basic['eid'] = $current_user_eid;
            $user_info_basic['random'] = \Flexio\Base\Util::generateRandomString(20);

            $user_info_basic = json_encode($user_info_basic);
            $token = \Flexio\Base\Util::encrypt($user_info_basic, $GLOBALS['g_store']->connection_enckey);

            // TODO: make sure encryption succeeds, or return an error

            $token = self::base64_url_encode($token); // make sure token is url safe since it'll be passwed as a query param
            $result = array(
                'token' => $token
            );

            // return the token
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function logintoken(\Flexio\Api\Request $request) : void
    {
        // function for logging in with a token; time-sensitive token is generated from session function

        $post_params = $request->getQueryParams();

        $request->track(\Flexio\Api\Action::TYPE_USER_LOGIN);
        // note: don't track request params since these contain credentials

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'token' => array('type' => 'string', 'required' => true) // token generated from session api call
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $token = $validated_post_params['token'];

        // attempt to decrypt the user info from the token
        $token = self::base64_url_decode($token);
        $user_info_basic = \Flexio\Base\Util::decrypt($token, $GLOBALS['g_store']->connection_enckey);
        if (!$user_info_basic)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $user_info_basic = json_decode($user_info_basic, true);

        if ($user_info_basic === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $current_user_eid = $user_info_basic['eid'] ?? false;
        if ($current_user_eid === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }

        $timestamp = $user_info_basic['timestamp'] ?? false;
        if ($timestamp === false)
        {
            $error_message = _('Invalid token.');
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
        }
        // TODO: check the timestamp to make sure it's within a minute of the previous token


        // default error message
        $error_message = _('Authentication failed.');

        try
        {
            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // set the user session
            \Flexio\System\System::createUserSession($current_user->getEid());

            // return "about" info
            $result = $current_user->get();
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function login(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $request->track(\Flexio\Api\Action::TYPE_USER_LOGIN);
        // note: don't track request params since these contain credentials

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true), // allow string here to accomodate username/email
                'password' => array('type' => 'string', 'required' => true)  // allow string here to fall through to general error message below
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $username = $validated_post_params['username'];
        $password = $validated_post_params['password'];

        // default error message
        $error_message = _('Invalid username or password.');

        try
        {
            // try to get the user eid from username
            $current_user_eid = \Flexio\Object\User::getEidFromIdentifier($username);
            if ($current_user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $current_user = \Flexio\Object\User::load($current_user_eid);

            // make sure user isn't deleted
            if ($current_user->getStatus() === \Model::STATUS_DELETED)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // make sure the user is available (e.g., not deleted and properly verified,
            // if verification is being used)
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
            {
                $error_message = _('Account not verified.  Please verify your account.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // verify the user with their credentials
            if ($current_user->checkPassword($password) === false)
            {
                $error_message = _('Invalid username or password.');
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED);
            }

            // set the user session
            \Flexio\System\System::createUserSession($current_user_eid);

            // return "about" info
            $result = $current_user->get();
            $request->setRequestingUser($current_user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);

            return;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // return an error
        sleep(1);
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, $error_message);
    }

    public static function logout(\Flexio\Api\Request $request) : void
    {
        $request->track(\Flexio\Api\Action::TYPE_USER_LOGOUT);

        \Flexio\System\System::destroyUserSession();

        // return empty "about" info
        $result = array();
        $result['eid'] = '';
        $result['eid_type'] = \Model::TYPE_USER;

        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    private static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/=', '._-');
    }

    private static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '._-', '+/='));
    }
}
