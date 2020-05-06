<?php
/**
 *
 * Copyright (c) 2011, Flex Research LLC. All rights reserved.
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
    public const REQUIRE_VERIFICATION = true;
    public const CREATE_DEFAULT_TOKEN = true;
    public const CREATE_DEFAULT_EXAMPLES = true;
    public const SEND_WELCOME_EMAIL = true;
    public const SEND_INVITE_EMAIL = true;

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
                'referrer'             => array('type' => 'string',     'required' => false, 'default' => ''),
                'token'                => array('type' => 'string',     'required' => false), // stripe payment token if it's included; TODO: more specific name?
                'stripe_subscription_id' => array('type' => 'string',     'required' => false, 'default' => ''),
                'config'               => array('type' => 'object',     'required' => false, 'default' => [])
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();

        // required fields
        $username = $validated_post_params['username'];
        $email = $validated_post_params['email'];
        $password = $validated_post_params['password'];

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

            // note: following logic is paralleled when creating a new user
            // in the user api implementation, except for the initial user
            // parameters which are populated partially here by the api request

            // create the user; determine the status and verify code based on
            // whether or not we're requiring verification
            $new_user_info = $validated_post_params;
            $new_user_info['email'] = $email;
            $new_user_info['eid_status'] = (\Flexio\Api\User::REQUIRE_VERIFICATION === true ? \Model::STATUS_PENDING : \Model::STATUS_AVAILABLE);
            $user = \Flexio\Object\User::create($new_user_info);

            // if appropriate, create an a default api token
            if (\Flexio\Api\User::CREATE_DEFAULT_TOKEN === true)
            {
                $token_properties = array();
                $token_properties['owned_by'] = $user->getEid();
                \Flexio\Object\Token::create($token_properties);
            }

            // if appropriate, create default examples
            if (\Flexio\Api\User::CREATE_DEFAULT_EXAMPLES === true)
            {
                // create default example objects
                $created_objects = \Flexio\Object\Factory::createExampleObjects($user->getEid());

                // run default pipes; this will run in the background and load data into
                // the index
                foreach ($created_objects as $o)
                {
                    if ($o->getType() !== \Model::TYPE_PIPE)
                        continue;

                    $pipe_eid = $o->getEid();
                    \Flexio\Api\Pipe::runFromEid($pipe_eid);
                }
            }

            // if a token is set, try to add a card; however, don't fail if it can't be added
            // since the overall user creation has already succeeded
            $stripe_token = $validated_post_params['token'] ?? false;
            if ($stripe_token !== false)
            {
                try
                {
                    // DEPRECATED: this mechanism is here to support old notion of creating a signup with
                    // basic CC info and was used in support of other services, such as the pdf-to-json,
                    // without gathering billing info; adding CC on signup is no longer used, but leave
                    // here for the time being
                    //$billing_info = array();
                    //$source_info = self::addCustomerPaymentSource($user, $billing_info, $stripe_token);
                }
                catch (\Flexio\Base\Exception $e)
                {
                }
            }

            // if appropriate, send an email
            if (\Flexio\Api\User::SEND_WELCOME_EMAIL === true)
            {
                // generate verification code that's good for 24 hours
                $verify_code_expires_in = 3600*24;
                $user_verify_code = $user->createVerifyCode($verify_code_expires_in);

                // send the email
                $email_params = array('email' => $email, 'verify_code' => $user_verify_code);
                \Flexio\Api\Message::sendWelcomeEmail($email_params);
            }

            // get the user info
            $result_user_info = $user->get();

            // track the user signup;
            // DISABLE: re-enabled on front end
            //\Flexio\Api\Segment::track(\Flexio\Api\Segment::TYPE_SIGNED_UP, $user->getEid(), $result_user_info);

            // return the user info
            $request->setRequestingUser($user->getEid());
            $request->setResponseParams($result_user_info);
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            $request->track();
            \Flexio\Api\Response::sendContent($result_user_info);
            return;
        }


        // POSSIBILITY 2: the user already exists; fail if the user status is anything besides pending
        if ($user->getStatus() != \Model::STATUS_PENDING)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, _('This email address is already taken.  Please try another.'));


        // POSSIBILITY 3: user already exists and has the correct verification code;
        // set the rest of the information, and activate the user
        $provided_verification_code = $validated_post_params['verify_code'];
        if ($user->checkVerifyCode($provided_verification_code) === true)
        {
            // start with the info provided
            $new_user_info = $validated_post_params;

            // invited users already exist, but need to have the rest of their
            // info set; as an sanity check, don't allow the email address to be,
            // changed, however this is how the current user was selected, so
            // it should always be the same
            unset($new_user_info['email']);

            // link was clicked in notification email and verify code checks out;
            // so automatically promote user to verified/active status
            $new_user_info['eid_status'] = \Model::STATUS_AVAILABLE;

            if ($user->set($new_user_info) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Operation failed'));

            // we're done; other parts of the account will have already been created
            $result_user_info = $user->get();
            $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
            \Flexio\Api\Response::sendContent($result_user_info);
            return;
        }


        // POSSIBILITY 4: user exists, but the user isn't yet verified and either the
        // verification code doesn't exist or the verification code that's been provided
        // doesn't match; the user could be trying to create an account after having a
        // object shared with them and having not verified; or a user could be trying
        // to create an account when they hadn't yet completed the verification process
        // previously; in either case, we need to create a new verification code and let
        // the user verify

        // start with the info provided
        $new_user_info = $validated_post_params;

        // invited users already exist, but need to have the rest of their
        // info set; as an sanity check, don't allow the email address to be,
        // changed, however this is how the current user was selected, so
        // it should always be the same
        unset($new_user_info['email']);

        // require the user to be verified; user status should already be pending,
        // but set it to pending as a sanity check
        $new_user_info['eid_status'] = \Model::STATUS_PENDING;
        if ($user->set($new_user_info) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Operation failed'));

        // if appropriate, send an email
        if (\Flexio\Api\User::SEND_WELCOME_EMAIL === true)
        {
            // generate verification code that's good for 24 hours
            $verify_code_expires_in = 3600*24;
            $user_verify_code = $user->createVerifyCode($verify_code_expires_in);

            // send the email
            $email_params = array('email' => $email, 'verify_code' => $user_verify_code);
            \Flexio\Api\Message::sendWelcomeEmail($email_params);
        }

        // get the user info
        $result_user_info = $user->get();

        // TODO: we want to track users that are signing up who have been invited into
        // a team; in this case, the user record is created when the user is invited
        // in \Flexio\Api\TeamMember::create() (look for \Flexio\Object\User::create)
        // but the user doesn't sign up until they supply the additional information,
        // which is at this point in the logic flow, and also where we want to track
        // the signup; however, this logic flow is also triggered by users that have
        // already signed up but have supplied an invalid/expired verification code;
        // so, to track signups for invited teammembers, we need a way to distinguish
        // them from simple invalid verifications; this should suffice for the time
        // being since the worst that happens are duplicate signup trackings in the
        // rare case of verification failures
        // DISABLE: re-enabled on front end
        //\Flexio\Api\Segment::track(\Flexio\Api\Segment::TYPE_SIGNED_UP, $user->getEid(), $result_user_info);

        // return the user info
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result_user_info);
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_DELETE) === false)
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
            \Flexio\System\System::destroyUserSession();

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
                'stripe_subscription_id' => array('type' => 'string',     'required' => false),
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
            if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_UPDATE) === false)
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $owner_user->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function billing(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = [];

        $stripe_customer_id = $owner_user->getStripeCustomerId();
        $stripe_customer_info = self::getStripeCustomer($stripe_customer_id);
        $stripe_source_info = self::getStripePaymentSource($stripe_customer_id);
        $result = array_merge($stripe_customer_info, $stripe_source_info);

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function changebilling(\Flexio\Api\Request $request) : void
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
                'token'               => array('type' => 'string', 'required' => false),
                'billing_name'        => array('type' => 'string', 'required' => false),
                'billing_company'     => array('type' => 'string', 'required' => false),
                'billing_email'       => array('type' => 'string', 'required' => false),
                'billing_address1'    => array('type' => 'string', 'required' => false),
                'billing_address2'    => array('type' => 'string', 'required' => false),
                'billing_city'        => array('type' => 'string', 'required' => false),
                'billing_state'       => array('type' => 'string', 'required' => false),
                'billing_postal_code' => array('type' => 'string', 'required' => false),
                'billing_country'     => array('type' => 'string', 'required' => false),
                'billing_other'       => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $billing_info = $validated_post_params;

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stripe_customer_info = array();
        $stripe_source_info = array();

        // create/update the customer with the billing info and save
        // the customer id to the user
        $stripe_customer_id = $owner_user->getStripeCustomerId();
        if (strlen($stripe_customer_id) === 0)
        {
            // if we don't have a customer, create one and save the customer id
            $stripe_customer_info = self::createStripeCustomer($billing_info);
            $stripe_customer_id = $stripe_customer_info['customer_id'];
            $user_info = array(
                'stripe_customer_id' => $stripe_customer_id
            );
            $owner_user->set($user_info);
        }

        if (strlen($stripe_customer_id) > 0)
        {
            // merge newly specified information with existing information (updateStripeCustomer()
            // function currently does a complete replace on billing address info, so merge here)
            $existing_stripe_customer_info = self::getStripeCustomer($stripe_customer_id);
            $billing_info = array_merge($existing_stripe_customer_info, $billing_info);
            $stripe_customer_info = self::updateStripeCustomer($stripe_customer_id, $billing_info);
        }

        // get the updated payment source info; TODO: can get this from the customer object
        $stripe_source_info = self::getStripePaymentSource($stripe_customer_id);

        // return the billing info
        $result = array_merge($stripe_customer_info, $stripe_source_info);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function plan(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        // load the object
        $owner_user = \Flexio\Object\User::load($owner_user_eid);

        // check the rights on the object
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = [];

        $stripe_customer_id = $owner_user->getStripeCustomerId();
        $stripe_subscription_info = self::getStripeSubscription($stripe_customer_id);
        $stripe_pending_invoice_info = self::getStripePendingInvoice($stripe_customer_id);
        $result = array_merge($stripe_subscription_info, $stripe_pending_invoice_info);

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function changeplan(\Flexio\Api\Request $request) : void
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
                'subscription_item_id' => array('type' => 'string', 'required' => false),
                'plan_id'              => array('type' => 'string', 'required' => false),
                'coupon_id'            => array('type' => 'string', 'required' => false),
                'seat_cnt'             => array('type' => 'integer', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $subscription_info = $validated_post_params;

        // load the user
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create/update the customer with the billing info and save
        // the customer id to the user
        $stripe_customer_id = $owner_user->getStripeCustomerId();
        if (strlen($stripe_customer_id) === 0)
        {
            // if we don't have a customer, create one and save the customer id
            $stripe_customer_info = self::createStripeCustomer(array());
            $stripe_customer_id = $stripe_customer_info['customer_id'];
            $user_info = array(
                'stripe_customer_id' => $stripe_customer_id
            );
            $owner_user->set($user_info);
        }

        // get the subscription id from the user object
        $stripe_subscription_id = $owner_user->getStripeSubscriptionId();

        $stripe_subscription_info = array();
        $stripe_pending_invoice_info = array();

        if (strlen($stripe_customer_id) > 0)
        {
            if (strlen($stripe_subscription_id) === 0)
            {
                // if we don't have a subscription for this customer,
                // create one and save the subscription id
                $stripe_subscription_info = self::createStripeSubscription($stripe_customer_id, $subscription_info);
                $stripe_subscription_id = $stripe_subscription_info['subscription_id'];
                $user_info = array(
                    'stripe_subscription_id' => $stripe_subscription_id
                );
                $owner_user->set($user_info);
            } else {
                // if we have a subscription for this customer, update it now
                $stripe_subscription_info = self::updateStripeSubscription($stripe_subscription_id, $subscription_info);
            }

            $stripe_pending_invoice_info = self::getStripePendingInvoice($stripe_customer_id);
        }

        // return the subscription info along with pending invoice info
        $result = array_merge($stripe_subscription_info, $stripe_pending_invoice_info);
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
        \Flexio\Api\Response::sendContent($result);
    }

    public static function deletecard(\Flexio\Api\Request $request) : void
    {
        // TODO: currently here for reference; delete when it's clear there's
        // nothing to reuse elsewhere
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);

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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_UPDATE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $path = $request_url;

        $pos = strpos($path, '/account/cards/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        // get the card (stripe source) identifier
        $stripe_source_id = $url_params['apiparam4'] ?? false;
        if ($stripe_source_id === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        $stripe_customer_id = $owner_user->getStripeCustomerId();
        $source_info = self::deleteStripePaymentSource($stripe_customer_id, $stripe_source_id);

        // return the card info
        $result = $source_info;
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE) === false)
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

    public static function requestverification(\Flexio\Api\Request $request) : void
    {
        // note: sends an email with a verification code to the users
        // email address with a link that will let them verify their
        // account; processed by requestpasswordreset()

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
            if ($user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        // generate verification code that's good for 24 hours
        $verify_code_expires_in = 3600*24;
        $user_verify_code = $user->createVerifyCode($verify_code_expires_in);

        // send the email
        $email_params = array('email' => $email, 'verify_code' => $user_verify_code);
        \Flexio\Api\Message::sendWelcomeEmail($email_params);

        $result = array();
        $result['email'] = $email;

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function processverification(\Flexio\Api\Request $request) : void
    {
        // note: takes the verification code from requestverification()
        // and verifies the account

        $post_params = $request->getPostParams();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($post_params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_post_params = $validator->getParams();
        $email = $validated_post_params['email'];
        $verify_code = $validated_post_params['verify_code'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            if ($user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        // TODO: temporarily disable user pending status to activate; allow any
        // user to be activated
        //if ($user->getStatus() != \Model::STATUS_PENDING)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('This user is already activated'));

        if ($user->checkVerifyCode($verify_code) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('The activation credentials do not match'));

        if ($user->set(array('eid_status' => \Model::STATUS_AVAILABLE)) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not activate the user at this time'));

        $result = array();
        $result['email'] = $email;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function requestpasswordreset(\Flexio\Api\Request $request) : void
    {
        // note: sends an email with a verification code to the users
        // email address with a link that will let them reset the password
        // on their account; processed by processpasswordreset()

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
            if ($user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        // generate verification code that's good for 24 hours
        $verify_code_expires_in = 3600*24;
        $user_verify_code = $user->createVerifyCode($verify_code_expires_in);

        // send the email
        $email_params = array('email' => $email, 'verify_code' => $user_verify_code);
        \Flexio\Api\Message::sendResetPasswordEmail($email_params);

        $result = array();
        $result['email'] = $email;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function processpasswordreset(\Flexio\Api\Request $request) : void
    {
        // note: takes the verification code from requestpasswordreset()
        // and resets the password to the new password

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
        $verify_code = $validated_post_params['verify_code'];

        $user = false;
        try
        {
            $user_eid = \Flexio\Object\User::getEidFromEmail($email);
            if ($user_eid === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $user = \Flexio\Object\User::load($user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        catch (\Flexio\Base\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, _('This user is unavailable'));
        }

        if ($user->checkVerifyCode($verify_code) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, _('The credentials do not match'));

        if ($user->set(array('password' => $password, 'eid_status' => \Model::STATUS_AVAILABLE)) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, _('Could not update user at this time'));

        // make sure the user is logged out so they have to resign in
        \Flexio\System\System::destroyUserSession();

        $result = array();
        $result['email'] = $email;
        $request->setResponseParams($result);
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        $request->track();
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_UPDATE) === false)
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_READ) === false)
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
        if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_USER_READ) === false)
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

        // check if email is already taken by a user

        // if the email is already correlated with a user, see if the user
        // is pending or active; if the user is active, the email is taken
        // and therefore invalid; if the user is pending, the email isn't
        // considered taken because the user hasn't validated the email yet;
        // for example, this situation may happen when a user is invited into
        // a team with an email and a pending user placeholder created, but
        // the user hasn't yet signed up and then goes to enter their email
        // address

        $filter = array('email' => $value, 'eid_status' => \Model::STATUS_AVAILABLE);
        $users = \Flexio\System\System::getModel()->user->list($filter);

        if (count($users) > 0)
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

            // check the rights
            if ($eid_type === \Model::TYPE_PIPE)
            {
                if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_PIPE_READ) === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
            }

            if ($eid_type === \Model::TYPE_CONNECTION)
            {
                if ($owner_user->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
            }

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

    private static function createStripeCustomer(array $billing_info) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = 'https://api.stripe.com/v1/customers';

        $post_data = array(
            'name' => $billing_info['billing_name'] ?? '',
            'email' => $billing_info['billing_email'] ?? '',
            'address' => array(
                'line1'       => $billing_info['billing_address1'] ?? '',
                'line2'       => $billing_info['billing_address2'] ?? '',
                'city'        => $billing_info['billing_city'] ?? '',
                'state'       => $billing_info['billing_state'] ?? '',
                'postal_code' => $billing_info['billing_postal_code'] ?? '',
                'country'     => $billing_info['billing_country'] ?? ''
            ),
            'metadata' => array(
                'billing_company' => $billing_info['billing_company'] ?? '',
                'billing_other'   => $billing_info['billing_other'] ?? ''
            )
        );
        $post_data = http_build_query($post_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, $message);
        }

        $customer_info = @json_decode($httpresult, true);
        $customer_info = self::mapStripeCustomerInfo($customer_info);
        return $customer_info;
    }

    private static function updateStripeCustomer(string $stripe_customer_id, array $billing_info) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id";

        $post_data = array(
            'name' => $billing_info['billing_name'] ?? '',
            'email' => $billing_info['billing_email'] ?? '',
            'address' => array(
                'line1'       => $billing_info['billing_address1'] ?? '',
                'line2'       => $billing_info['billing_address2'] ?? '',
                'city'        => $billing_info['billing_city'] ?? '',
                'state'       => $billing_info['billing_state'] ?? '',
                'postal_code' => $billing_info['billing_postal_code'] ?? '',
                'country'     => $billing_info['billing_country'] ?? ''
            ),
            'metadata' => array(
                'billing_company' => $billing_info['billing_company'] ?? '',
                'billing_other'   => $billing_info['billing_other'] ?? ''
            )
        );
        if (isset($billing_info['token']))
            $post_data['source'] = $billing_info['token'];

        $post_data = http_build_query($post_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, $message);
        }

        $customer_info = @json_decode($httpresult, true);
        $customer_info = self::mapStripeCustomerInfo($customer_info);
        return $customer_info;
    }

    private static function getStripeCustomer(string $stripe_customer_id) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        // if we don't have a customer id, return the properties with empty values
        $customer_info = array();
        if (strlen($stripe_customer_id) === 0)
            return self::mapStripeCustomerInfo($customer_info);

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id";

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
        }

        $customer_info = @json_decode($httpresult, true);
        $customer_info = self::mapStripeCustomerInfo($customer_info);
        return $customer_info;
    }

    private static function addStripePaymentSource(string $stripe_customer_id, string $stripe_token) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources";

        $post_data = array(
            'source' => $stripe_token
        );
        $post_data = http_build_query($post_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, $message);
        }

        $source_info = @json_decode($httpresult, true);
        $source_info = self::mapStripeSourceInfo($source_info);
        return $source_info;
    }

    private static function deleteStripePaymentSource($stripe_customer_id, string $stripe_source_id) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = "https://api.stripe.com/v1/customers/$stripe_customer_id/sources/$stripe_source_id";

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED, $message);
        }

        $source_info = @json_decode($httpresult, true);
        $source_info = self::mapStripeSourceInfo($source_info);
        return $source_info;
    }

    private static function getStripePaymentSource(string $stripe_customer_id) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api

        // if we don't have a customer id, return the properties with empty values
        if (strlen($stripe_customer_id) === 0)
            return self::mapStripeSourceInfo(array());

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
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
        }

        // stripe supports more than one source per customer, but we only
        // expose one card in the billing info; get the first source item
        $sources = @json_decode($httpresult, true);
        $source_info = $sources['data'];

        if (count($source_info) < 1)
            return self::mapStripeSourceInfo(array());

        return self::mapStripeSourceInfo($source_info[0]);
    }

    private static function createStripeSubscription(string $stripe_customer_id, array $subscription_info) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api/subscriptions/create

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = 'https://api.stripe.com/v1/subscriptions';

        $post_data = array(
            'customer' => $stripe_customer_id ?? '',
            'items' => array(
                array(
                    'plan' => $subscription_info['plan_id'] ?? '',
                    'quantity' => $subscription_info['seat_cnt'] ?? 1
                )
            )
        );

        // only post a coupon code if the length is greater than zero
        // (this will avoid removing existing coupons in Stripe)
        $coupon_id = $subscription_info['coupon_id'] ?? '';
        if (strlen($coupon_id) > 0)
            $post_data['coupon'] = $coupon_id;

        $post_data = http_build_query($post_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, $message);
        }

        $subscription_info = @json_decode($httpresult, true);

        if (count($subscription_info['items']) < 1)
            return self::mapStripeSubscriptionInfo(array());

        return self::mapStripeSubscriptionInfo($subscription_info);
    }

    private static function updateStripeSubscription(string $stripe_subscription_id, array $subscription_info) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api/subscriptions/update

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $stripe_api_endpoint = "https://api.stripe.com/v1/subscriptions/$stripe_subscription_id";

        $post_data = array(
            'items' => array(
                array(
                    'id' => $subscription_info['subscription_item_id'] ?? '',
                    'plan' => $subscription_info['plan_id'] ?? '',
                    'quantity' => $subscription_info['seat_cnt'] ?? 1
                )
            )
        );

        // only post a coupon code if the length is greater than zero
        // (this will avoid removing existing coupons in Stripe)
        $coupon_id = $subscription_info['coupon_id'] ?? '';
        if (strlen($coupon_id) > 0)
            $post_data['coupon'] = $coupon_id;

        $post_data = http_build_query($post_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $stripe_api_endpoint);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, $message);
        }

        $subscription_info = @json_decode($httpresult, true);

        if (count($subscription_info['items']) < 1)
            return self::mapStripeSubscriptionInfo(array());

        return self::mapStripeSubscriptionInfo($subscription_info);
    }

    private static function getStripeSubscription(string $stripe_customer_id) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api/subscriptions/list

        // if we don't have a customer id, return the properties with empty values
        if (strlen($stripe_customer_id) === 0)
            return self::mapStripeSubscriptionInfo(array());

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $get_data = array(
            'customer' => $stripe_customer_id ?? ''
        );
        $query_str = http_build_query($get_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $stripe_api_endpoint = "https://api.stripe.com/v1/subscriptions";
        $url = $stripe_api_endpoint . '?' . $query_str;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
        }

        // stripe supports more than one source per customer, but we only
        // expose one card in the billing info; get the first source item
        $subscriptions = @json_decode($httpresult, true);
        $subscription_info = $subscriptions['data'];

        if (count($subscription_info) < 1)
            return self::mapStripeSubscriptionInfo(array());

        return self::mapStripeSubscriptionInfo($subscription_info[0]);
    }

    private static function getStripePendingInvoice(string $stripe_customer_id) : array
    {
        // see here for more information:
        // https://stripe.com/docs/api/subscriptions/list

        // if we don't have a customer id, return the properties with empty values
        if (strlen($stripe_customer_id) === 0)
            return self::mapStripePendingInvoiceInfo(array());

        $stripe_secret_key = $GLOBALS['g_config']->stripe_secret_key ?? false;
        if ($stripe_secret_key === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::ERROR_GENERAL);

        $get_data = array(
            'customer' => $stripe_customer_id ?? ''
        );
        $query_str = http_build_query($get_data);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $stripe_secret_key;
        $stripe_api_endpoint = "https://api.stripe.com/v1/invoices/upcoming";
        $url = $stripe_api_endpoint . '?' . $query_str;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $httpresult = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200)
        {
            $error = @json_decode($httpresult, true);
            $message = $error['error']['message'] ?? '';
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
        }

        // stripe supports more than one source per customer, but we only
        // expose one card in the billing info; get the first source item
        $pending_invoice_info = @json_decode($httpresult, true);
        return self::mapStripePendingInvoiceInfo($pending_invoice_info);
    }

    private static function mapStripeCustomerInfo(array $customer_info) : array
    {
        $result = array(
            'customer_id' => $customer_info['id'] ?? '',
            'billing_name' => $customer_info['name'] ?? '',
            'billing_email' => $customer_info['email'] ?? '',
            'billing_address1' => $customer_info['address']['line1'] ?? '',
            'billing_address2' => $customer_info['address']['line2'] ?? '',
            'billing_city' => $customer_info['address']['city'] ?? '',
            'billing_state' => $customer_info['address']['state'] ?? '',
            'billing_postal_code' => $customer_info['address']['postal_code'] ?? '',
            'billing_country' => $customer_info['address']['country'] ?? '',
            'billing_company' => $customer_info['metadata']['billing_company'] ?? '',
            'billing_other' => $customer_info['metadata']['billing_other'] ?? ''
        );

        return $result;
    }

    private static function mapStripeSourceInfo(array $source_info) : array
    {
        $result = array(
            'card_id' => $source_info['id'] ?? '',
            'card_type' => $source_info['brand'] ?? '',
            'card_last4' => $source_info['last4'] ?? '',
            'card_exp_month' => $source_info['exp_month'] ?? '',
            'card_exp_years' => $source_info['exp_year'] ?? ''
        );

        return $result;
    }

    private static function mapStripeSubscriptionInfo(array $subscription_info) : array
    {
        $result = array(
            'subscription_id' => $subscription_info['id'] ?? '',
            'subscription_item_id' => $subscription_info['items']['data'][0]['id'] ?? '',
            'plan_id' => $subscription_info['items']['data'][0]['plan']['id'] ?? '',
            'seat_cnt' => $subscription_info['items']['data'][0]['quantity'] ?? 1,
            'discount' => array(
                'id' => $subscription_info['discount']['coupon']['id'] ?? '',
                'name' => $subscription_info['discount']['coupon']['name'] ?? '',
                'amount_off' => $subscription_info['discount']['coupon']['amount_off'] ?? 0,
                'percent_off' => $subscription_info['discount']['coupon']['percent_off'] ?? 0
            )
        );

        return $result;
    }

    private static function mapStripePendingInvoiceInfo(array $pending_invoice_info) : array
    {
        $result = array(
            'next_amount_due' => $pending_invoice_info['amount_due'],
            'next_amount_paid' => $pending_invoice_info['amount_paid'],
            'next_amount_remaining' => $pending_invoice_info['amount_remaining']
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

        if (isset($properties['token']))
            $properties['token'] = "*****";

        return $properties;
    }
}
