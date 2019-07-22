<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
    public static function sendWelcomeEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to = $validated_params['email'];
        $verify_code = $validated_params['verify_code'];

        $activation_link = self::getBaseUrl() . '/app/signin?ref=verification_email&email='.urlencode($to).'&verify_code='.$verify_code;

        // get templates from the application res directory
        $msg_text = self::getTextEmail('account-verify', [ 'activation_link' => $activation_link ]);
        $msg_html = self::getHtmlEmail('account-verify', [ 'activation_link' => $activation_link ]);

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to,
            'subject' => 'Welcome to Flex.io',
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
        $to = $validated_params['email'];
        $verify_code = $validated_params['verify_code'];

        // prepare info structure for create account
        $reset_link = self::getBaseUrl() . '/app/resetpassword?email='.urlencode($to).'&verify_code='.$verify_code;

        // get templates from the application res directory
        $msg_text = self::getTextEmail('forgot-password', [ 'reset_link' => $reset_link ]);
        $msg_html = self::getHtmlEmail('forgot-password', [ 'reset_link' => $reset_link ]);

        // send an email that the user's account was created
        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to,
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
                'from_name'   => array('type' => 'string', 'required' => true),
                'from_email'  => array('type' => 'email',  'required' => true),
                'object_name' => array('type' => 'string', 'required' => true),
                'message'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to = $validated_params['email'];
        $from_name = $validated_params['from_name'];
        $from_email = $validated_params['from_email'];
        $object_name = $validated_params['object_name'];
        $message = $validated_params['message'] ?? '';
        $share_link = self::getBaseUrl() . "/app/$object_name";

        // get text template from the application res directory
        $msg_text = self::getTextEmail('project-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "\n$message\n",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        // get html template from the application res directory
        $msg_html = self::getHtmlEmail('project-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "$message<br>",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => "$from_name via " . \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to,
            'subject' => "${from_name} invited you into the \"${object_name}\" team",
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    public static function sendSharePipeEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'email'       => array('type' => 'email',  'required' => true),
                'from_name'   => array('type' => 'string', 'required' => true),
                'from_email'  => array('type' => 'email',  'required' => true),
                'object_name' => array('type' => 'string', 'required' => true),
                'message'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            return false;

        $validated_params = $validator->getParams();
        $to = $validated_params['email'];
        $from_name = $validated_params['from_name'];
        $from_email = $validated_params['from_email'];
        $object_name = $validated_params['object_name'];
        $message = $validated_params['message'] ?? '';
        $share_link = self::getBaseUrl() . "/app/pipe/$object_name";

        // get text template from the application res directory
        $msg_text = self::getTextEmail('pipe-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "\n$message\n",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        // get html template from the application res directory
        $msg_html = self::getHtmlEmail('pipe-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "$message<br>",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        $email = \Flexio\Services\NoticeEmail::create(array(
            'from' => "$from_name via " . \Flexio\Services\NoticeEmail::EMAIL_ADDRESS_NO_REPLY,
            'to' => $to,
            'subject' => "${from_name} shared pipe \"${object_name}\" with you",
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

        return $msg;
    }

    private static function getBaseUrl() : string
    {
        return 'https://' . $_SERVER['SERVER_NAME'];
    }
}
