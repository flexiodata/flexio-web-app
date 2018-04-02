<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams, David Z. Williams
 * Created:  2011-05-12
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api2;


class User
{
    public static function create(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'user_name'             => array('type' => 'string',  'required' => true),
                'email'                 => array('type' => 'string',  'required' => true),
                'password'              => array('type' => 'string',  'required' => true),
                'full_name'             => array('type' => 'string',  'required' => false, 'default' => ''),
                'first_name'            => array('type' => 'string',  'required' => false, 'default' => ''),
                'last_name'             => array('type' => 'string',  'required' => false, 'default' => ''),
                'phone'                 => array('type' => 'string',  'required' => false, 'default' => ''),
                'location_city'         => array('type' => 'string',  'required' => false, 'default' => ''),
                'location_state'        => array('type' => 'string',  'required' => false, 'default' => ''),
                'location_country'      => array('type' => 'string',  'required' => false, 'default' => ''),
                'company_name'          => array('type' => 'string',  'required' => false, 'default' => ''),
                'company_url'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'locale_language'       => array('type' => 'string',  'required' => false, 'default' => 'en_US'),
                'locale_decimal'        => array('type' => 'string',  'required' => false, 'default' => '.'),
                'locale_thousands'      => array('type' => 'string',  'required' => false, 'default' => ','),
                'locale_dateformat'     => array('type' => 'string',  'required' => false, 'default' => 'm/d/Y'),
                'timezone'              => array('type' => 'string',  'required' => false, 'default' => 'UTC'),
                'verify_code'           => array('type' => 'string',  'required' => false, 'default' => ''),
                'config'                => array('type' => 'object',  'required' => false, 'default' => []),
                'send_email'            => array('type' => 'boolean', 'required' => false, 'default' => true),
                'create_examples' => array('type' => 'boolean', 'required' => false, 'default' => true),
                'require_verification'  => array('type' => 'boolean', 'required' => false, 'default' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();

        // required fields
        $user_name = $validated_post_params['user_name'];
        $email = $validated_post_params['email'];
        $password = $validated_post_params['password'];

        // make sure the user_name is valid syntactically; note: don't check for existence here
        if (!\Flexio\Base\Identifier::isValid($user_name))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, _('This username is invalid.  Please try another.'));

        // make sure the password is valid
        if (\Flexio\Base\Util::isValidPassword($password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, _('The password is invalid.  Please try another.'));

        // configuration fields we don't want to pass on
        $send_email = $validated_post_params['send_email'];

        // TODO: for now, never send an email
        $send_email = false;



        $create_examples = $validated_post_params['create_examples'];
        $require_verification = $validated_post_params['require_verification'];
        unset($validated_post_params['send_email']);
        unset($validated_post_params['create_examples']);
        unset($validated_post_params['require_verification']);

        // try to find the user
        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            if ($user_eid !== false)
                $user = \Flexio\Object\User::load($user_eid);
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // POSSIBILITY 1: user doesn't exist; create the user
        if ($user === false)
        {
            // determine the status and verify code based on whether or not we're requiring verification
            $eid_status = ($require_verification === true ? \Model::STATUS_PENDING : \Model::STATUS_AVAILABLE);
            $verify_code = ($require_verification === true ? \Flexio\Base\Util::generateHandle() : '');

            // set the new user info
            $new_user_info = $validated_post_params;
            $new_user_info['eid_status'] = $eid_status;
            $new_user_info['verify_code'] = $verify_code;

            // create the user
            $user = \Flexio\Object\User::create($new_user_info);
            $user_eid = $user->getEid();

            // owner and created have to be set after creation because a user is
            // created by themselves on signup, but the user eid isn't yet known
            $additional_user_properties = array(
                'owned_by' => $user_eid,
                'created_by' => $user_eid
            );
            $user->set($additional_user_properties);

            $user->grant($user_eid, \Model::ACCESS_CODE_TYPE_EID,
                array(
                    \Flexio\Object\Right::TYPE_READ_RIGHTS,
                    \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                    \Flexio\Object\Right::TYPE_READ,
                    \Flexio\Object\Right::TYPE_WRITE,
                    \Flexio\Object\Right::TYPE_DELETE
                )
            );

            // create a default api key for the user
            $token_properties = array();
            $token_properties['owned_by'] = $user->getEid();
            \Flexio\Object\Token::create($token_properties);

            // if appropriate, send an email
            if ($send_email === true)
            {
                $message_type = \Flexio\Api2\Message::TYPE_EMAIL_WELCOME;
                $email_params = array('email' => $email, 'verify_code' => $verify_code);
                $message = \Flexio\Api2\Message::create($message_type, $email_params);
                $message->send();
            }

            // if appropriate, create examples
            if ($create_examples === true)
                self::createExampleObjects($user_eid);

            // return the user info
            $result = $user->get();
            \Flexio\Api2\Response::sendContent($result);
            return;
        }


        // POSSIBILITY 2: the user already already exists; fail if the user status is anything besides pending
        if ($user->getStatus() != \Model::STATUS_PENDING)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, _('This email address is already taken.  Please try another.'));


        // POSSIBILITY 3: user already exists and has the correct verification code;
        // set the rest of the information, and activate the user
        $existing_verification_code = $user->getVerifyCode();
        $provided_verification_code = $validated_post_params['verify_code'];

        if (strlen($existing_verification_code) > 0 && $existing_verification_code === $provided_verification_code)
        {
            // start with the info provided
            $new_user_info = $validated_post_params;

            // invited users already exist, but need to have the rest of their
            // info set; don't allow last minute changes to the username or email
            unset($new_user_info['user_name']);
            unset($new_user_info['email']);

            // link was clicked in notification email and verify code checks out;
            // so automatically promote user to verified/active status
            $new_user_info['verify_code'] = '';
            $new_user_info['eid_status'] = \Model::STATUS_AVAILABLE;

            $result = $user->set($new_user_info);
            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Operation failed'));

            // we're done; other parts of the account will have already been created
            $result = $user->get();
            \Flexio\Api2\Response::sendContent($result);
            return;
        }


        // POSSIBILITY 4: user exists, but the user isn't yet verified and either the
        // verification code doesn't exist or the verification code that's been provided
        // doesn't match; the user could be trying to create an account after having a
        // object shared with them and having not verified; or a user could be trying
        // to create an account when they hadn't yet completed the verification process
        // previously; in either case, we need to create a new verification code and let
        // the user verify

        $new_verify_code = \Flexio\Base\Util::generateHandle();

        $new_user_info = array();
        $new_user_info['verify_code'] = $new_verify_code;

        $result = $user->set($new_user_info);
        if ($result === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Operation failed'));

        // if appropriate, send an email
        if ($send_email === true)
        {
            $message_type = \Flexio\Api2\Message::TYPE_EMAIL_WELCOME;
            $email_params = array('email' => $email, 'verify_code' => $new_verify_code);
            $message = \Flexio\Api2\Message::create($message_type, $email_params);
            $message->send();
        }

        // return the user info
        $result = $user->get();
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function set(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'user_name'         => array('type' => 'string', 'required' => false),
                'password'          => array('type' => 'string', 'required' => false),
                'full_name'         => array('type' => 'string', 'required' => false),
                'first_name'        => array('type' => 'string', 'required' => false),
                'last_name'         => array('type' => 'string', 'required' => false),
                'email'             => array('type' => 'string', 'required' => false),
                'phone'             => array('type' => 'string', 'required' => false),
                'location_city'     => array('type' => 'string', 'required' => false),
                'location_state'    => array('type' => 'string', 'required' => false),
                'location_country'  => array('type' => 'string', 'required' => false),
                'company_name'      => array('type' => 'string', 'required' => false),
                'company_url'       => array('type' => 'string', 'required' => false),
                'locale_language'   => array('type' => 'string', 'required' => false),
                'locale_decimal'    => array('type' => 'string', 'required' => false),
                'locale_thousands'  => array('type' => 'string', 'required' => false),
                'locale_dateformat' => array('type' => 'string', 'required' => false),
                'timezone'          => array('type' => 'string', 'required' => false),
                'config'            => array('type' => 'object', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // note: password parameter is allowed in this call; it's used in some
        // type of workflow (such as when sharing with a user that doesn't exist)

        $validated_post_params = $validator->getParams();

        // if a user_name is specified, make sure it's valid
        if (isset($validated_post_params['user_name']) && !\Flexio\Base\Identifier::isValid($validated_post_params['user_name']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if a password is specified, make sure it's valid
        if (isset($validated_post_params['password']) && \Flexio\Base\Util::isValidPassword($validated_post_params['password']) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights, but only if the object isn't pending;
        // TODO: proper approach is to always check rights; right now, \Flexio\Api2\User::set()
        // is used to set user info for unverified accounts that were created from an invitation
        // the rest of the info needs to be set to complete the process before the user is able
        // to log in; probably best to pass the verification code and use the \Flexio\Api2\User::create()
        // function to complete the process rather than this, since this approach could be used to
        // set info for unverified users
        if ($owner_user->getStatus() !== \Model::STATUS_PENDING)
        {
            if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        $owner_user->set($validated_post_params);
        $result = $owner_user->get();
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function get(\Flexio\Api2\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $owner_user->get();
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function changepassword(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'old_password'      => array('type' => 'string', 'required' => true),
                'new_password'      => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $old_password = $validated_post_params['old_password'];
        $new_password = $validated_post_params['new_password'];

        // make sure the new password is valid
        if (\Flexio\Base\Util::isValidPassword($new_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($owner_user->checkPassword($old_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, _('The current password entered was incorrect'));

        $new_params = array(
            'password' => $new_password
        );
        $owner_user->set($new_params);
        $result = $owner_user->get();
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function activate(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'     => array('type' => 'string', 'required' => true),
                'verify_code'      => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $code = $validated_post_params['verify_code'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, _('This user is unavailable'));
        }

        if ($user->getStatus() != \Model::STATUS_PENDING)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('This user is already activated'));

        if ($user->getVerifyCode() != $code)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('The activation credentials do not match'));

        if ($user->set(array('eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not activate the user at this time'));

        $result = array();
        $result['email'] = $email;
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function resendverify(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'     => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, _('This user is unavailable'));
        }

        // TODO: if the verify code is a set, but blank, should we regenerate
        // the verification code?

        $verify_code = $user->getVerifyCode();
        if (!isset($verify_code))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, _('Missing verification code'));

        $message_type = \Flexio\Api2\Message::TYPE_EMAIL_WELCOME;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Api2\Message::create($message_type, $email_params);
        $message->send();

        $result = array();
        $result['email'] = $email;
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function requestpasswordreset(\Flexio\Api2\Request $request)
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'     => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $verify_code = \Flexio\Base\Util::generateHandle();

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, _('This user is unavailable'));
        }

        if ($user->set(array('verify_code' => $verify_code)) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not send password reset email at this time'));

        $message_type = \Flexio\Api2\Message::TYPE_EMAIL_RESET_PASSWORD;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Api2\Message::create($message_type, $email_params);
        $message->send();

        $result = array();
        $result['email'] = $email;
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function resetpassword(\Flexio\Api2\Request $request
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'password'    => array('type' => 'string', 'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $password = $validated_post_params['password'];
        $code = $validated_post_params['verify_code'];

        // make sure the new password is valid
        if (\Flexio\Base\Util::isValidPassword($password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, _('This user is unavailable'));
        }

        if ($user->getVerifyCode() !== $code)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, _('The credentials do not match'));

        if ($user->set(array('password' => $password, 'eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not update user at this time'));

        $result = array();
        $result['email'] = $email;
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function resetConfig(\Flexio\Api2\Request $request)
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // note: this is an API endpoint function for debugging; this allows
        // the user configuration to be reset so that items like a welcome page
        // can be displayed again

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $new_params = array('config' => array());
        $owner_user->set($new_params);
        $result = array('success' => true);
        \Flexio\Api2\Response::sendContent($result);
    }

    public static function createExampleObjects(string $user_eid) : array
    {
        // create sample pipes; ensure user creation even if sample fails
        $created_items = array();

        $objects = self::getExampleObjects();
        foreach ($objects as $o)
        {
            if (!isset($o['eid_type']))
                continue;

            $new_object = false;
            switch ($o['eid_type'])
            {
                case \Model::TYPE_CONNECTION:
                    $object_eid = self::createConnectionFromFile($user_eid, $o['path']);
                    $new_object = \Flexio\Object\Connection::load($object_eid);
                    break;

                case \Model::TYPE_PIPE:
                    $object_eid = self::createPipeFromFile($user_eid, $o['path']);
                    $new_object = \Flexio\Object\Pipe::load($object_eid);
                    break;
            }

            if ($new_object !== false)
                $created_items[] = $new_object->get();
        }

        return $created_items;
    }

    public static function createConnectionFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the pipe file and convert it to JSON
        $buf = \Flexio\Base\File::read($file_name);
        $definition = @json_decode($buf,true);
        if ($definition === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: create the object
        $call_params['name'] = $definition['name'] ?? 'Sample Connection';
        $call_params['ename'] = $definition['ename'] ?? '';
        $call_params['description'] = $definition['description'] ?? '';

        if (isset($definition['connection_type']))
            $call_params['connection_type'] = $definition['connection_type'];
        if (isset($definition['connection_info']))
            $call_params['connection_info'] = $definition['connection_info'];
        if (isset($definition['expires']))
            $call_params['expires'] = $definition['expires'];

        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $connection = \Flexio\Object\Connection::create($call_params);

        $connection->grant($user_eid, \Model::ACCESS_CODE_TYPE_EID, array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE,
                \Flexio\Object\Right::TYPE_EXECUTE
            )
        );

        return $connection->getEid();
    }

    public static function createPipeFromFile(string $user_eid, string $file_name) : string
    {
        // STEP 1: read the pipe file and convert it to JSON
        $buf = \Flexio\Base\File::read($file_name);
        $definition = @json_decode($buf,true);
        if ($definition === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // STEP 2: create the object
        $call_params['name'] = $definition['name'] ?? 'Sample Pipe';
        $call_params['ename'] = $definition['ename'] ?? '';
        $call_params['description'] = $definition['description'] ?? '';
        $call_params['task'] = array();
        if (isset($definition['task']))
            $call_params['task'] = $definition['task'];

        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $pipe = \Flexio\Object\Pipe::create($call_params);

        $pipe->grant($user_eid, \Model::ACCESS_CODE_TYPE_EID, array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE,
                \Flexio\Object\Right::TYPE_EXECUTE
            )
        );

        return $pipe->getEid();
    }

    private static function getExampleObjects() : array
    {
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(
            array('eid_type' => \Model::TYPE_CONNECTION, 'path' => $demo_dir . 'connection_amazons3.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_clean_csv.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_convert_csv.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_create_thumbnail.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_pivot_table.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_create_chart.json')
        );

        return $objects;
    }
}
