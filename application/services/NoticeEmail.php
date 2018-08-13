<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; Aaron L. Williams
 * Created:  2013-06-12
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class NoticeEmail
{
    private const EMAIL_ADDRESS_NO_REPLY = 'no-reply@flex.io';

    private $email; // email properties (e.g. from, to, subject, etc)
    private $_ses;

    public function __construct()
    {
        $this->email = \Flexio\Base\Email::create();
        $this->_ses = null;
    }

    public static function create(array $params = null) : \Flexio\Services\NoticeEmail
    {
        $service = new self;
        $service->email = \Flexio\Base\Email::create($params);
        return $service;
    }

    public function send() : bool
    {
        $attachments = $this->getAttachments();

        if (count($attachments) > 0)
            return $this->sendWithAttachments();
             else
            return $this->sendWithoutAttachments();
    }

    public function setFrom(array $addresses) : \Flexio\Services\NoticeEmail
    {
        $this->email->setFrom($addresses);
        return $this;
    }

    public function getFrom() : array
    {
        return $this->email->getFrom();
    }

    public function setTo(array $addresses) : \Flexio\Services\NoticeEmail
    {
        $this->email->setTo($addresses);
        return $this;
    }

    public function getTo() : array
    {
        return $this->email->getTo();
    }

    public function setCC(array $addresses) : \Flexio\Services\NoticeEmail
    {
        $this->email->setCC($addresses);
        return $this;
    }

    public function getCC() : array
    {
        return $this->email->getCC();
    }

    public function setBCC(array $addresses) : \Flexio\Services\NoticeEmail
    {
        $this->email->setBCC($addresses);
        return $this;
    }

    public function getBCC() : array
    {
        return $this->email->getBCC();
    }

    public function setReplyTo(array $addresses) : \Flexio\Services\NoticeEmail
    {
        $this->email->setReplyTo($addresses);
        return $this;
    }

    public function getReplyTo() : array
    {
        return $this->email->getReplyTo();
    }

    public function setSubject(string $subject) : \Flexio\Services\NoticeEmail
    {
        $this->email->setSubject($subject);
        return $this;
    }

    public function getSubject() : string
    {
        return $this->email->getSubject();
    }

    public function setMessageText(string $message) : \Flexio\Services\NoticeEmail
    {
        $this->email->setMessageText($message);
        return $this;
    }

    public function getMessageText() : string
    {
        return $this->email->getMessageText();
    }

    public function setMessageHtml(string $message) : \Flexio\Services\NoticeEmail
    {
        $this->email->setMessageHtml($message);
        return $this;
    }

    public function getMessageHtml() : string
    {
        return $this->email->getMessageHtml();
    }

    public function setMessageHtmlEmbedded(string $message) : \Flexio\Services\NoticeEmail
    {
        $this->email->setMessageHtmlEmbedded($message);
        return $this;
    }

    public function getMessageHtmlEmbedded() : string
    {
        return $this->email->getMessageHtmlEmbedded();
    }

    public function addAttachment(array $attachment) : \Flexio\Services\NoticeEmail
    {
        $this->email->addAttachment($attachment);
        return $this;
    }

    public function getAttachments() : array
    {
        return $this->email->getAttachments();
    }

    public function clearAttachments() : \Flexio\Services\NoticeEmail
    {
        $this->email->clearAttachments();
        return $this;
    }

    private function sendWithoutAttachments() : bool
    {
        $ses = $this->getSes();
        if (!$ses)
            return false;

        // make sure we have at least one valid from and to address
        $from_addresses = $this->getFrom();
        if (count($from_addresses) === 0)
            return false;

        $to_addresses = $this->getTo();
        if (count($to_addresses) === 0)
            return false;

        // if we don't have a reply-to address, then supply it
        $replyto_addresses - $this->getReplyTo();
        if (count($replyto_addresses) === 0)
            $this->setReplyTo([self::EMAIL_ADDRESS_NO_REPLY]);

        // build up the destination array
        $destination = array();
        $destination['ToAddresses'] = $this->getTo();

        $cc_addresses = $this->getCC();
        if (count($cc_addresses) > 0)
            $destination['CcAddresses'] = $cc_addresses;

        $bcc_addresses = $this->getBCC();
        if (count($bcc_addresses) > 0)
            $destination['BccAddresses'] = $bcc_addresses;

        // build up the message array
        $message = array(
            'Subject' => array(
                'Data' => $this->getSubject(),
            ),
            'Body' => array(
                'Text' => array(
                    'Data' => $this->getMessageText(),
                )
            ),
        );

        $msg_html = $this->getMessageHtml();
        if (strlen($msg_html) > 0)
            $message['Body']['Html'] = array('Data' => $msg_html);

        // create email array
        $mail = array(
            'Source' => implode(',', $this->getFrom()),
            'Destination' => $destination,
            'Message' => $message,
            'ReplyToAddresses' => implode(',', $this->getReplyTo())
            //'ReturnPath' => array()
        );

        try
        {
            $response = $ses->sendEmail($mail);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    private function sendWithAttachments() : bool
    {
        $ses = $this->getSes();
        if (!$ses)
            return false;

        // prevent "Non-static method PEAR::isError()" warning
        $old_error_settings = error_reporting(E_ALL ^ E_STRICT);

        $mail_mime = new \Mail_mime(array('eol' => "\n"));
        $mail_mime->setTxtBody($this->getMessageText());
        $mail_mime->setHTMLBody($this->getMessageHtml());

        $to_addresses = $this->getTo();
        foreach ($to_addresses as $to)
        {
            $mail_mime->addTo($to);
        }

        $cc_addresses = $this->getCC();
        foreach ($cc_addresses as $cc)
        {
            $mail_mime->addCc($cc);
        }

        $bcc_addresses = $this->getBCC();
        foreach ($bcc_addresses as $bcc)
        {
            $mail_mime->addBcc($bcc);
        }

        $attachments = $this->getAttachments();
        foreach ($attachments as $a)
        {
            $mail_mime->addAttachment($a['file'], $a['mime_type'], $a['name']);
        }

        $body = $mail_mime->get();
        $headers = $mail_mime->txtHeaders(array('Subject' => $this->getSubject()));
        $message = $headers . "\r\n" . $body;

        // restore original error settings
        error_reporting($old_error_settings);

        // create email array

        $destination = array();
        $destination['ToAddresses'] = implode(',', $this->getTo());

        $cc_addresses = $this->getCC();
        if (count($cc_addresses) > 0)
            $destination['CcAddresses'] = implode(',', $cc_addresses);

        $bcc_addresses = $this->getBCC();
        if (count($bcc_addresses) > 0)
            $destination['BccAddresses'] = implode(',', $bcc_addresses);

        $mail = array(
            'Source' => implode(',', $this->getFrom()),
            'Destinations' => $destination,
            'RawMessage' => array(
                                'Data' => $message
                                )
        );

        try
        {
            $response = @$ses->sendRawEmail($mail);
        }
        catch (\Exception $e)
        {
            //die($e->getMessage());
            return false;
        }

        return true;
    }

    private function getSes() : \Aws\Ses\SesClient
    {
        global $g_config;

        if (null === $this->_ses)
        {
            $credentials = new \Aws\Credentials\Credentials($g_config->ses_access_key, $g_config->ses_secret_key);

            $this->_ses = new \Aws\Ses\SesClient([
                'version'     => 'latest',
                'region'      => 'us-east-1',
                'credentials' => $credentials
            ]);
        }

        return $this->_ses;
    }
}
