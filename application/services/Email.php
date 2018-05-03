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


// email processing involves parsing the email; to parse emails,
// the following library is used:  https://github.com/zbateson/MailMimeParser


declare(strict_types=1);
namespace Flexio\Services;


class Email
{
<<<<<<< HEAD
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


    private $protocol;
    private $host;
    private $port;
    private $username;
    private $password;




    public static function isValid(string $email) : bool
    {
        // checks if an email address is valid
        return \Flexio\Base\Util::isValidEmail($email);
=======
    "op": "email",
    "params": {
        "to": "",
        "subject": "",
        "body": "",
        "html": "",
        "attachments": [
            {"file": "<path>", "name": "<name>", "mime_type": "<mime_type>"},
            {"file": "<path>"},
            "<path>" // alternative format; string
            ...
        ]
>>>>>>> d191d710f56d5ba1247d3b74cb2bb5ddb43e59bb
    }

    public static function create(array $params = null) : \Flexio\Services\Email
    {
        if (!isset($params))
            return new self;

        $service = new self;
        $service->initialize($params);
        return $service;
    }

    public static function parseText(string $content) : \Flexio\Services\Email
    {
        // parse the string content
        $parser = new \ZBateson\MailMimeParser\MailMimeParser;
        $message = $parser->parse($content);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParsedMessage($message);

        return $email;
    }

    public static function parseFile(string $path) : \Flexio\Services\Email
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

    public static function parseResource($handle) : \Flexio\Services\Email
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


    public function setFrom($addresses) : \Flexio\Services\Email // TODO: set parameter type
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

    public function setTo($addresses) : \Flexio\Services\Email // TODO: set parameter type
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

    public function setCC($addresses) : \Flexio\Services\Email // TODO: set parameter type
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

    public function setBCC($addresses) : \Flexio\Services\Email // TODO: set parameter type
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

    public function setReplyTo($addresses) : \Flexio\Services\Email // TODO: set parameter type
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

    private function initialize($params = [])
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

        $this->setFrom(($params['from'] ?? ''));
        $this->protocol = ($params['protocol'] ?? '');              // none, smtp
        $this->security = ($params['security'] ?? '');              // none, ssl, tls
        $this->authentication = ($params['authentication'] ?? '');  // none, password, encpassword, kerberos, ntlm, oauth2
        $this->host = ($params['host'] ?? '');
        $this->username = ($params['username'] ?? '');
        $this->password = ($params['password'] ?? '');
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

    public function send() : bool
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true); // passing 'true' enables exceptions

        try
        {
            $port = $this->port;
            if (!$port)
            {
                if ($this->security == 'tls')
                    $this->port = 587;
                else if ($this->security == 'ssl')
                    $this->port = 465;
                else
                    $this->port = 25;
            }

            $mail->SMTPDebug = 2;              // enable verbose debug output
            $mail->isSMTP();                     // set mailer to use SMTP
            $mail->Host = $this->host;           // specify main and backup SMTP servers
            $mail->SMTPAuth = strlen($this->username) > 0 ? true:false; // Enable SMTP authentication
            $mail->Username = $this->username;    // SMTP username
            $mail->Password = $this->password;    // SMTP password
            $mail->SMTPSecure = $this->security;  // '', 'ssl', or 'tls'
            $mail->Port = $port;
        
            $from = count($this->from_addresses) > 0 ? $this->from_addresses[0] : '';
            $mail->setFrom($from);

            foreach ($this->to_addresses as $email)
            {
                $mail->addAddress($email);     // add a recipient
            }
        
            $mail->Subject = $this->subject;
            $mail->Body = $this->msg_text;
        
            $mail->send();
        }
        catch (Exception $e)
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
