<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
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
    const TYPE_EMAIL_WELCOME             = 'email_welcome';
    const TYPE_EMAIL_RESET_PASSWORD      = 'email_reset_password';
    const TYPE_EMAIL_SHARE               = 'email_share';
    const TYPE_EMAIL_PIPE                = 'email_pipe';

    protected $messge_type;
    protected $message_params;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create(string $type, array $params) : \Flexio\Api\Message
    {
        switch ($type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            case self::TYPE_EMAIL_WELCOME:
            case self::TYPE_EMAIL_RESET_PASSWORD:
            case self::TYPE_EMAIL_SHARE:
            case self::TYPE_EMAIL_PIPE:
                break;
        }

        $object = (new self);
        $object->message_type = $type;
        $object->message_params = $params;

        return $object;
    }

    public function send() : bool
    {
        switch ($this->message_type)
        {
            default:
                return false;

            case self::TYPE_EMAIL_WELCOME:
                return self::createWelcomeEmail($this->message_params);

            case self::TYPE_EMAIL_RESET_PASSWORD:
                return self::createResetPasswordEmail($this->message_params);

            case self::TYPE_EMAIL_SHARE:
                return self::createShareProjectEmail($this->message_params);

            case self::TYPE_EMAIL_PIPE:
                return self::createSharePipeEmail($this->message_params);
        }
    }

    private function initialize()
    {
        $this->message_type = false;
        $this->message_params = false;
    }

    private static function createWelcomeEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->getParams()) === false)
            return false;

        $to = $params['email'];
        $verify_code = $params['verify_code'];

        $activation_link = self::getBaseUrl() . '/app/signin?ref=verification_email&email='.urlencode($to).'&verify_code='.$verify_code;

        // get templates from the application res directory
        $msg_text = self::getTextEmail('template-account-verify', [ 'activation_link' => $activation_link ]);
        $msg_html = self::getHtmlEmail('template-account-verify', [ 'activation_link' => $activation_link ]);

        // send an email that the user's account was created
        $email = \Flexio\Services\Email::create(array(
            'from' => 'Flex.io <no-reply@flex.io>',
            'to' => $to,
            'subject' => 'Welcome to Flex.io',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    private static function createResetPasswordEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))->getParams()) === false)
            return false;

        $to = $params['email'];
        $verify_code = $params['verify_code'];

        // prepare info structure for create account
        $reset_link = self::getBaseUrl() . '/app/resetpassword?email='.urlencode($to).'&verify_code='.$verify_code;

        // get templates from the application res directory
        $msg_text = self::getTextEmail('template-forgot-password', [ 'reset_link' => $reset_link ]);
        $msg_html = self::getHtmlEmail('template-forgot-password', [ 'reset_link' => $reset_link ]);

        // send an email that the user's account was created
        $email = \Flexio\Services\Email::create(array(
            'from' => 'Flex.io <no-reply@flex.io>',
            'to' => $to,
            'subject' => 'Flex.io password reset',
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    private static function createShareProjectEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'from_name'   => array('type' => 'string', 'required' => true),
                'from_email'   => array('type' => 'string', 'required' => true),
                'object_name' => array('type' => 'string', 'required' => true),
                'object_eid'  => array('type' => 'string', 'required' => true),
                'verify_code' => array('type' => 'string', 'required' => false),
                'message'     => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            return false;

        $to = $params['email'];
        $from_name = $params['from_name'];
        $from_email = $params['from_email'];
        $object_name = $params['object_name'];
        $object_eid = $params['object_eid'];
        $message = $params['message'] ?? '';
        $verify_code_str = isset($params['verify_code']) ? '&verify_code='.$params['verify_code'] : '';
        $share_link = self::getBaseUrl() . '/a/shareauth?ref=share_email&email='.urlencode($to).'&object_eid='. $object_eid . $verify_code_str;

        // get text template from the application res directory
        $msg_text = self::getTextEmail('template-project-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "\n$message\n",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        // get html template from the application res directory
        $msg_html = self::getHtmlEmail('template-project-share', [
            'name' => $from_name,
            'from_email' => $from_email,
            'message' => (strlen($message) == 0) ? '' : "$message<br><br>",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        $email = \Flexio\Services\Email::create(array(
            'from' => "$from_name via Flex.io <no-reply@flex.io>",
            'to' => $to,
            'subject' => "${from_name} wants to share \"${object_name}\" with you",
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    private static function createSharePipeEmail(array $params) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'from_name'   => array('type' => 'string', 'required' => true),
                'object_name' => array('type' => 'string', 'required' => true),
                'object_eid'  => array('type' => 'string', 'required' => true),
                'message'     => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            return false;

        $to = $params['email'];
        $from_name = $params['from_name'];
        $object_name = $params['object_name'];
        $object_eid = $params['object_eid'];
        $message = $params['message'] ?? '';
        $share_link = self::getBaseUrl() . "/app/pipe/$object_eid";

        // get text template from the application res directory
        $msg_text = self::getTextEmail('template-pipe-share', [
            'name' => $from_name,
            'message' => (strlen($message) == 0) ? '' : "\n$message\n",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        // get html template from the application res directory
        $msg_html = self::getHtmlEmail('template-pipe-share', [
            'name' => $from_name,
            'message' => (strlen($message) == 0) ? '' : "$message<br><br>",
            'object_name' => $object_name,
            'share_link' => $share_link
        ]);

        $email = \Flexio\Services\Email::create(array(
            'from' => "$from_name via Flex.io <no-reply@flex.io>",
            'to' => $to,
            'subject' => "${from_name} shared pipe \"${object_name}\" with you",
            'msg_text' => $msg_text,
            'msg_html' => $msg_html
        ));
        return $email->send();
    }

    private static function getHtmlEmail(string $template_file, array $replacement_strs) : string
    {
        $res_dir = \Flexio\System\System::getResDirectory();

        $btn_primary_style = file_get_contents($res_dir . DIRECTORY_SEPARATOR . 'template-btn-primary.html');
        $msg_template = file_get_contents($res_dir . DIRECTORY_SEPARATOR . 'template-html-email.html');

        // load template file contents
        $msg = file_get_contents($res_dir . DIRECTORY_SEPARATOR . "$template_file.html");

        // replace message and button tokens from template files
        $msg = str_replace('${html_message}', $msg, $msg_template);
        $msg = str_replace('${btn_primary_style}', $btn_primary_style, $msg);

        // now replace all tokens that are in the message
        foreach ($replacement_strs as $k => $v)
        {
            $msg = str_replace('${'.$k.'}', $v, $msg);
        }

        return $msg;
    }

    private static function getTextEmail(string $template_file, array $replacement_strs) : string
    {
        $res_dir = \Flexio\System\System::getResDirectory();

        // load template file contents
        $msg = file_get_contents($res_dir . DIRECTORY_SEPARATOR . "$template_file.txt");

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
