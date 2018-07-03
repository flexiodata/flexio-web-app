<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-05-01
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;



class OauthHelper extends \PHPMailer\PHPMailer\OAuth
{
    private $email;
    private $token;

    public function __construct()
    {
    }

    public function set($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    public function getOauth64()
    {
        return base64_encode(
            'user=' .
            $this->email .
            "\001auth=Bearer " .
            $this->token .
            "\001\001"
        );
    }
}

class Email
{
    private $from_addresses;
    private $to_addresses;
    private $cc_addresses;
    private $bcc_addresses;
    private $replyto_addresses;
    private $subject;
    private $msg_text;
    private $msg_html;
    private $msg_htmlembedded;
    private $attachments;

    private $protocol;
    private $host;
    private $port;
    private $username;
    private $password;
    private $oauth;

    public static function create(array $params = null) : \Flexio\Services\Email
    {
        if (!isset($params))
            return new self;

        $service = new self;
        $service->initialize($params);
        return $service;
    }

    public static function setEmail(array $params = null)
    {
        if (isset($params['from']))
            $this->setFrom($params['from']);
        if (isset($params['to']))
            $this->setTo($params['to']);
        if (isset($params['cc']))
            $this->setCC($params['cc']);
        if (isset($params['bcc']))
            $this->setBCC($params['bcc']);
        if (isset($params['reply_to']))
            $this->setReplyTo($params['reply_to']);
        if (isset($params['subject']))
            $this->setSubject($params['subject']);
        if (isset($params['msg_text']))
            $this->setMessageText($params['msg_text']);
        if (isset($params['msg_html']))
            $this->setMessageHtml($params['msg_html']);

        if (isset($params['attachments']) && is_array($params['attachments']))
        {
            $attachments = $params['attachments'];
            foreach ($attachments as $a)
            {
                $this->addAttachment($a);
            }
        }

        return $this;
    }

    public function setFrom($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        if ($addresses === null || $addresses === '')
        {
            $this->from_addresses = [];
            return $this;
        }

        // make sure we have an array
        if (is_string($addresses))
            $addresses = explode(',', $addresses);
        if (!is_array($addresses))
            return $this;

        // set the addresses
        $this->from_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getFrom() : array
    {
        return $this->from_addresses;
    }

    public function setTo($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        if ($addresses === null || $addresses === '')
        {
            $this->to_addresses = [];
            return $this;
        }

        // make sure we have an array
        if (is_string($addresses))
            $addresses = explode(',', $addresses);
        if (!is_array($addresses))
            return $this;

        // set the addresses
        $this->to_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getTo() : array
    {
        return $this->to_addresses;
    }

    public function setCC($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        // make sure we have an array
        if (is_string($addresses))
            $addresses = explode(',', $addresses);
        if (!is_array($addresses))
            return $this;

        // set the addresses
        $this->cc_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getCC() : array
    {
        return $this->cc_addresses;
    }

    public function setBCC($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        // make sure we have an array
        if (is_string($addresses))
            $addresses = explode(',', $addresses);
        if (!is_array($addresses))
            return $this;

        // set the addresses
        $this->bcc_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getBCC() : array
    {
        return $this->bcc_addresses;
    }

    public function setReplyTo($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        // make sure we have an array
        if (is_string($addresses))
            $addresses = explode(',', $addresses);
        if (!is_array($addresses))
            return $this;

        // set the addresses
        $this->replyto_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getReplyTo() : array
    {
        return $this->replyto_addresses;
    }

    public function setSubject(string $subject) : \Flexio\Services\Email
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function setMessageText(string $message) : \Flexio\Services\Email
    {
	    $this->msg_text = $message;
        return $this;
    }

    public function getMessageText() : string
    {
        return $this->msg_text;
    }

    public function setMessageHtml(string $message) : \Flexio\Services\Email
    {
        $this->msg_html = $message;
        return $this;
    }

    public function getMessageHtml() : string
    {
        return $this->msg_html;
    }

    public function setMessageHtmlEmbedded(string $message) : \Flexio\Services\Email
    {
        $this->msg_htmlembedded = $message;
        return $this;
    }

    public function getMessageHtmlEmbedded() : string
    {
        return $this->msg_htmlembedded;
    }

    public function addAttachment(array $attachment) : \Flexio\Services\Email
    {
        $a = array();
        $a['name'] = $attachment['name'] ?? '';
        $a['file'] = $attachment['file'] ?? '';
        $a['mime_type'] = $attachment['mime_type'] ?? \Flexio\Base\ContentType::UNDEFINED;
        $a['content'] = $attachment['content'] ?? '';

        $this->attachments[] = $a;
        return $this;
    }

    public function getAttachments() : array
    {
        return $this->attachments;
    }

    public function clearAttachments() : \Flexio\Services\Email
    {
        $this->attachments = array();
        return $this;
    }

    private function initialize($params = []) : void
    {
        $this->oauth = null;
  
        $this->from_addresses = array();
        $this->to_addresses = array();
        $this->cc_addresses = array();
        $this->bcc_addresses = array();
        $this->replyto_addresses = array();
        $this->subject = '';
        $this->msg_text = '';
        $this->msg_html = '';
        $this->msg_htmlembedded = '';
        $this->attachments = array();

        $from = ($params['from'] ?? '');
        if (strlen($from) == 0)
            $from = ($params['email'] ?? '');

        $this->setFrom($from);


        if (($params['connection_type'] ?? '') == 'gmail')
        {
            $this->protocol = 'smtp';
            $this->security = 'ssl';
            $this->authentication = 'oauth2';
            $this->host = 'smtp.gmail.com';
            $this->username = '';
            $this->password = '';
            $this->oauth = new OauthHelper();
            $this->oauth->set($from, $params['access_token']);
        }
         else
        {
            $this->protocol = ($params['protocol'] ?? '');              // none, smtp
            $this->security = ($params['security'] ?? '');              // none, ssl, tls
            $this->authentication = ($params['authentication'] ?? '');  // none, password, encpassword, kerberos, ntlm, oauth2
            $this->host = ($params['host'] ?? '');
            $this->username = ($params['username'] ?? '');
            $this->password = ($params['password'] ?? '');
        }
    }

    public function send() : bool
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true); // passing 'true' enables exceptions

        try
        {
            $port = $this->port;
            if (!$port)
            {
                if ($this->security == 'tls')
                    $port = 587;
                else if ($this->security == 'ssl')
                    $port = 465;
                else
                    $port = 25;
            }

            //$mail->SMTPDebug = 2;                // enable verbose debug output
            $mail->isSMTP();                     // set mailer to use SMTP
            $mail->Host = $this->host;           // specify main and backup SMTP servers
            $mail->SMTPAuth = (strlen($this->username) > 0 || $this->oauth !== null) ? true:false; // Enable SMTP authentication
            $mail->Username = $this->username;    // SMTP username
            $mail->Password = $this->password;    // SMTP password
            $mail->SMTPSecure = $this->security;  // '', 'ssl', or 'tls'
            $mail->Port = $port;
            $mail->Timeout = 60;                  // timeout in seconds

            if ($this->oauth !== null)
            {
                $mail->AuthType = 'XOAUTH2';
                $mail->setOAuth($this->oauth);
            }

            $from = count($this->from_addresses) > 0 ? $this->from_addresses[0] : '';
            $mail->setFrom($from);

            foreach ($this->to_addresses as $email)
            {
                $mail->addAddress($email);     // add a recipient
            }

            foreach ($this->attachments as $attachment)
            {
                $mail->addAttachment($attachment['file'], $attachment['name'], 'base64', $attachment['mime_type']);
            }

            $mail->Subject = $this->subject;
            $mail->Body = $this->msg_text;

            $mail->send();
        }
        catch (\Exception $e)
        {
            //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            return false;
        }

        return true;
    }

    private static function getCleanedAddresses(array $addresses) : array
    {
        $cleaned_addresses = array();
        foreach ($addresses as $a)
        {
            if (!is_string($a))
                continue;

            $a = trim($a);
            if (strlen($a) === 0)
                continue;

            $cleaned_addresses[] = $a;
        }

        return $cleaned_addresses;
    }
}
