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


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class EmailSendJob extends \Flexio\Jobs\Base
{
    const DATA_MODE_NONE       = 'none';
    const DATA_MODE_BODY       = 'body';
    const DATA_MODE_ATTACHMENT = 'attachment';


    public function run()
    {
        // pass on the streams so that they can be further handled
        $this->getOutput()->merge($this->getInput());

        // note: current behavior is to only allow outputs (including email)
        // in runtime; so, if we're not in runtime, we're done
        if ($this->isRunMode() === false)
            return;

        // send the email
        $this->sendEmail();
    }

    private function sendEmail()
    {
        // get the attachments
        $instream_list = $this->getInput()->enum();
        $attachments = self::getAttachmentsFromInput($instream_list);

        // build the email
        $job_definition = $this->getProperties();
        $to = $job_definition['params']['to'];
        $to = implode(',', $to);
        $subject = isset($job_definition['params']['subject']) ? $job_definition['params']['subject'] : '';
        $body_text = isset($job_definition['params']['body_text']) ? $job_definition['params']['body_text'] : '';
        $body_html = isset($job_definition['params']['body_html']) ? $job_definition['params']['body_html'] : '';

        $email_params = array(
            'from' => "Flex.io <no-reply@flex.io>",
            'to' => $to,
            'subject' => $subject,
            'msg_text' => $body_text
        );

        if (strlen($body_html) > 0)
            $email_params['msg_html'] = $body_html;

        $data_mode = isset($job_definition['params']['data']) ? $job_definition['params']['data'] : self::DATA_MODE_ATTACHMENT;

        $email_params['attachments'] = array();
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

        $email = \Email::create($email_params);
        $res = $email->send();

        // delete the temporary attachments

        foreach ($attachments as $a)
        {
            $file = $a['file'];
            if (strlen($file) > 0)
                @unlink($file);
        }
    }

    private function getAttachmentsFromInput($instream_list)
    {
        $attachments = array();

        foreach ($instream_list as $instream)
        {
            $mime_type = $instream->getMimeType();
            $name = $instream->getName();

            if ($mime_type === \ContentType::MIME_TYPE_FLEXIO_TABLE)
            {
                $extension_to_add = 'csv';
                $filename = \Util::getFilename($name);
                $name = "$filename.$extension_to_add";

                $mime_type = \ContentType::MIME_TYPE_CSV;
                $attachment_file = \Util::getTempFilename($extension_to_add);
                if (!$this->saveDataToCsv($instream, $attachment_file, -1, 20000000))
                    continue; // TODO: fail?
            }
             else
            {
                $extension = \Util::getFileExtension($name);
                $attachment_file = \Util::getTempFilename($extension);
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

    private function saveDataToFile($stream, $filename)
    {
        $handle = fopen($filename, "wt");
        if (!$handle)
            return false;

        $stream->read(function ($data) use ($handle) {
            fputs($handle, $data);
        });

        fclose($handle);
        return true;
    }

    private function saveDataToCsv($stream, $filename, $maxrows = -1, $maxbytes = -1)
    {
        $handle = fopen($filename, "wt");
        if (!$handle)
            return false;

        // write header row
        $row = $stream->getStructure()->getNames();
        fputcsv($handle, $row);

        // write out the content
        $stream->read(function ($data) use ($handle) {
            fputcsv($handle, array_values($data));
        });

        fclose($handle);
        return true;
    }


    // job definition info
    const MIME_TYPE = 'flexio.email';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.email",
        "params": {
            "to": "",
            "subject": "",
            "body_text": "",
            "body_html": "",
            "data": ""
        }
    }
EOD;
    // 'data' can be one of the following: "none" / "body" / "attachment"
    // if 'body' the file will be appended to your body_text
    // if 'none' the data file will be ignored and you'll just be sending an email
    // if 'attachment' the data file will be attached to the email
    // if 'link' the data file
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.email"]
            },
            "params": {
                "type": "object",
                "required": ["to"],
                "properties": {
                    "to" : {
                        "type": "array"
                    },
                    "subject" : {
                        "type": "string"
                    },
                    "body_text" : {
                        "type": "string"
                    },
                    "body_html" : {
                        "type": "string"
                    },
                    "data" : {
                        "type": "string",
                        "enum": ["none","body","attachment"]
                    }
                }
            }
        }
    }
EOD;
}
