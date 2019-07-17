<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams, David Z. Williams
 * Created:  2011-05-12
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class User
{
    public static function create(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username'             => array('type' => 'identifier', 'required' => true),
                'email'                => array('type' => 'email',      'required' => true),
                'password'             => array('type' => 'password',   'required' => true),
                'first_name'           => array('type' => 'string',     'required' => false, 'default' => ''),
                'last_name'            => array('type' => 'string',     'required' => false, 'default' => ''),
                'phone'                => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_city'        => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_state'       => array('type' => 'string',     'required' => false, 'default' => ''),
                'location_country'     => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_name'         => array('type' => 'string',     'required' => false, 'default' => ''),
                'company_url'          => array('type' => 'string',     'required' => false, 'default' => ''),
                'locale_language'      => array('type' => 'string',     'required' => false, 'default' => 'en_US'),
                'locale_decimal'       => array('type' => 'string',     'required' => false, 'default' => '.'),
                'locale_thousands'     => array('type' => 'string',     'required' => false, 'default' => ','),
                'locale_dateformat'    => array('type' => 'string',     'required' => false, 'default' => 'm/d/Y'),
                'timezone'             => array('type' => 'string',     'required' => false, 'default' => 'UTC'),
                'verify_code'          => array('type' => 'string',     'required' => false, 'default' => ''),
                'usage_tier'           => array('type' => 'string',     'required' => false, 'default' => ''),
                'referrer'             => array('type' => 'string',     'required' => false, 'default' => ''),
                'token'                => array('type' => 'string',     'required' => false), // stripe payment token if it's included; TODO: more specific name?
                'config'               => array('type' => 'object',     'required' => false, 'default' => []),
                'send_email'           => array('type' => 'boolean',    'required' => false, 'default' => false), // don't send an email by default
                'create_examples'      => array('type' => 'boolean',    'required' => false, 'default' => true),
                'require_verification' => array('type' => 'boolean',    'required' => false, 'default' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // required fields
        $username = $validated_post_params['username'];
        $email = $validated_post_params['email'];
        $password = $validated_post_params['password'];

        // configuration fields we don't want to pass on
        $send_email = $validated_post_params['send_email'];


        // for now, never send an email
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
            $request->track(\Flexio\Api\Action::TYPE_USER_CREATE);
            $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
            $request->setRequestParams($cleaned_post_params);

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

            $user->grant($user_eid, array(
                    \Flexio\Object\Action::TYPE_READ_RIGHTS,
                    \Flexio\Object\Action::TYPE_WRITE_RIGHTS,
                    \Flexio\Object\Action::TYPE_READ,
                    \Flexio\Object\Action::TYPE_WRITE,
                    \Flexio\Object\Action::TYPE_DELETE
                )
            );

            // create a default api key for the user
            $token_properties = array();
            $token_properties['owned_by'] = $user->getEid();
            \Flexio\Object\Token::create($token_properties);

            // if a token is set, try to add a card; however, don't fail if it can't be added
            // since the overall user creation has already succeeded
            $stripe_token = $validated_post_params['token'] ?? false;
            if ($stripe_token !== false)
            {
                try
                {
                    $source_info = self::addCustomerPaymentSource($user, $stripe_token);
                }
                catch (\Flexio\Base\Exception $e)
                {
                }
            }

            // if appropriate, send an email
            if ($send_email === true)
            {
                $message_type = \Flexio\Api\Message::TYPE_EMAIL_WELCOME;
                $email_params = array('email' => $email, 'verify_code' => $verify_code);
                $message = \Flexio\Api\Message::create($message_type, $email_params);
                $message->send();
            }

            // if appropriate, create examples
            if ($create_examples === true)
                self::createExampleObjects($user_eid);

            // return the user info
            $result = $user->get();
            $request->setRequestingUser($user_eid);
            $request->setResponseParams($result);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result);
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
            unset($new_user_info['username']);
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
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);
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
            $message_type = \Flexio\Api\Message::TYPE_EMAIL_WELCOME;
            $email_params = array('email' => $email, 'verify_code' => $new_verify_code);
            $message = \Flexio\Api\Message::create($message_type, $email_params);
            $message->send();
        }

        // return the user info
        $result = $user->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function purge(\Flexio\Api\Request $request) : void
    {
        // purge will permanently delete all database records associated
        // with the given owner; username and password confirmation is
        // required as a precaution

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username' => array('type' => 'string', 'required' => true), // allow string here to accomodate username/email
                'password' => array('type' => 'password', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $entered_username = $post_params['username'];
        $entered_password = $post_params['password'];

        // make sure the requesting user has delete rights for the owner
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // as an additional check, confirm that the username and password for the intended
        // user to delete match the username and password of the user being deleted
        $entered_user_eid = \Flexio\Object\User::getEidFromIdentifier($entered_username); // get eid from username or email

        if ($owner_user->getEid() !== $entered_user_eid)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, _('The username entered doesn\'t match the username of the user to delete'));

        if ($owner_user->checkPassword($entered_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, _('The passwored entered doesn\'t match the password of the user to delete'));

        // permanently delete the user (purge)
        $owner_user->purge();

        // if the requesting user is the same as the user being deleted,
        // clear the login identity (equivalent to logging out)
        if ($owner_user_eid === $requesting_user_eid)
        {
            \Flexio\System\System::clearLoginIdentity();
            @session_destroy();
            @setcookie('FXSESSID', '', time()-86400, '/');
        }

        // return the information for the deleted user
        $result = array();
        $result['eid'] = $owner_user_eid;
        $result['eid_type'] = \Model::TYPE_USER;

        $request->setResponseParams($result);
        \Flexio\Api\Response::sendContent($result);
    }

    public static function set(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_USER_UPDATE);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'username'          => array('type' => 'identifier', 'required' => false),
                'email'             => array('type' => 'email',      'required' => false),
                'password'          => array('type' => 'password',   'required' => false),
                'first_name'        => array('type' => 'string',     'required' => false),
                'last_name'         => array('type' => 'string',     'required' => false),
                'phone'             => array('type' => 'string',     'required' => false),
                'location_city'     => array('type' => 'string',     'required' => false),
                'location_state'    => array('type' => 'string',     'required' => false),
                'location_country'  => array('type' => 'string',     'required' => false),
                'company_name'      => array('type' => 'string',     'required' => false),
                'company_url'       => array('type' => 'string',     'required' => false),
                'locale_language'   => array('type' => 'string',     'required' => false),
                'locale_decimal'    => array('type' => 'string',     'required' => false),
                'locale_thousands'  => array('type' => 'string',     'required' => false),
                'locale_dateformat' => array('type' => 'string',     'required' => false),
                'timezone'          => array('type' => 'string',     'required' => false),
                'usage_tier'        => array('type' => 'string',     'required' => false),
                'referrer'          => array('type' => 'string',     'required' => false),
                'config'            => array('type' => 'object',     'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // note: password parameter is allowed in this call; it's used in some
        // type of workflow (such as when sharing with a user that doesn't exist)

        $validated_post_params = $validator->getParams();

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights, but only if the object isn't pending;
        // TODO: proper approach is to always check rights; right now, \Flexio\Api\User::set()
        // is used to set user info for unverified accounts that were created from an invitation
        // the rest of the info needs to be set to complete the process before the user is able
        // to log in; probably best to pass the verification code and use the \Flexio\Api\User::create()
        // function to complete the process rather than this, since this approach could be used to
        // set info for unverified users
        if ($owner_user->getStatus() !== \Model::STATUS_PENDING)
        {
            if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        $owner_user->set($validated_post_params);
        $result = $owner_user->get();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function get(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $owner_user->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function listcards(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the stripe customer id; if a key doesn't yet exist, there are no sources
        $stripe_customer_id = $owner_user->getStripeCustomerId();
        if (strlen($stripe_customer_id) === 0)
        {
            $result = array();
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result);
            return;
        }

        // create/update customer with payment source
        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);

        $result = curl_exec($ch);
        $source_info = @json_decode($result, true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $result = array();
        foreach ($source_info['data'] as $s)
        {
            $result[] = self::mapCardSourceInfo($s);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function addcard(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // TODO: user update action or something else?
        $request->track(\Flexio\Api\Action::TYPE_USER_UPDATE);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'token' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $stripe_token = $validated_post_params['token'];

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $source_info = self::addCustomerPaymentSource($owner_user, $stripe_token);

        // return the list of cards
        $result = $source_info;
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function deletecard(\Flexio\Api\Request $request) : void
    {
        $request_url = $request->getUrl();
        $url_params = $request->getUrlParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // TODO: user update action or something else?
        $request->track(\Flexio\Api\Action::TYPE_USER_UPDATE);

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $path = $request_url;

        $pos = strpos($path, '/account/cards/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // get the card (stripe source) identifier
        $stripe_source_id = $url_params['apiparam4'] ?? false;
        if ($stripe_source_id === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        $source_info = self::deleteCustomerPaymentSource($owner_user, $stripe_source_id);

        $result = array(
            'card_id' => $source_info['id'],
            'card_type' => $source_info['brand'],
            'card_last4' => $source_info['last4'],
            'card_exp_month' => $source_info['exp_month'],
            'card_exp_years' => $source_info['exp_year']
        );

        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function changepassword(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $request->track(\Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        // note: in validator below, use 'string' type for old/new passwords to allow them
        // to be any string; we'll validate password format separately so we can return
        // a custom error message that the old/new password entered was invalid as opposed to
        // a syntax error
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'old_password' => array('type' => 'string', 'required' => true),
                'new_password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $old_password = $validated_post_params['old_password'];
        $new_password = $validated_post_params['new_password'];

        if (\Flexio\Base\Password::isValid($old_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, _('The current password entered was incorrect'));
        if (\Flexio\Base\Password::isValid($new_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('A password must have at least 8 characters with at least 1 number'));

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($owner_user->checkPassword($old_password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAUTHORIZED, _('The current password entered was incorrect'));

        $new_params = array(
            'password' => $new_password
        );
        $owner_user->set($new_params);
        $result = $owner_user->get();
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function resetpassword(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $request->track(\Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET);
        $cleaned_post_params = self::cleanProperties($post_params); // don't store sensitive info
        $request->setRequestParams($cleaned_post_params);

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'       => array('type' => 'email',    'required' => true),
                'password'    => array('type' => 'password', 'required' => true),
                'verify_code' => array('type' => 'string',   'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $password = $validated_post_params['password'];
        $code = $validated_post_params['verify_code'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        if ($user->getVerifyCode() !== $code)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, _('The credentials do not match'));

        if ($user->set(array('password' => $password, 'eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not update user at this time'));

        $result = array();
        $result['email'] = $email;
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function activate(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $code = $validated_post_params['verify_code'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        if ($user->getStatus() != \Model::STATUS_PENDING)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('This user is already activated'));

        if ($user->getVerifyCode() != $code)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('The activation credentials do not match'));

        if ($user->set(array('eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not activate the user at this time'));

        $result = array();
        $result['email'] = $email;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function resendverify(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email' => array('type' => 'email', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        // TODO: if the verify code is a set, but blank, should we regenerate
        // the verification code?

        $verify_code = $user->getVerifyCode();
        if (!isset($verify_code))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Missing verification code'));

        $message_type = \Flexio\Api\Message::TYPE_EMAIL_WELCOME;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Api\Message::create($message_type, $email_params);
        $message->send();

        $result = array();
        $result['email'] = $email;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function requestpasswordreset(\Flexio\Api\Request $request) : void
    {
        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email' => array('type' => 'email', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $verify_code = \Flexio\Base\Util::generateHandle();

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        if ($user->set(array('verify_code' => $verify_code)) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not send password reset email at this time'));

        $message_type = \Flexio\Api\Message::TYPE_EMAIL_RESET_PASSWORD;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Api\Message::create($message_type, $email_params);
        $message->send();

        $result = array();
        $result['email'] = $email;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function resetConfig(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // note: this is an API endpoint function for debugging; this allows
        // the user configuration to be reset so that items like a welcome page
        // can be displayed again

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $new_params = array('config' => array());
        $owner_user->set($new_params);
        $result = array('success' => true);

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public function setProfilePicture(\Flexio\Api\Request $request) : void
    {
        // TODO: patch through to implementation in \Flexio\Object\User
    }

    public function getProfilePicture(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $content = '';
        $etag = $owner_user->getProfilePicture($content);

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");

        \Flexio\Api\Response::sendRaw($content);
    }

    public function setProfileBackground(\Flexio\Api\Request $request) : void
    {
        // TODO: patch through to implementation in \Flexio\Object\User
    }

    public function getProfileBackground(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $content = '';
        $etag = $owner_user->getProfileBackground($content);

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");

        \Flexio\Api\Response::sendRaw($content);
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
        $call_params['name'] = $definition['name'] ?? 'sample-connection';
        $call_params['description'] = $definition['description'] ?? '';

        if (isset($definition['connection_status']))
            $call_params['connection_status'] = $definition['connection_status'];
        if (isset($definition['connection_type']))
            $call_params['connection_type'] = $definition['connection_type'];
        if (isset($definition['connection_info']))
            $call_params['connection_info'] = $definition['connection_info'];
        if (isset($definition['expires']))
            $call_params['expires'] = $definition['expires'];

        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $connection = \Flexio\Object\Connection::create($call_params);

        $connection->grant($user_eid, array(
                \Flexio\Object\Action::TYPE_READ_RIGHTS,
                \Flexio\Object\Action::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Action::TYPE_READ,
                \Flexio\Object\Action::TYPE_WRITE,
                \Flexio\Object\Action::TYPE_DELETE,
                \Flexio\Object\Action::TYPE_EXECUTE
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
        $call_params['name'] = $definition['name'] ?? 'sample-pipe';
        $call_params['description'] = $definition['description'] ?? '';
        $call_params['task'] = array();
        if (isset($definition['task']))
            $call_params['task'] = $definition['task'];

        $call_params['owned_by'] = $user_eid;
        $call_params['created_by'] = $user_eid;
        $pipe = \Flexio\Object\Pipe::create($call_params);

        $pipe->grant($user_eid, array(
                \Flexio\Object\Action::TYPE_READ_RIGHTS,
                \Flexio\Object\Action::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Action::TYPE_READ,
                \Flexio\Object\Action::TYPE_WRITE,
                \Flexio\Object\Action::TYPE_DELETE,
                \Flexio\Object\Action::TYPE_EXECUTE
            )
        );

        return $pipe->getEid();
    }

    public static function validateCredentials(\Flexio\Api\Request $request) : void
    {
        // function for validating user credentials (e.g. username, email, password)
        // publicly available

        $post_params = $request->getPostParams();

        // the input for a validation is an array of objects that each
        // have a key, value and type; only 10 items can be validated at a time
        /*
        [
            {
                "key": "",
                "value": "",
                "type": ""
            },
            {
                "key": "",
                "value": "",
                "type": ""
            }
        ]
        */

        // make sure params is an array
        if (!is_array($post_params))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (count($post_params) === 0 || count($post_params) > 10)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // make sure each of the items in the array has the required params
        foreach ($post_params as $p)
        {
            // checks to see if a username is available
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($p, array(
                    'key'      => array('type' => 'string', 'required' => true),
                    'value'    => array('type' => 'string', 'required' => true),
                    'type'     => array('type' => 'string', 'required' => true),
                    'eid_type' => array('type' => 'string', 'required' => false)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        // build up the return object of results
        $result = array();
        foreach ($post_params as $p)
        {
            $result[] = self::validateCredentialItem($p);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function validateObjects(\Flexio\Api\Request $request) : void
    {
        // function for validating user objects (e.g. pipe name, connection name, script syntax)
        // requires appropriate permissions

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // the input for a validation is an array of objects that each
        // have a key, value and type; only 10 items can be validated at a time
        /*
        [
            {
                "key": "",
                "value": "",
                "type": ""
            },
            {
                "key": "",
                "value": "",
                "type": ""
            }
        ]
        */

        // make sure params is an array
        if (!is_array($post_params))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (count($post_params) === 0 || count($post_params) > 10)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // make sure each of the items in the array has the required params
        foreach ($post_params as $p)
        {
            // checks to see if a username is available
            $validator = \Flexio\Base\Validator::create();
            if (($validator->check($p, array(
                    'key'      => array('type' => 'string', 'required' => true),
                    'value'    => array('type' => 'string', 'required' => true),
                    'type'     => array('type' => 'string', 'required' => true),
                    'eid_type' => array('type' => 'string', 'required' => false)
                ))->hasErrors()) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        // build up the return object of results
        $result = array();
        foreach ($post_params as $p)
        {
            $result[] = self::validateObjectItem($p, $owner_user_eid, $requesting_user_eid);
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    private static function validateCredentialItem(array $params) : array
    {
        $key = $params['key'];
        $value = $params['value'];
        $type = $params['type'];
        $eid_type = $params['eid_type'] ?? \Model::TYPE_UNDEFINED;

        $valid = false;
        $message = '';

        switch ($type)
        {
            case 'identifier':
            case 'username':
                $valid = self::validateIdentifier($type, $value, $message);
                break;

            case 'email':
                $valid = self::validateEmail($type, $value, $message);
                break;

            case 'password':
                {
                    if (\Flexio\Base\Password::isValid($value) === false)
                    {
                        // invalid password
                        $valid = false;
                        $message = _('A password must have at least 8 characters with at least 1 number');
                    }
                    else
                    {
                        $valid = true;
                        $message = '';
                    }
                }
                break;
        }

        // echo back the key and whether or not it's valid (note: don't echo
        // back the value to minimize transport of values like a password)
        $properties = array();
        $properties['key'] = $key;
        $properties['valid'] = $valid;
        $properties['message'] = $message;

        return $properties;
    }

    private static function validateObjectItem(array $params, string $owner_user_eid, string $requesting_user_eid = '') : array
    {
        $key = $params['key'];
        $value = $params['value'];
        $type = $params['type'];
        $eid_type = $params['eid_type'] ?? \Model::TYPE_UNDEFINED;

        // continue with the validation checks
        $valid = false;
        $message = '';

        switch ($type)
        {
            case 'name':
                $valid = self::validateName($type, $eid_type, $owner_user_eid, $requesting_user_eid, $value, $message);
                break;

            // python/javascript
            case 'javascript':
            case 'nodejs':
            case 'python':
                {
                    // TODO: for now, validation of scripts isn't depenent on any specific
                    // user info; simply make sure a user is logged in
                    if (\Flexio\Base\Eid::isValid($requesting_user_eid) === false)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

                    $code = base64_decode($value);
                    $err = \Flexio\Jobs\Execute::checkScript($type, $code);
                    if ($err === true)
                    {
                        $valid = true;
                        $message = '';
                    }
                     else
                    {
                        $valid = false;
                        $message = $err;
                    }
                }
                break;

            // task
            case 'task':
            {
                // TODO: for now, validation of tasks isn't depenent on any specific
                // user info; simply make sure a user is logged in
                if (\Flexio\Base\Eid::isValid($requesting_user_eid) === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

                $task = $value;
                $error_list = array();
                $err = self::validateTask($requesting_user_eid, $task, $error_list);

                if ($err === true)
                {
                    $valid = true;
                    $message = '';
                }
                 else
                {
                    // if we have an error, return the message for the first error
                    $message = '';
                    if (count($error_list) > 0)
                        $message = $error_list[0]['message'];

                    $valid = false;
                    $message = $message;
                }
            }
            break;
        }

        // echo back the key and whether or not it's valid (note: don't echo
        // back the value to minimize transport of values like a password)
        $properties = array();
        $properties['key'] = $key;
        $properties['valid'] = $valid;
        $properties['message'] = $message;

        return $properties;
    }

    private static function validateIdentifier(string $type, string $value, string &$message = '') : bool
    {
        $prefix = 'An identifier';

        switch ($type)
        {
            case 'identifier': $prefix = 'An identifier';  break;
            case 'username':   $prefix = 'A username';     break;
        }

        // check for valid identifier
        if (\Flexio\Base\Identifier::isValid($value, $message, $prefix) === false)
            return false;

        // check if identifier already exists
        if (\Flexio\Object\User::getEidFromUsername($value) !== false)
        {
            switch ($type)
            {
                case 'identifier':
                    $message = _('This identifier is already taken.');
                    break;
                case 'username':
                    $message = _('This username is already taken.');
                    break;
            }

            return false;
        }

        return true;
    }

    private static function validateEmail(string $type, string $value, string &$message = '') : bool
    {
        // check for valid email
        if (\Flexio\Base\Email::isValid($value) === false)
        {
            $message = _('This email address must be formatted correctly.');
            return false;
        }

        // check if email already exists
        if (\Flexio\Object\User::getEidFromEmail($value) !== false)
        {
            $message = _('This email address is already taken.');
            return false;
        }

        return true;
    }

    private static function validateName(string $type, string $eid_type, string $owner_user_eid, string $requesting_user_eid, string $value, string &$message = '') : bool
    {
        try
        {
            $prefix = 'A name';

            // check for valid identifier
            if (\Flexio\Base\Identifier::isValid($value, $message, $prefix) === false)
                return false;

            // a name can only be specific for a valid user; if the user doesn't load
            // an exception will be thrown
            $owner_user = \Flexio\Object\User::load($owner_user_eid);

            // make sure the requesting user has rights to the user (the object may
            // not exist, which means we need to check against write privileges on
            // the user); throw an exception, which will cause the function to return
            // false, which won't reveal any info about names created by a particular
            // user
            if ($owner_user->allows($requesting_user_eid, \Flexio\Object\Action::TYPE_WRITE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

            if (($eid_type == \Model::TYPE_PIPE || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Pipe::getEidFromName($owner_user->getEid(), $value) !== false)
            {
                // identifier already exists
                $message = _('This name is already taken.');
                return false;
            }

            if (($eid_type == \Model::TYPE_CONNECTION || $eid_type == \Model::TYPE_UNDEFINED) &&
                \Flexio\Object\Connection::getEidFromName($owner_user->getEid(), $value) !== false)
            {
                // identifier already exists
                $message = _('This name is already taken.');
                return false;
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            // can't validate against the user (e.g. user doesn't exist)
            return false;
        }

        return true;
    }

    private static function validateTask(string $process_owner_eid, array $task, array &$errors) : bool
    {
        // returns true if the task is valid; false otherwise; sets the errors
        // parameter to an array of errors

        $process = \Flexio\Jobs\Process::create();
        $process->setOwner($process_owner_eid);

        $errors = $process->validate($t);
        if (count($errors) > 0)
            return false;

        return true;
    }

    private static function getExampleObjects() : array
    {
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        $objects = array(
            array('eid_type' => \Model::TYPE_CONNECTION, 'path' => $demo_dir . 'connection_amazons3.json'),

            /*
            TODO:

            Example: Keyword API
            Description: Create an API from keywords from a website
            * Demonstrates how to parse data from a website
            * Demonstrates how to create an use an API endpoint to get pipe contents

            Example: Access APIs that require Oauth
            Description: Extract keywords from Gmail
            * Demonstrates ability to access APIs that use Oauth
            * Demonstrates ability to read read/manipulate Gmail

            Example: Process Incoming Emails
            * Demonstrates ability to email pipes
            * Demonstrates how to extract parts from incoming email and process it

            */

            // Example: Email Results of a Python Function
            // Description: Get the top 5 stories from the Firebase Hacker News Feed and deliver via email
            // * Demonstrates scheduling
            // * Demonstrates emailing of results
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_email_results_of_python_function.json'),

            // Example: Save Data to Local Storage
            // Description: Aggregate, de-duplicate and save recent stories from Hacker News to local storage
            // * Demonstrates saving results to local storage
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_save_data_to_local_storage.json'),

            // Example: Analyze Data with Nodejs and Python
            // Description: Analyze Job Postings on GitHub Jobs with Nodejs Lodash and Python Pandas
            // * Demonstrates fact that various libraries are supported; execute is more than bare-bones
            // * Demonstrates fact that executes can be chained together
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_analyze_data_with_nodejs_and_python.json'),

            // Example: Server-Side Input Validation
            // Description: Validate input with a server-side logic that hides data used to validate with Python Cerberus
            // * Demonstrates how to use input area for testing
            // * Demonstrates reading from server-side data to validate/invalidate data
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_serverside_input_validation.json'),

            // legacy
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_generate_sample_data_api_feed.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_visualize_data_from_an_api_feed.json'),
            array('eid_type' => \Model::TYPE_PIPE,'path' => $demo_dir . 'pipe_render_webpage.json')
        );

        return $objects;
    }

    private static function addCustomerPaymentSource(\Flexio\Object\User $user, string $token) : array
    {
        // create/update customer with payment source
        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        // two possibilities:
        // 1. no stripe customer id (default); create new customer and update the customer id
        // 2. existing customer id; create a source manually on the existing customer

        $source_info = array();

        $stripe_customer_id = $user->getStripeCustomerId();
        if (strlen($stripe_customer_id) === 0)
        {
            $headers = array();
            $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';

            $stripe_api_endpoint = 'https://api.stripe.com/v1/customers';
            $post_data = "source=$token";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $result = curl_exec($ch);
            $customer_info = @json_decode($result, true);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 200)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            // update the customer id
            $new_stripe_customer_id = $customer_info['id'];
            $new_params = array(
                'stripe_customer_id' => $new_stripe_customer_id
            );
            $user->set($new_params);

            // get the newly created card (should only have one since the customer was just created)
            $sources = $customer_info['sources']['data'];
            if (count($sources) < 1)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $source_info = self::mapCardSourceInfo($sources[0]);
        }
        else
        {
            $headers = array();
            $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';

            // create the source
            $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources";
            $post_data = "source=$token";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $result = curl_exec($ch);
            $source_info = @json_decode($result, true);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 200)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $source_info = self::mapCardSourceInfo($source_info);
        }

        return $source_info;
    }

    private static function deleteCustomerPaymentSource(\Flexio\Object\User $user, string $source_id) : array
    {
        // delete the payment source
        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $stripe_customer_id = $user->getStripeCustomerId();
        $stripe_source_id = $source_id;
        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources/$stripe_source_id";
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);

        $result = curl_exec($ch);
        $source_info = @json_decode($result, true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);

        return $source_info;
    }

    private static function getCustomerPaymentSources(string $stripe_secret_key, string $stripe_customer_id) :? array
    {
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);

        $result = curl_exec($ch);
        $source_info = @json_decode($result, true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
            return null;

        $result = array();
        foreach ($source_info['data'] as $s)
        {
            $result[] = array(
                'card_id' => $s['id'],
                'card_type' => $s['brand'],
                'card_last4' => $s['last4'],
                'card_exp_month' => $s['exp_month'],
                'card_exp_years' => $s['exp_year']
            );
        }

        return $result;
    }

    private static function mapCardSourceInfo(array $source) : array
    {
        $result = array(
            'card_id' => $source['id'],
            'card_type' => $source['brand'],
            'card_last4' => $source['last4'],
            'card_exp_month' => $source['exp_month'],
            'card_exp_years' => $source['exp_year']
        );

        return $result;
    }

    private static function cleanProperties(array $properties) : array
    {
        if (isset($properties['password']))
            $properties['password'] = "*****";

        if (isset($properties['old_password']))
            $properties['old_password'] = "*****";

        if (isset($properties['new_password']))
            $properties['new_password'] = "*****";

        if (isset($properties['verify_code']))
            $properties['verify_code'] = "*****";

        if (isset($properties['token']))
            $properties['token'] = "*****";

        return $properties;
    }
}
