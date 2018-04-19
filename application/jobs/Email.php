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
        "data": ""
    }
}
*/

class Email extends \Flexio\Jobs\Base
{
    const DATA_MODE_NONE       = 'none';
    const DATA_MODE_BODY       = 'body';
    const DATA_MODE_ATTACHMENT = 'attachment';
    const EMAIL_WAIT_FREQUENCY = 1; // wait time between emails that are sent
    const EMAIL_TO_ADDRESS_MAX_SIZE = 25; // maximum number of users that an email can be sent to at once

    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $this->sendEmail($process);
    }

    private function sendEmail(\Flexio\IFace\IProcess $process)
    {
        // get the parameters
        $job_definition = $this->getProperties();
        $to = $job_definition['params']['to'] ?? array();
        if (is_string($to))
            $to = explode(',', $to);

        $subject = isset($job_definition['params']['subject']) ? $job_definition['params']['subject'] : '';
        $body_text = isset($job_definition['params']['body_text']) ? $job_definition['params']['body_text'] : '';
        $body_html = isset($job_definition['params']['body_html']) ? $job_definition['params']['body_html'] : '';

        // enforce basic rate limits to prevent spam; only allow a max of 25 people to get
        // an email at once; also, only allow one email notice a second; of course multiple
        // jobs could be fired, but this at least helps throttle emails used within a loop
        if (count($to) > self::EMAIL_TO_ADDRESS_MAX_SIZE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::SIZE_LIMIT_EXCEEDED);
        sleep(self::EMAIL_WAIT_FREQUENCY);

        // build the email
        $instream_list = array($process->getStdin());
        $attachments = self::getAttachmentsFromInput($instream_list);

        $email_params = array(
            'from' => "Flex.io <no-reply@flex.io>",
            'to' => $to,
            'subject' => $subject,
            'msg_text' => $body_text
        );

        if (strlen($body_html) > 0)
            $email_params['msg_html'] = $body_html;

        $data_mode = isset($job_definition['params']['data']) ? $job_definition['params']['data'] : self::DATA_MODE_ATTACHMENT;

        $attachments = array();

        if (isset($job_definition['params']['attachments']))
        {
            foreach ($job_definition['params']['attachments'] as $attachment)
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

        /*
        foreach ($attachments as $a)
        {
            if ($data_mode === self::DATA_MODE_ATTACHMENT)
            {
                $email_params['attachments'][] = array(
                    'name' => $a['name'],
                    'file' => $a['file'],
                    'mime_type' => $a['mime_type']
                );
            }

            if ($data_mode === self::DATA_MODE_BODY)
            {
                // add blank lines if there's already some body text
                if (strlen($body_text) > 0)
                    $body_text .= "\n\n";

                $body_txt .= $a['name'];
                $body_txt .= '------------------------------';
                $body_text .= file_get_contents($a['file']);
            }

            if ($data_mode === self::DATA_MODE_NONE)
            {
                // nothing
            }

            // everything else; equivalent for now to nothing
        }
        */

        $email = \Flexio\Services\Email::create($email_params);
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


    private function getAttachmentsFromInput(array $instream_list)
    {
        $attachments = array();

        foreach ($instream_list as $instream)
        {
            $mime_type = $instream->getMimeType();
            $name = $instream->getName();

            if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
            {
                $extension_to_add = 'csv';
                $filename = \Flexio\Base\File::getFilename($name);
                $name = "$filename.$extension_to_add";

                $mime_type = \Flexio\Base\ContentType::CSV;
                $attachment_file = \Flexio\Base\File::getTempFilename($extension_to_add);
                if (!$this->saveDataToCsv($instream, $attachment_file, -1, 20000000))
                    continue; // TODO: fail?
            }
             else
            {
                $extension = \Flexio\Base\File::getFileExtension($name);
                $attachment_file = \Flexio\Base\File::getTempFilename($extension);
                if (!$this->saveDataToFile($instream, $attachment_file))
                    continue; // TODO: fail?
            }

            if (strlen($name) === 0)
                $name = $attachment_file;

            $attachments[] = array(
                'name' => $name,
                'mime_type' => $mime_type,
                'file' => $attachment_file
            );
        }

        return $attachments;
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
