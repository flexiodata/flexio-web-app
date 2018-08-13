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
    private $email; // email properties (e.g. from, to, subject, etc)
    private $protocol;
    private $security;
    private $authentication;
    private $host;
    private $port;
    private $username;
    private $password;
    private $oauth;

    public function __construct()
    {
        $this->email = \Flexio\Base\Email::create();
        $this->protocol = '';
        $this->security = '';
        $this->authentication = '';
        $this->host = '';
        $this->port = '';
        $this->username = '';
        $this->password = '';
        $this->oauth = null;
    }

    public static function create(array $params = null) : \Flexio\Services\Email
    {
        $service = new self;

        // set the email properties
        if (isset($params['from']))
            $service->setFrom($params['from']);
        if (isset($params['to']))
            $service->setTo($params['to']);
        if (isset($params['cc']))
            $service->setCC($params['cc']);
        if (isset($params['bcc']))
            $service->setBCC($params['bcc']);
        if (isset($params['reply_to']))
            $service->setReplyTo($params['reply_to']);
        if (isset($params['subject']))
            $service->setSubject($params['subject']);
        if (isset($params['msg_text']))
            $service->setMessageText($params['msg_text']);
        if (isset($params['msg_html']))
            $service->setMessageHtml($params['msg_html']);

        if (isset($params['attachments']) && is_array($params['attachments']))
        {
            $attachments = $params['attachments'];
            foreach ($attachments as $a)
            {
                $service->addAttachment($a);
            }
        }

        // set the email connection info
        if (($params['connection_type'] ?? '') == 'gmail')
        {
            $service->protocol = 'smtp';
            $service->security = 'ssl';
            $service->authentication = 'oauth2';
            $service->host = 'smtp.gmail.com';
            $service->port = null;
            $service->username = '';
            $service->password = '';
            $service->oauth = new OauthHelper();
            $service->oauth->set($from, $params['access_token']);
        }
         else
        {
            $service->protocol = ($params['protocol'] ?? '');              // none, smtp
            $service->security = ($params['security'] ?? '');              // none, ssl, tls
            $service->authentication = ($params['authentication'] ?? '');  // none, password, encpassword, kerberos, ntlm, oauth2
            $service->host = ($params['host'] ?? '');
            $service->port = ($params['port'] ?? '');
            $service->username = ($params['username'] ?? '');
            $service->password = ($params['password'] ?? '');
            $service->oauth = null;
        }

        return $service;
    }

    public function setFrom($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        $this->email->setFrom($addresses);
        return $this;
    }

    public function getFrom() : array
    {
        return $this->email->getFrom();
    }

    public function setTo($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        $this->email->setTo($addresses);
        return $this;
    }

    public function getTo() : array
    {
        return $this->email->getTo();
    }

    public function setCC($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        $this->email->setCC($addresses);
        return $this;
    }

    public function getCC() : array
    {
        return $this->email->getCC();
    }

    public function setBCC($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        $this->email->setBCC($addresses);
        return $this;
    }

    public function getBCC() : array
    {
        return $this->email->getBCC();
    }

    public function setReplyTo($addresses) : \Flexio\Services\Email // TODO: add parameter type
    {
        $this->email->setReplyTo($addresses);
        return $this;
    }

    public function getReplyTo() : array
    {
        return $this->email->getReplyTo();
    }

    public function setSubject(string $subject) : \Flexio\Services\Email
    {
        $this->email->setSubject($subject);
        return $this;
    }

    public function getSubject() : string
    {
        return $this->email->getSubject();
    }

    public function setMessageText(string $message) : \Flexio\Services\Email
    {
        $this->email->setMessageText($message);
        return $this;
    }

    public function getMessageText() : string
    {
        return $this->email->getMessageText();
    }

    public function setMessageHtml(string $message) : \Flexio\Services\Email
    {
        $this->email->setMessageHtml($message);
        return $this;
    }

    public function getMessageHtml() : string
    {
        return $this->email->getMessageHtml();
    }

    public function setMessageHtmlEmbedded(string $message) : \Flexio\Services\Email
    {
        $this->email->setMessageHtmlEmbedded($message);
        return $this;
    }

    public function getMessageHtmlEmbedded() : string
    {
        return $this->email->getMessageHtmlEmbedded();
    }

    public function addAttachment(array $attachment) : \Flexio\Services\Email
    {
        $this->email->addAttachment($attachment);
        return $this;
    }

    public function getAttachments() : array
    {
        return $this->email->getAttachments();
    }

    public function clearAttachments() : \Flexio\Services\Email
    {
        $this->email->clearAttachments();
        return $this;
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

            $from_addresses = $this->getFrom();
            $from = count($from_addresses) > 0 ? $from_addresses[0] : '';
            $mail->setFrom($from);

            $to_addresses = $this->getTo();
            foreach ($to_addresses as $a)
            {
                $mail->addAddress($a);     // add a recipient
            }

            $attachments = $this->getAttachments();
            foreach ($attachments as $a)
            {
                $mail->addAttachment($a['file'], $a['name'], 'base64', $a['mime_type']);
            }

            $mail->Subject = $this->getSubject();
            $mail->Body = $this->getMessageText();

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
