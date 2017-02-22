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
// pecl mailparse is used:  https://pecl.php.net/package/mailparse
// and therefore needs to be installed; however, because of a compile
// directive problem involving the following lines in mailparse.c
//     #if !HAVE_MBSTRING
//     #error The mailparse extension requires the mbstring extension!
// where the error is flagged even though mbstring is installed,
// the library needs to be installed manually with these two lines commented
// out; to install the library manually, do the following:
// 1.   cd /tmp
// 2.   pecl download mailparse
// 3.   tar xvzf mailparse-3.0.1.tgz
// 4.   cd mailparse-3.0.1
// 5.   phpize
// 6.   ./configure
// 7.   <
//      edit mailparse.c and comment out the following three lines
//          #if !HAVE_MBSTRING
//          #error The mailparse extension requires the mbstring extension!
//          #endif
//      so that we have
//          // #if !HAVE_MBSTRING
//          // #error The mailparse extension requires the mbstring extension!
//          // #endif
//      >
// 8.   make
// 9.   make install
// 10.  <
//      add the following to php.ini and restart the web server
//      extension=mailparse.so
//      >


// TODO: here's a standalone library:  https://github.com/zbateson/MailMimeParser
// replace the current implementation?


namespace Flexio\Services;


// library for parsing emails
if (!isset($GLOBALS['phpmimemailparser_included']))
{
    $GLOBALS['phpmimemailparser_included'] = true;
    set_include_path(get_include_path() . PATH_SEPARATOR . (dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpmimemailparser'));
}
require_once 'phpmimemailparser_init.php';

// library for building up mime email
if (!isset($GLOBALS['phpmailmime_included']))
{
    $GLOBALS['phpmailmime_included'] = true;
    set_include_path(get_include_path() . PATH_SEPARATOR . (\Flexio\System\System::getBaseDirectory() . '/library/phpmailmime'));
}


class Email
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

    public static function isValid($email)
    {
        // checks if an email address is valid
        return \Flexio\System\Util::isValidEmail($email);
    }

    public static function create($params = false)
    {
        $email = new self;
        $email->initialize();

        if ($params === false)
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

    public static function parseText($content)
    {
        if (!is_string($content))
            return false;

        // parse the content
        $parser = new \PhpMimeMailParser\Parser;
        $parser->setText($content);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParser($parser);

        return $email;
    }

    public static function parseStream($stream)
    {
        if (!is_string($stream))
            return false;
        if (strlen($stream) === 0)
            return false;

        // get the stream
        $stream_handle = fopen($stream, 'r');
        if ($stream_handle === false)
            return false;

        // parse the stream
        $parser = new \PhpMimeMailParser\Parser;
        $parser->setStream($stream_handle);

        // create the email with the parsed values
        $email = new self;
        $email->initializeFromParser($parser);

        return $email;
    }

    public function send()
    {
        if (count($this->attachments) > 0)
            $this->sendWithAttachments();
             else
            $this->sendWithoutAttachments();
    }

    public function setFrom($addresses)
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

    public function getFrom()
    {
        return $this->from_addresses;
    }

    public function setTo($addresses)
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

    public function getTo()
    {
        return $this->to_addresses;
    }

    public function setCC($addresses)
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

    public function getCC()
    {
        return $this->cc_addresses;
    }

    public function setBCC($addresses)
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

    public function getBCC()
    {
        return $this->bcc_addresses;
    }

    public function setReplyTo($addresses)
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

    public function getReplyTo()
    {
        return $this->replyto_addresses;
    }

    public function setSubject($subject)
    {
        if (!is_string($subject))
            return $this;

        $this->subject = $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setMessageText($message)
    {
        if (!is_string($message))
            return $this;

	    $this->msg_text = $message;
        return $this;
    }

    public function getMessageText()
    {
        return $this->msg_text;
    }

    public function setMessageHtml($message)
    {
        if (!is_string($message))
            return $this;

        $this->msg_html = $message;
        return $this;
    }

    public function getMessageHtml()
    {
        return $this->msg_html;
    }

    public function setMessageHtmlEmbedded($message)
    {
        if (!is_string($message))
            return $this;

        $this->msg_htmlembedded = $message;
        return $this;
    }

    public function getMessageHtmlEmbedded()
    {
        return $this->msg_htmlembedded;
    }

    public function addAttachment($attachment)
    {
        if (!is_array($attachment))
            return $this;

        if ($this->attachments === false)
            $this->attachments = array();

        $a = array();
        $a['name'] = isset_or($attachment['name'],'');
        $a['file'] = isset_or($attachment['file'],'');
        $a['mime_type'] = isset_or($attachment['mime_type'], \Flexio\System\ContentType::MIME_TYPE_NONE);
        $a['content'] = isset_or($attachment['content'],'');

        $this->attachments[] = $a;
        return $this;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function clearAttachments()
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

    private function initializeFromParser($parser)
    {
        // start with a clean slate
        $this->initialize();

        // get the "from" values
        $from = array();
        $from_array = $parser->getAddresses('from');
        foreach ($from_array as $item)
        {
            $display = $item['display'];
            $address = $item['address'];
            $is_group = $item['is_group'];
            $from[] = "$display <$address>";
        }
        $this->setFrom($from);

        // get the "to"
        $to = array();
        $to_array = $parser->getAddresses('to');
        foreach ($to_array as $item)
        {
            $display = $item['display'];
            $address = $item['address'];
            $is_group = $item['is_group'];
            $to[] = "$display <$address>";
        }
        $this->setTo($to);

        // get the "cc"
        $this->setCC(''); // TODO: populate

        // get the "bcc"
        $this->setBCC(''); // TODO: populate

        // get the "replyto"
        $this->setReplyTo(''); // TODO: populate

        // get the subject
        $this->setSubject($parser->getHeader('subject'));

        // get the body
        $this->setMessageText($parser->getMessageBody('text'));
        $this->setMessageHtml($parser->getMessageBody('html'));
        $this->setMessageHtmlEmbedded($parser->getMessageBody('htmlEmbedded'));

        // get the attachments; note: the $attachment->getContent() call reads
        // the file into memory, which may cause problems with large attachments;
        // the parser library allows a streaming option, so this potential issue
        // is in the implementation below
        $attachments = $parser->getAttachments();
        if (count($attachments) > 0)
        {
            foreach ($attachments as $attachment)
            {
                $a = array();
                $a['name'] = $attachment->getFilename();
                $a['mime_type'] = $attachment->getContentType();
                $a['content'] = $attachment->getContent();
                $this->attachments[] = $a;
            }
        }
    }

    private function sendWithoutAttachments()
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
        if (count($this->cc_addresses) >= 0)
            $destination['CcAddresses'] = $this->cc_addresses;
        if (count($this->bcc_addresses) >= 0)
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

    private function sendWithAttachments()
    {
        $ses = $this->getSes();
        if (!$ses)
            return false;

        // prevent "Non-static method PEAR::isError()" warning
        $old_error_settings = error_reporting(E_ALL ^ E_STRICT);

        include_once 'Mail/mime.php';

        $mail_mime = new \Mail_mime(array('eol' => "\n"));
        $mail_mime->setTxtBody($this->msg_text);
        $mail_mime->setHTMLBody($this->msg_html);
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
        $mail = array(
            'Source' => implode(',',$this->from_addresses),
            'Destinations' => $this->to_addresses,
            'RawMessage' => array(
                                'Data' => $message
                                )
        );

        try
        {
            $response = $ses->sendRawEmail($mail);

        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private function getSes()
    {
        setAutoloaderIgnoreErrors(true);
        require_once dirname(dirname(__DIR__)) . '/library/aws/aws.phar';
        global $g_config;

        if (null === $this->_ses)
        {
            $credentials = new Aws\Credentials\Credentials($g_config->ses_access_key, $g_config->ses_secret_key);

            $this->_ses = new Aws\Ses\SesClient([
                'version'     => 'latest',
                'region'      => 'us-east-1',
                'credentials' => $credentials
            ]);
        }

        return $this->_ses;
    }


/*
    private function getSes()
    {
        if (null === $this->_ses)
        {
            $aws = $this->getAWS();
            if ($aws)
                $this->_ses = $aws->get('ses');
        }

        return $this->_ses;
    }

    private function getAWS()
    {
        setAutoloaderIgnoreErrors(true);

        require_once dirname(dirname(__DIR__)) . '/library/aws/aws.phar';

        global $g_config;

        if (null === $this->_aws)
        {
            if (!isset($g_config->ses_access_key) || !isset($g_config->ses_secret_key))
                return null;

            $this->_aws = Aws\Common\Aws::factory(array(
               'key' => $g_config->ses_access_key,
               'secret' => $g_config->ses_secret_key,
               'region' => 'us-east-1'
            ));
        }

        return $this->_aws;
    }
*/

    private static function getCleanedAddresses($addresses)
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
