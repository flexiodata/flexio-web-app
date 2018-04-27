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
namespace Flexio\Api;


class Trigger
{
    public static function handleEmail($stream)
    {
        // STEP 21: parse the stream
        $parser = \Flexio\Services\Email::parseResource($stream);

        // STEP 2: determine where to route the email; the pipe to launch
        // is the first part of the email; e.g. <owner>/<pipe>@email.flex.io

        $user_and_pipe = null;

        $email_to_addresses = $parser->getTo();
        if (count($email_to_addresses) > 0)
        {
            $primary_to_address = trim($email_to_addresses[0], " \t\n\r\0\x0B<>");
            $email_to_parts = explode("@", $primary_to_address, 2);
            if (count($email_to_parts) == 2)
            {
                $to_part = $email_to_parts[0];

                $user_and_pipe = explode("/", $to_part, 2);
                if (count($user_and_pipe) != 2)
                    $user_and_pipe = null;
            }
        }

        if (!$user_and_pipe)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Email address must specify user and pipe identifier");

        $user = $user_and_pipe[0];
        $pipe = $user_and_pipe[1];

        // if the user is a username, and not already a user eid, resolve it to an eid
        $user_eid = \Flexio\Object\User::getEidFromUsername($user);
        if ($user_eid)
            $user = $user_eid;

        $pipe_eid = \Flexio\Object\Pipe::getEidFromName($user, $pipe);
        if (!$pipe_eid)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, "Pipe object could not be resolved/found");

        // STEP 3: trigger the appropriate process with the email as an input
        $process = false;
        try
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, "Pipe object not found");

            $pipe_properties = $pipe->get();
            $process_properties = array(
                'parent_eid' => $pipe_properties['eid'],
                'task' => $pipe_properties['task'],
                'owned_by' => $pipe_properties['owned_by']['eid'],
                'created_by' => $pipe_properties['owned_by']['eid'] // TODO: we need to determine user based on email (e.g. owner, or public)
            );
            $process = \Flexio\Object\Process::create($process_properties);
            $process->setRights($pipe->getRights()); // processes inherit rights from the pipe

            // create the process
            $engine = \Flexio\Jobs\StoredProcess::create($process);

            // set an environment variable (parameter) with the "from" email address
            $process_email_params = array();
            $from_addresses = $parser->getFrom();
            if (count($from_addresses) > 0)
            {
                $from_addresses = \Flexio\Services\Email::splitAddressList($from_addresses);
                $process_email_params = array('email-from' => $from_addresses[0]['email'],
                                              'email-from-display' => $from_addresses[0]['display']);
            }
            $engine->setParams($process_email_params);

            // set the process stdin with the email message body
            $message = $parser->getMessageText();
            $message_stream = self::createStreamFromMessage($message);
            $engine->setStdin($message_stream); // set the body of the

            // add any attachments to the process files array
            $streams = self::saveAttachmentsToStreams($parser);
            foreach ($streams as $s)
            {
                $engine->addFile($s->getName(), $s);
            }

            // run the process
            $engine->run(false); // TODO: run in background?
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // STEP 4: if the process ran, return the info
        if ($process === false)
            return false;

        return $process->get();
    }

    private static function createStreamFromMessage(string $message) : \Flexio\Base\Stream
    {
        $stream = \Flexio\Base\Stream::create();
        $stream->buffer = (string)$message;     // shortcut to speed it up -- can also use getWriter()->write((string)$v)
        return $stream;
    }

    private static function saveAttachmentsToStreams(\Flexio\Services\Email $email) : array
    {
        // create a new stream for each attachment; return an array of stream eids
        $streams = array();

        $email_attachments = $email->getAttachments();
        foreach ($email_attachments as $attachment)
        {
            $content = $attachment['content'] ?? '';

            // create the stream
            $outstream_properties = array(
                'name' => $attachment['name'] ?? 'content.dat',
                'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream',
                'size' => strlen($content)
            );

            $outstream = \Flexio\Base\Stream::create($outstream_properties);
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($content);
            $streams[] = $outstream;
        }

        return $streams;
    }
}
