<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
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
    public static function handleEmail($stream, bool $echo_result = false) : void // echo_result is available for testing, but not normally necessary
    {
        // STEP 1: parse the stream
        $parser = \Flexio\Base\Email::parseResource($stream);

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

                // support | and / delimiters
                $to_part = str_replace(array('|','/'), '|', $to_part);
                $user_and_pipe = explode('|', $to_part, 2);

                if (count($user_and_pipe) != 2)
                    $user_and_pipe = null;
            }
        }

        if (!$user_and_pipe)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Email address must specify user and pipe identifier");

        $user = $user_and_pipe[0];
        $pipe = $user_and_pipe[1];

        // if the user is a username, and not already a user eid, resolve it to an eid
        $user_eid = \Flexio\Object\User::getEidFromUsername($user);
        if ($user_eid)
            $user = $user_eid;

        // if the user is a username, and not already a user eid, resolve it to an eid
        $pipe_eid = \Flexio\Object\Pipe::getEidFromName($user, $pipe);
        if ($pipe_eid)
            $pipe = $pipe_eid;

        // STEP 3: trigger the appropriate process with the email as an input
        $pipe = \Flexio\Object\Pipe::load($pipe);
        if ($pipe->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Pipe object not found");

        // get the pipe properties
        $pipe_properties = $pipe->get();

        // only allow pipes to be triggered if the pipe is activated
        $email_trigger_active = ($pipe_properties['deploy_mode'] === \Model::PIPE_DEPLOY_MODE_RUN);
        if ($email_trigger_active === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // create a new process object for storing process info
        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => \Model::PROCESS_TRIGGERED_EMAIL,
            'owned_by' => $pipe_properties['owned_by']['eid'],
            'created_by' => $pipe_properties['owned_by']['eid'] // TODO: we need to determine user based on email (e.g. owner, or public)
        );
        $process_store = \Flexio\Object\Process::create($process_properties);

        // create a new process engine for running a process
        $mount_properties = array(
            'connection_eid' => $pipe_properties['parent']['eid'] ?? ''
        );
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $mount_properties);
        $process_engine->queue('\Flexio\Jobs\Task::run', $pipe_properties['task']);

        // set an environment variable (parameter) with the "from" email address
        $process_email_params = array();
        $from_addresses = $parser->getFrom();
        if (count($from_addresses) > 0)
        {
            $from_addresses = \Flexio\Base\Email::splitAddressList($from_addresses);
            $process_email_params = array('trigger.email.from' => $from_addresses[0]['email'],
                                          'trigger.email.fromdisplay' => $from_addresses[0]['display'],
                                          'trigger.email.subject' => $parser->getSubject(),
                                          'email-from' => $from_addresses[0]['email'],            // deprecated, please remove
                                          'email-from-display' => $from_addresses[0]['display']); // deprecated, please remove
        }
        $process_engine->setParams($process_email_params);

        // set the process stdin with the email message body
        $message = $parser->getMessageText();
        $message_stream = self::createStreamFromMessage($message);
        $process_engine->setStdin($message_stream); // set the body of the

        // add any attachments to the process files array
        $streams = self::saveAttachmentsToStreams($parser);
        foreach ($streams as $s)
        {
            $process_engine->addFile($s->getName(), $s);
        }

        // create a process host to connect the store/engine and run the process
        $process_host = \Flexio\Jobs\ProcessHost::create($process_store, $process_engine);
        $process_host->run(true);
    }

    private static function createStreamFromMessage(string $message) : \Flexio\Base\Stream
    {
        $stream = \Flexio\Base\Stream::create();
        $stream->getWriter()->write($message);
        return $stream;
    }

    private static function saveAttachmentsToStreams(\Flexio\Base\Email $email) : array
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
