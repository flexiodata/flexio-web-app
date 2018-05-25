<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-25
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


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

    public static function isValid(string $email) : bool
    {
        return \Flexio\Base\Util::isValidEmail($email);
    }

    public static function create(array $params = null) : \Flexio\Base\Email
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

    public static function parseText(string $content) : \Flexio\Base\Email
    {
        // parse the string content
        $parser = new \ZBateson\MailMimeParser\MailMimeParser;
        $message = $parser->parse($content);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParsedMessage($message);

        return $email;
    }

    public static function parseFile(string $path) : \Flexio\Base\Email
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

    public static function parseResource($handle) : \Flexio\Base\Email
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

    public function setFrom($addresses) : \Flexio\Base\Email // TODO: add parameter type
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

    public function setTo($addresses) : \Flexio\Base\Email // TODO: add parameter type
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

    public function setCC($addresses) : \Flexio\Base\Email // TODO: add parameter type
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

    public function setBCC($addresses) : \Flexio\Base\Email // TODO: add parameter type
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

    public function setReplyTo($addresses) : \Flexio\Base\Email // TODO: add parameter type
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

    public function setSubject(string $subject) : \Flexio\Base\Email
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function setMessageText(string $message) : \Flexio\Base\Email
    {
	    $this->msg_text = $message;
        return $this;
    }

    public function getMessageText() : string
    {
        return $this->msg_text;
    }

    public function setMessageHtml(string $message) : \Flexio\Base\Email
    {
        $this->msg_html = $message;
        return $this;
    }

    public function getMessageHtml() : string
    {
        return $this->msg_html;
    }

    public function setMessageHtmlEmbedded(string $message) : \Flexio\Base\Email
    {
        $this->msg_htmlembedded = $message;
        return $this;
    }

    public function getMessageHtmlEmbedded() : string
    {
        return $this->msg_htmlembedded;
    }

    public function addAttachment(array $attachment) : \Flexio\Base\Email
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

    public function clearAttachments() : \Flexio\Base\Email
    {
        $this->attachments = array();
        return $this;
    }

    private function initialize() : void
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

    private function initializeFromParsedMessage(\ZBateson\MailMimeParser\Message $message) : void
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
