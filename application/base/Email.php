<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
    private const EMAIL_ADDRESS_DELIMITER = ',';

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

    public function __construct()
    {
        $this->initialize();
    }

    public static function isValid(string $email) : bool
    {
        return \Flexio\Base\Util::isValidEmail($email);
    }

    public static function create(array $params = null) : \Flexio\Base\Email
    {
        $from = $params['from'] ?? array();
        $to = $params['to'] ?? array();
        $cc = $params['cc'] ?? array();
        $bcc = $params['bcc'] ?? array();
        $reply_to = $params['reply_to'] ?? array();
        $subject = $params['subject'] ?? '';
        $msg_text = $params['msg_text'] ?? '';
        $msg_html = $params['msg_html'] ?? '';
        $attachments = $params['attachments'] ?? array();

        // allow addresses to be specified as a delimited list
        if (is_string($from))
            $from = explode(self::EMAIL_ADDRESS_DELIMITER, $from);
        if (is_string($to))
            $to = explode(self::EMAIL_ADDRESS_DELIMITER, $to);
        if (is_string($cc))
            $cc = explode(self::EMAIL_ADDRESS_DELIMITER, $cc);
        if (is_string($bcc))
            $bcc = explode(self::EMAIL_ADDRESS_DELIMITER, $bcc);
        if (is_string($reply_to))
            $bcc = explode(self::EMAIL_ADDRESS_DELIMITER, $bcc);

        // set the parameters
        $email = new self;
        $email->setFrom($from);
        $email->setTo($to);
        $email->setCC($cc);
        $email->setBCC($bcc);
        $email->setReplyTo($reply_to);
        $email->setSubject($subject);
        $email->setMessageText($msg_text);
        $email->setMessageHtml($msg_html);
        foreach ($attachments as $a)
        {
            $email->addAttachment($a);
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

    public static function splitAddressList(array $arr) : array
    {
        // splits an address list like:  [  "First Last <first.last@email.com>" ] into
        //                               [ { "display" => "First Last", "email" => "first.last@email.com" } ]

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

    public function setFrom(array $addresses) : \Flexio\Base\Email
    {
        $this->from_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getFrom() : array
    {
        return $this->from_addresses;
    }

    public function setTo(array $addresses) : \Flexio\Base\Email
    {
        $this->to_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getTo() : array
    {
        return $this->to_addresses;
    }

    public function setCC(array $addresses) : \Flexio\Base\Email
    {
        $this->cc_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getCC() : array
    {
        return $this->cc_addresses;
    }

    public function setBCC(array $addresses) : \Flexio\Base\Email
    {
        $this->bcc_addresses = self::getCleanedAddresses($addresses);
        return $this;
    }

    public function getBCC() : array
    {
        return $this->bcc_addresses;
    }

    public function setReplyTo(array $addresses) : \Flexio\Base\Email
    {
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
        $cc_header = $message->getHeader('cc');
        if ($cc_header instanceof \ZBateson\MailMimeParser\Header\AddressHeader)
        {
            $cc = array();
            $addresses = $cc_header->getParts();
            foreach ($addresses as $a)
            {
                $display = $a->getName();
                $address = $a->getEmail();
                $cc[] = "$display <$address>";
            }
            $this->setCC($cc);
        }

        // get the "bcc"
        $bcc_header = $message->getHeader('bcc');
        if ($bcc_header instanceof \ZBateson\MailMimeParser\Header\AddressHeader)
        {
            $bcc = array();
            $addresses = $bcc_header->getParts();
            foreach ($addresses as $a)
            {
                $display = $a->getName();
                $address = $a->getEmail();
                $bcc[] = "$display <$address>";
            }
            $this->setBCC($bcc);
        }

        // get the "replyto"
        $replyto_header = $message->getHeader('reply-to');
        if ($replyto_header instanceof \ZBateson\MailMimeParser\Header\AddressHeader)
        {
            $replyto = array();
            $addresses = $replyto_header->getParts();
            foreach ($addresses as $a)
            {
                $display = $a->getName();
                $address = $a->getEmail();
                $replyto[] = "$display <$address>";
            }
            $this->setReplyTo($replyto);
        }

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
