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
EXAMPLE:
// 'data' can be one of the following: "none" / "body" / "attachment"
// if 'body' the file will be appended to your body_text
// if 'none' the data file will be ignored and you'll just be sending an email
// if 'attachment' the data file will be attached to the email
// if 'link' the data file
{
    "op": "email",
    "params": {
        "to": "",
        "subject": "",
        "body_text": "",
        "body_html": "",
        "attachments": [
            {"file": "<path>", "name": "<name>", "mime_type": "<mime_type>"},
            {"file": "<path>"},
            "<path>" // alternative format; string
            ...
        ]
    }
}
*/

class Email extends \Flexio\Jobs\Base
{
    const EMAIL_WAIT_FREQUENCY = 1; // wait time between emails that are sent
    const EMAIL_TO_ADDRESS_MAX_SIZE = 25; // maximum number of users that an email can be sent to at once

    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // get the parameters
        $params = $this->getJobParameters();
        $to = $params['to'] ?? array();
        if (is_string($to))
            $to = explode(',', $to);

        $subject = isset($params['subject']) ? $params['subject'] : '';
        $body_text = isset($params['body']) ? $params['body'] : '';
        $body_html = isset($params['html']) ? $params['html'] : '';

        // enforce basic rate limits to prevent spam; only allow a max of 25 people to get
        // an email at once; also, only allow one email notice a second; of course multiple
        // jobs could be fired, but this at least helps throttle emails used within a loop
        if (count($to) > self::EMAIL_TO_ADDRESS_MAX_SIZE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::SIZE_LIMIT_EXCEEDED);
        sleep(self::EMAIL_WAIT_FREQUENCY);


        $email_params = array(
            'from' => "Flex.io <no-reply@flex.io>",
            'to' => $to,
            'subject' => $subject,
            'msg_text' => $body_text
        );

        if (strlen($body_html) > 0)
            $email_params['msg_html'] = $body_html;

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
            }

            $email_params['attachments'] = $attachments;
        }

        $email = \Flexio\Services\NoticeEmail::create($email_params);
        $res = $email->send();

        // delete the temporary attachments

        foreach ($attachments as $a)
        {
            $file = $a['file'];
            if (strlen($file) > 0)
                @unlink($file);
        }
    }

    private function convertToDiskFile(\Flexio\IFace\IProcess $process, array &$attachment)
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

    private function saveDataToFile(\Flexio\IFace\IStream $stream, string $filename)
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

    private function saveDataToCsv(\Flexio\IFace\IStream $stream, string $filename, int $maxrows = -1, int $maxbytes = -1)
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
