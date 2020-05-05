<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-30
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Message
{
    public const NOTICE_TRIAL_HALFWAY = 'notice-trial-halfway';
    public const NOTICE_TRIAL_ENDING = 'notice-trial-ending';

    public static function sendWelcomeEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to_email = $validated_params['email'];
        $verify_code = $validated_params['verify_code'];

        $activation_link = \Flexio\System\System::getUserVerificationLink($to_email, $verify_code);

        // get templates from the application res directory
        $msg_text = self::getTextEmail('account-verify', [ 'verify_code' => $verify_code, 'activation_link' => $activation_link ]);
        $msg_html = self::getHtmlEmail('account-verify', [ 'verify_code' => $verify_code, 'activation_link' => $activation_link ]);

        // if a test email address is specified, override the test email
        // note: test email override only available in debug mode
        $test_email_address = self::getTestEmailAddress();
        if (strlen($test_email_address) > 0)
            $to_email = $test_email_address;

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to_email,
            'subject' => 'Welcome to Flex.io',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    public static function sendTrialHalfwayEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'first_name'  => array('type' => 'string',  'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to_email = $validated_params['email'];
        $first_name = $validated_params['first_name'];

        $billing_link = 'https://www.flex.io/app/account/billing';

        // get templates from the application res directory
        $msg_text = self::getTextEmail('trial-halfway', [ 'first_name' => $first_name, 'billing_link' => $billing_link ]);
        $msg_html = self::getHtmlEmail('trial-halfway', [ 'first_name' => $first_name, 'billing_link' => $billing_link ]);

        // if a test email address is specified, override the test email
        // note: test email override only available in debug mode
        $test_email_address = self::getTestEmailAddress();
        if (strlen($test_email_address) > 0)
            $to_email = $test_email_address;

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to_email,
            'subject' => 'Your free trial is ending in 7 days',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    public static function sendTrialEndingEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'first_name'  => array('type' => 'string',  'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to_email = $validated_params['email'];
        $first_name = $validated_params['first_name'];

        $billing_link = 'https://www.flex.io/app/account/billing';

        // get templates from the application res directory
        $msg_text = self::getTextEmail('trial-ending', [ 'first_name' => $first_name, 'billing_link' => $billing_link ]);
        $msg_html = self::getHtmlEmail('trial-ending', [ 'first_name' => $first_name, 'billing_link' => $billing_link ]);

        // if a test email address is specified, override the test email
        // note: test email override only available in debug mode
        $test_email_address = self::getTestEmailAddress();
        if (strlen($test_email_address) > 0)
            $to_email = $test_email_address;

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to_email,
            'subject' => 'Your free trial ends tomorrow',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    public static function sendResetPasswordEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to_email = $validated_params['email'];
        $verify_code = $validated_params['verify_code'];

        // prepare info structure for create account
        $reset_link = \Flexio\System\System::getPasswordResetLink($to_email, $verify_code);

        // get templates from the application res directory
        $msg_text = self::getTextEmail('forgot-password', [ 'reset_link' => $reset_link ]);
        $msg_html = self::getHtmlEmail('forgot-password', [ 'reset_link' => $reset_link ]);

        // if a test email address is specified, override the test email
        // note: test email override only available in debug mode
        $test_email_address = self::getTestEmailAddress();
        if (strlen($test_email_address) > 0)
            $to_email = $test_email_address;

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to_email,
            'subject' => 'Flex.io password reset',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    public static function sendTeamInvitationEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true),
                'from_name'   => array('type' => 'string', 'required' => true),
                'from_email'  => array('type' => 'email',  'required' => true),
                'object_name' => array('type' => 'string', 'required' => true),
                'message'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to_email = $validated_params['email'];
        $verify_code = $validated_params['verify_code'];
        $from_name = $validated_params['from_name'];
        $from_email = $validated_params['from_email'];
        $object_name = $validated_params['object_name'];
        $message = $validated_params['message'] ?? '';
        $share_link = \Flexio\System\System::getTeamInviteLink($to_email, $verify_code, $object_name);

        // if a test email address is specified, override the test email
        // note: test email override only available in debug mode
        $test_email_address = self::getTestEmailAddress();
        if (strlen($test_email_address) > 0)
            $to_email = $test_email_address;

        // get text template from the application res directory
        $msg_text = self::getTextEmail('team-invite', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "\n$message\n",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        // get html template from the application res directory
        $msg_html = self::getHtmlEmail('team-invite', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "$message<br>",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => "$from_name via " . \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to_email,
            'subject' => "${from_name} invited you to the team \"${object_name}\" on Flex.io",
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    private static function getHtmlEmail(string $template_file, array $replacement_strs) : string
    {
        $email_dir = \Flexio\System\System::getEmailTemplateDirectory();

        // load template file contents
        $msg = file_get_contents($email_dir . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . "$template_file.html");

        // now replace all tokens that are in the message
        foreach ($replacement_strs as $k => $v)
        {
            $msg = str_replace('${'.$k.'}', $v, $msg);
            $msg = str_replace('&#36;{'.$k.'}', $v, $msg); // MJML compiler HTML-encodes the '$' character
        }

        // always replace ${year}
        $date = date("Y");
        $msg = str_replace('${year}', $date, $msg);
        $msg = str_replace('&#36;{year}', $date, $msg); // MJML compiler HTML-encodes the '$' character

        return $msg;
    }

    private static function getTextEmail(string $template_file, array $replacement_strs) : string
    {
        $email_dir = \Flexio\System\System::getEmailTemplateDirectory();

        // load template file contents
        $msg = file_get_contents($email_dir . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR . "$template_file.txt");

        // now replace all tokens that are in the message
        foreach ($replacement_strs as $k => $v)
        {
            $msg = str_replace('${'.$k.'}', $v, $msg);
        }

        // always replace ${year}
        $date = date("Y");
        $msg = str_replace('${year}', $date, $msg);
        $msg = str_replace('&#36;{year}', $date, $msg); // MJML compiler HTML-encodes the '$' character

        return $msg;
    }

    private static function getTestEmailAddress() : string
    {
        if (!IS_DEBUG())
            return '';

        $test_email_address = $GLOBALS['g_config']->test_email_address ?? '';
        return $test_email_address;
    }
}
