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


// email processing involves parsing the email; to parse emails,
// the following library is used:  https://github.com/zbateson/MailMimeParser


declare(strict_types=1);
namespace Flexio\Services;


class NoticeEmail
{
    const EMAIL_ADDRESS_NO_REPLY = 'no-reply@flex.io';

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

    private $_aws = null;
    private $_ses = null;

    public static function isValid(string $email) : bool
    {
        // checks if an email address is valid
        return \Flexio\Base\Util::isValidEmail($email);
    }

    public static function create(array $params = null) : \Flexio\Services\NoticeEmail
    {
        $email = new self;
        $email->initialize();

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

    public static function parseText(string $content) : \Flexio\Services\NoticeEmail
    {
        // parse the string content
        $parser = new \ZBateson\MailMimeParser\MailMimeParser;
        $message = $parser->parse($content);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParsedMessage($message);

        return $email;
    }

    public static function parseFile(string $path) : \Flexio\Services\NoticeEmail
    {
        $email = new self;

        if (strlen($path) === 0)
            return $email;

        // open the file
        $handle = fopen($path, 'r');
        if ($handle === false)
            return $email;

        // parse the file contents
        $parser = new \ZBateson\MailMimeParser\MailMimeParser;
        $message = $parser->parse($handle);
        fclose($handle);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParsedMessage($message);

        return $email;
    }

    public static function parseResource($handle) : \Flexio\Services\NoticeEmail
    {
        // parse the stream
        $parser = new \ZBateson\MailMimeParser\MailMimeParser;
        $message = $parser->parse($handle);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParsedMessage($message);

        return $email;
    }

    // splits an address list like:  [  "First Last <first.last@email.com>" ] into
    //                               [ { "display" => "First Last", "email" => "first.last@email.com" } ]

    public static function splitAddressList(array $arr) : array
    {
        $ret = [];
        foreach ($arr as $a)
            $ret[] = self::splitAddress($a);
        return $ret;
    }

    public static function splitAddress(string $str) : array
    {
        $pos = strpos($str, '<');
        if ($pos === false)
            return array("display" => $str, "email" => $str);

        $ret = [];
        $ret['display'] = trim(substr($str, 0, $pos), " \t\n\r\0\x0B<>'\"");
        $ret['email'] = trim(substr($str, $pos+1), " \t\n\r\0\x0B<>'\"");
        return $ret;
    }

    public function send() : bool
    {
        if (count($this->attachments) > 0)
            return $this->sendWithAttachments();
             else
            return $this->sendWithoutAttachments();
    }

    public function setFrom($addresses) : \Flexio\Services\NoticeEmail // TODO: set parameter type
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

    public function setTo($addresses) : \Flexio\Services\NoticeEmail // TODO: set parameter type
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

    public function setCC($addresses) : \Flexio\Services\NoticeEmail // TODO: set parameter type
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

    public function setBCC($addresses) : \Flexio\Services\NoticeEmail // TODO: set parameter type
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

    public function setReplyTo($addresses) : \Flexio\Services\NoticeEmail // TODO: set parameter type
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

    private function initialize()
    {
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
    }

    private function initializeFromParsedMessage(\ZBateson\MailMimeParser\Message $message)
    {
        // start with a clean slate
        $this->initialize();

        // get the 'from' address headers
        $from_header = $message->getHeader('from');
        if ($from_header instanceof \ZBateson\MailMimeParser\Header\AddressHeader)
        {
            $from = array();
            $addresses = $from_header->getParts();
            foreach ($addresses as $a)
            {
                $display = $a->getName();
                $address = $a->getEmail();
                $from[] = "$display <$address>";
            }
            $this->setFrom($from);
        }

        // get the 'to' address headers
        $to_header = $message->getHeader('to');
        if ($to_header instanceof \ZBateson\MailMimeParser\Header\AddressHeader)
        {
            $to = array();
            $addresses = $to_header->getParts();
            foreach ($addresses as $a)
            {
                $display = $a->getName();
                $address = $a->getEmail();
                $to[] = "$display <$address>";
            }
            $this->setTo($to);
        }

        // get the "cc"
        $this->setCC(''); // TODO: populate

        // get the "bcc"
        $this->setBCC(''); // TODO: populate

        // get the "replyto"
        $this->setReplyTo(''); // TODO: populate

        // get the subject
        $subject = $message->getHeaderValue('subject') ?? '';
        $this->setSubject($subject);

        // get the body
        $txtcontent = $message->getTextContent() ?? '';
        $this->setMessageText($txtcontent);

        $htmlcontent = $message->getHtmlContent() ?? '';
        $this->setMessageHtml($htmlcontent);
        $this->setMessageHtmlEmbedded($htmlcontent);

        // get the attachments
        $attachments = $message->getAllAttachmentParts();
        $this->attachments = [];
        foreach ($attachments as $attachment)
        {
            $att = array(
                'mime_type' => $attachment->getHeaderValue('Content-Type', 'application/octet-stream'),
                'name' => $attachment->getHeaderParameter('Content-Type', 'name') ?? null,
                'file' => null,
                'content' => stream_get_contents($attachment->getContentResourceHandle())
            );

            $this->addAttachment($att);
        }
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
