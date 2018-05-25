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

    private $from_addresses = array();
    private $to_addresses = array();
    private $cc_addresses = array();
    private $bcc_addresses = array();
    private $replyto_addresses = array();
    private $subject = '';
    private $msg_text = '';
    private $msg_html = '';
    private $msg_htmlembedded = '';
    private $attachments = array();

    private $_ses = null;

    public static function create(array $params = null) : \Flexio\Services\NoticeEmail
    {
        $email = new self;

        if (!isset($params))
            return $email;

        if (isset($params['from']))
            $email->setFrom($params['from']);
        if (isset($params['to']))
            $email->setTo($params['to']);
        if (isset($params['cc']))
            $email->setCC($params['cc']);
        if (isset($params['bcc']))
            $email->setBCC($params['bcc']);
        if (isset($params['reply_to']))
            $email->setReplyTo($params['reply_to']);
        if (isset($params['subject']))
            $email->setSubject($params['subject']);
        if (isset($params['msg_text']))
            $email->setMessageText($params['msg_text']);
        if (isset($params['msg_html']))
            $email->setMessageHtml($params['msg_html']);

        if (isset($params['attachments']) && is_array($params['attachments']))
        {
            $attachments = $params['attachments'];
            foreach ($attachments as $a)
            {
                $email->addAttachment($a);
            }
        }

        return $email;
    }

    public function send() : bool
    {
        if (count($this->attachments) > 0)
            return $this->sendWithAttachments();
             else
            return $this->sendWithoutAttachments();
    }

    public function setFrom($addresses) : \Flexio\Services\NoticeEmail // TODO: add parameter type
    {
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

    public function setTo($addresses) : \Flexio\Services\NoticeEmail // TODO: add parameter type
    {
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

    public function setCC($addresses) : \Flexio\Services\NoticeEmail // TODO: add parameter type
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

    public function setBCC($addresses) : \Flexio\Services\NoticeEmail // TODO: add parameter type
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

    public function setReplyTo($addresses) : \Flexio\Services\NoticeEmail // TODO: add parameter type
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

    public function setSubject(string $subject) : \Flexio\Services\NoticeEmail
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function setMessageText(string $message) : \Flexio\Services\NoticeEmail
    {
	    $this->msg_text = $message;
        return $this;
    }

    public function getMessageText() : string
    {
        return $this->msg_text;
    }

    public function setMessageHtml(string $message) : \Flexio\Services\NoticeEmail
    {
        $this->msg_html = $message;
        return $this;
    }

    public function getMessageHtml() : string
    {
        return $this->msg_html;
    }

    public function setMessageHtmlEmbedded(string $message) : \Flexio\Services\NoticeEmail
    {
        $this->msg_htmlembedded = $message;
        return $this;
    }

    public function getMessageHtmlEmbedded() : string
    {
        return $this->msg_htmlembedded;
    }

    public function addAttachment(array $attachment) : \Flexio\Services\NoticeEmail
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

    public function clearAttachments() : \Flexio\Services\NoticeEmail
    {
        $this->attachments = array();
        return $this;
    }

    private function sendWithoutAttachments() : bool
    {
        $ses = $this->getSes();
        if (!$ses)
            return false;

        // make sure we have at least one valid from and to address
        if (count($this->from_addresses) === 0)
            return false;
        if (count($this->to_addresses) === 0)
            return false;

        // if we don't have a reply-to address, then supply it
        if (count($this->replyto_addresses) === 0)
            $this->setReplyTo(self::EMAIL_ADDRESS_NO_REPLY);

        // build up the destination array
        $destination = array();
        $destination['ToAddresses'] = $this->to_addresses;
        if (count($this->cc_addresses) > 0)
            $destination['CcAddresses'] = $this->cc_addresses;
        if (count($this->bcc_addresses) > 0)
            $destination['BccAddresses'] = $this->bcc_addresses;

        // build up the message array
        $message = array(
            'Subject' => array(
                'Data' => $this->subject,
            ),
            'Body' => array(
                'Text' => array(
                    'Data' => $this->msg_text,
                )
            ),
        );

        if (strlen($this->msg_html) > 0)
            $message['Body']['Html'] = array('Data' => $this->msg_html);

        // create email array
        $mail = array(
            'Source' => implode(',', $this->from_addresses),
            'Destination' => $destination,
            'Message' => $message,
            'ReplyToAddresses' => $this->replyto_addresses
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
        $mail_mime->setTxtBody($this->msg_text);
        $mail_mime->setHTMLBody($this->msg_html);
        foreach ($this->to_addresses as $to)
        {
            $mail_mime->addTo($to);
        }
        foreach ($this->cc_addresses as $cc)
        {
            $mail_mime->addCc($cc);
        }
        foreach ($this->bcc_addresses as $bcc)
        {
            $mail_mime->addBcc($bcc);
        }
        foreach ($this->attachments as $attachment)
        {
            $mail_mime->addAttachment($attachment['file'], $attachment['mime_type'], $attachment['name']);
        }
        $body = $mail_mime->get();
        $headers = $mail_mime->txtHeaders(array('Subject' => $this->subject));
        $message = $headers . "\r\n" . $body;

        // restore original error settings
        error_reporting($old_error_settings);

        // create email array

        $destination = array();
        $destination['ToAddresses'] = implode(',',$this->to_addresses);
        if (count($this->cc_addresses) > 0)
            $destination['CcAddresses'] = implode(',',$this->cc_addresses);
        if (count($this->bcc_addresses) > 0)
            $destination['BccAddresses'] = implode(',',$this->bcc_addresses);

        $mail = array(
            'Source' => implode(',',$this->from_addresses),
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
