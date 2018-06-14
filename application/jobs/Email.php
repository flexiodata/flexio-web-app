<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-03-11
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "email",  // string, required
    "to": "",       // string|array, required
    "subject": "",  // string
    "body": "",     // string
    "html": "",     // string
    "attachments": [
        {
            "file": "<path>",           // string
            "name": "<name>",           // string
            "mime_type": "<mime_type>"  // string
        },
        {
            "file": "<path>"            // string
        },
        "<path>"                        // string
        ...
    ]
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['email'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Email extends \Flexio\Jobs\Base
{
    private const EMAIL_WAIT_FREQUENCY = 1; // wait time between emails that are sent
    private const EMAIL_TO_ADDRESS_MAX_SIZE = 25; // maximum number of users that an email can be sent to at once

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // get the parameters
        $params = $this->getJobParameters();
        $to = $params['to'] ?? array();
        if (is_string($to))
            $to = explode(',', $to);

        $from = ($params['from'] ?? '');
        $to = ($params['to'] ?? '');
        $connection = ($params['connection'] ?? null);
        $subject = ($params['subject'] ?? '');
        $body_text = ($params['body'] ?? ($params['body_text'] ?? ''));
        $body_html = ($params['html'] ?? ($params['body_html'] ?? ''));

        if ($connection === null)
        {
            // for now, we will temporarily allow messages to be sent with noreply@flex.io


            $email_params = array(
                'from' => "Flex.io <no-reply@flex.io>",
                'to' => $to,
                'subject' => $subject,
                'msg_text' => $body_text
            );

            $email = \Flexio\Services\NoticeEmail::create($email_params);

        }
         else
        {
            $connection = $process->getConnection($connection);

            $email = \Flexio\Services\Email::create($connection['connection_info'] ?? []);

            $from_addresses = $email->getFrom();
            if (count($from_addresses) == 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "'from' address must be specified");

            if (strlen($from) > 0)
            {
                $email->setFrom($from);
            }

            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessageText($body_text);
            $email->setMessageHtml($body_html);
        }



        $attachments = array();
        if (isset($params['attachments']))
        {
            if (!is_array($params['attachments']) || \Flexio\Base\Util::isAssociativeArray($params['attachments']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "'attachments' parameter must be an array");

            foreach ($params['attachments'] as $attachment)
            {
                if (is_string($attachment))
                {
                    $file = $attachment;
                    $name = null;
                    $mime_type = null;
                }
                else if (isset($attachment['file']))
                {
                    $file = $attachment['file'];
                    $name = $attachment['name'] ?? null;
                    $mime_type = $attachment['mime_type'] ?? null;
                }


                if ($name === null)
                {
                    $name = substr($file, strrpos($file, '/') + 1);
                }

                if ($mime_type === null)
                {
                    $mime_type = \Flexio\Base\ContentType::getMimeTypeFromExtension($name);
                }

                $attachments[] = [
                    'name' => $name,
                    'file' => $file,
                    'mime_type' => $mime_type
                ];
            }
        }

        if (count($attachments) > 0)
        {
            foreach ($attachments as &$attachment)
            {
                self::convertToDiskFile($process, $attachment);

                register_shutdown_function('unlink', $attachment['file']);

                $email->addAttachment($attachment);
            }
        }

        $email->send();
    }

    private function convertToDiskFile(\Flexio\IFace\IProcess $process, array &$attachment) : void
    {
        $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $fname = $storage_tmpbase . "emailattach-" . \Flexio\Base\Util::generateRandomString(20);

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        try
        {
            $stream = $vfs->open($attachment['file']);
            if (!$stream)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
            }


            $reader = $stream->getReader();
            $f = fopen($fname, "wb");
            while (($data = $reader->read(32768)) != false)
            {
                fwrite($f, $data);
            }
            fclose($f);

            $attachment['file'] = $fname;
        }
        catch (\Exception $e)
        {
            $f = fopen($fname, "wb");
            $vfs->read($attachment['file'], function($data) use (&$f) {
                fwrite($f, $data);
            });
            fclose($f);

            $attachment['file'] = $fname;
        }
    }

    private function saveDataToFile(\Flexio\IFace\IStream $stream, string $filename) : bool
    {
        $handle = fopen($filename, "wt");
        if (!$handle)
            return false;

        $streamreader = $stream->getReader();
        while (true)
        {
            $data = $streamreader->read();
            if ($data === false)
                break;

            fputs($handle, $data);
        }

        fclose($handle);
        return true;
    }

    private function saveDataToCsv(\Flexio\IFace\IStream $stream, string $filename, int $maxrows = -1, int $maxbytes = -1) : bool
    {
        $handle = fopen($filename, "wt");
        if (!$handle)
            return false;

        // write header row
        $row = $stream->getStructure()->getNames();
        fputcsv($handle, $row);

        $streamreader = $stream->getReader();
        while (true)
        {
            $data = $streamreader->readRow();
            if ($data === false)
                break;

            fputcsv($handle, array_values($data));
        }

        fclose($handle);
        return true;
    }
}
