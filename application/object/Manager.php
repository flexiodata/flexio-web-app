<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-02
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Manager
{
    public static function handleEmail(string $stream, string $pipe_eid = null)
    {
        // if eid is specified, the pipe with the given eid will be
        // run, otherwise, the first part of the email subject will
        // be used

        // STEP 1: save the email to a temporary file
        $emailfile = \Flexio\Base\Util::getTempFilename('txt');

        $instream = fopen($stream, 'rb');
        $outstream = fopen($emailfile, 'w');

        while (!feof($instream))
        {
            fwrite($outstream, fread($instream, 4096));
        }

        fclose($instream);
        fclose($outstream);

        // STEP 2: parse the temporary file
        $parser = \Flexio\Services\Email::parseStream($emailfile);

        // STEP 3: determine where to route the email; the pipe to launch
        // is the first part of the email; e.g. <pipe_eid>@email.flex.io

        if (!isset($pipe_eid))
        {
            $email_to_addresses = $parser->getTo();
            if (count($email_to_addresses) > 0)
            {
                $primary_to_address = $email_to_addresses[0];
                $email_to_parts = explode("@", $primary_to_address, 2);
                $pipe_eid = $email_to_parts[0];
            }
        }

        // STEP 4: trigger the appropriate process with the email as an input
        $process = false;
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($pipe !== false)
        {
            $pipe_properties = $pipe->get();
            $process = \Flexio\Object\Process::create($pipe_properties);
            if ($process !== false)
            {
                // save the email attachments as streams, and if there
                // are any attachments, run the pipe with the attachments
                $streams = self::saveAttachmentsToStreams($parser);
                foreach ($streams as $s)
                {
                    $process->addInput($streams);
                }

                $process->run(false); // handleEmail should be run in background from email processing script
            }
        }

        // STEP 5: delete the temporary file
        unlink($emailfile);

        // if the process ran, return the info
        if ($process === false)
            return false;

        return $process->get();
    }

    private static function saveAttachmentsToStreams($email_parser) // TODO: set parameter type
    {
        // create a new stream for each attachment; return an array of stream eids
        $streams = array();

        $email_attachments = $email_parser->getAttachments();
        foreach ($email_attachments as $attachment)
        {
            // TODO: read attachment in chunks for large files

            $name = $attachment['name'];
            $content = $attachment['content'];
            $mime_type = \Flexio\Base\ContentType::getMimeType($name, $content);

            // create the stream
            $outstream_properties = array(
                'name' => $name,
                'mime_type' => $mime_type
            );
            $outstream = \Flexio\Object\Stream::create($outstream_properties);
            $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

            if ($streamwriter !== false)
            {
                $streamwriter->write($content);
                $streams[] = array('eid' => $outstream->getEid());
            }
        }

        return $streams;
    }
}
