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
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api1;


class Trigger
{
    public static function handleEmail($stream, string $pipe_eid = null) // TODO: add function parameter
    {
        // if eid is specified, the pipe with the given eid will be
        // run, otherwise, the first part of the email subject will
        // be used
/*
        // STEP 1: save the email to a temporary file
        $emailfile = \Flexio\Base\File::getTempFilename('txt');

        $instream = fopen($stream, 'rb');
        $outstream = fopen($emailfile, 'w');

        while (!feof($instream))
        {
            fwrite($outstream, fread($instream, 4096));
        }

        fclose($instream);
        fclose($outstream);
        */

        // STEP 2: parse the temporary file
        $parser = \Flexio\Services\Email::parseResource($stream);

        // STEP 3: determine where to route the email; the pipe to launch
        // is the first part of the email; e.g. <pipe_eid>@email.flex.io

        if (!isset($pipe_eid))
        {
            $email_to_addresses = $parser->getTo();
            if (count($email_to_addresses) > 0)
            {
                $primary_to_address = trim($email_to_addresses[0], " \t\n\r\0\x0B<>");
                $email_to_parts = explode("@", $primary_to_address, 2);
                $pipe_eid = $email_to_parts[0];
            }
        }

        // STEP 4: trigger the appropriate process with the email as an input
        $process = false;
        try
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            $pipe_properties = $pipe->get();
            $process_properties = array(
                'parent_eid' => $pipe_properties['eid'],
                'task' => $pipe_properties['task'],
                'owned_by' => $pipe_properties['owned_by']['eid'],
                'created_by' => $pipe_properties['owned_by']['eid'] // TODO: we need to determine user based on email (e.g. owner, or public)
            );
            $process = \Flexio\Object\Process::create($process_properties);
            $process->setRights($pipe->getRights()); // processes inherit rights from the pipe

            // set an environment variable (parameter) with the "from" email address
            $process_email_params = array();
            $from_addresses = $parser->getFrom();
            if (count($from_addresses) > 0)
            {
                $from_addresses = \Flexio\Services\Email::splitAddressList($from_addresses);
                $process_email_params = array('email-from' => $from_addresses[0]['email'],
                                              'email-from-display' => $from_addresses[0]['display']);
            }

            // save the email attachments as streams, and if there
            // are any attachments, run the pipe with the attachments
            $streams = self::saveAttachmentsToStreams($parser, $process);
            foreach ($streams as $s)
            {
                // TODO: add the streams to the process; stdin only allows
                // one; use some other technique?
            }


            // run the pipe
            $engine = \Flexio\Jobs\StoredProcess::create($process);
            $engine->setParams($process_email_params);
            $engine->run(false); // handleEmail should be run in background from email processing script
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // STEP 5: delete the temporary file
        //unlink($emailfile);

        // if the process ran, return the info
        if ($process === false)
            return false;

        return $process->get();
    }

    private static function saveAttachmentsToStreams(\Flexio\Services\Email $email) : array
    {
        // create a new stream for each attachment; return an array of stream eids
        $streams = array();

        $email_attachments = $email->getAttachments();
        foreach ($email_attachments as $attachment)
        {
            // create the stream
            $outstream_properties = array(
                'name' => $attachment['name'] ?? 'content.dat',
                'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream'
            );

            $outstream = \Flexio\Object\Stream::create($outstream_properties);
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($attachment['content']);
            $streams[] = $outstream;
        }

        return $streams;
    }
}
