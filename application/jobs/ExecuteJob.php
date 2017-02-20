<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-11-20
 *
 * @package flexio
 * @subpackage Jobs
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class ExecuteJob extends Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            $this->createOutput($instream);
/*
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
                    break;

                // stream/text/csv input
                case ContentType::MIME_TYPE_STREAM:
                case ContentType::MIME_TYPE_TXT:
                case ContentType::MIME_TYPE_CSV:
                case ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutput($instream);
                    break;
            }
*/
        }
    }

    private function createOutput($instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(Util::generateHandle());
        $this->getOutput()->push($outstream);

        // if the input mime type is a table, set the output type to text
        if ($instream->getMimeType() === ContentType::MIME_TYPE_FLEXIO_TABLE)
            $outstream->setMimeType(ContentType::MIME_TYPE_TXT);

        // properties
        $job_definition = $this->getProperties();
        $input_structure = $instream->getStructure();

        // determine what program to load
        $program_type = false;
        $program_extension = false;
        switch ($job_definition['params']['lang'])
        {
            case 'r':
                $program_type = 'r';
                $program_extension = 'r';
                break;

            case 'python':
                $program_type = 'python';
                $program_extension = 'py';
                break;

            case 'javascript':
                $program_type = 'javascript';
                $program_extension = 'js';
                break;

            case 'go':
                $program_type = 'go';
                $program_extension = 'go';
                break;
        }

        if ($program_type === false)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $program_path = Util::getBinaryPath($program_type);
        if (!isset($program_path))
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // get the code from the template
        $code = isset_or($job_definition['params']['code'], '');
        if (strlen($code) == 0)
            return $this->fail(\Model::ERROR_MISSING_PARAMETER, _(''), __FILE__, __LINE__);

        // the code is base64 encoded, so decode it and write it out
        // to a temporary file
        $code = base64_decode($code);
        $filename = Util::createTempFile('fxscript', $program_extension);
        file_put_contents($filename, $code);

        // initiate the program process
        $cmd = $program_path . ' ' . "\"$filename\"";
        $cwd = sys_get_temp_dir();
        $env = array('some_option' => 'aeiou');

        $descriptorspec = array(
           0 => array("pipe", "r"),
           1 => array("pipe", "w"),
           2 => array("pipe", "w")
        );

        $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, NULL);

        if (!is_resource($process))
        {
            @unlink($filename);
            return $this->fail(\Model::ERROR_GENERAL, _(''), __FILE__, __LINE__);
        }

        if ($instream->getMimeType() !== ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $instream->read(function ($data) use (&$pipes) {
                fputs($pipes[0], $data);
            });
        }
         else
        {
            $count = 0;
            $structure = $instream->getStructure();
            $structure_fields = implode(',', $structure->getNames()) . "\r\n";

            $instream->read(function ($data) use (&$pipes, &$count, $structure_fields) {

                if ($count === 0)
                    fputs($pipes[0], $structure_fields);

                if (is_array($data))
                    $data = implode(',', $data) . "\r\n"; // TODO: better CSV output?
                fputs($pipes[0], $data);

                $count++;
            });
        }

        fclose($pipes[0]);
        fclose($pipes[2]);

        // note: here, we're writing out all the input to stdin before reading it from stdout;
        // this causes a deadlock when programs write to stdout and fill it up before finishing
        // reading it from stdin; see the comments here: http://php.net/manual/en/function.proc-open.php
        // for example, script #1 below will cause a deadlock if the input is sufficiently large
        // whereas script #2 will work fine; TODO: need to figure out a solution for arbitrarily
        // sized inputs

        // script #1:
        //
        // import sys
        // for line in sys.stdin:
        //     sys.stdout.write(line)

        // script #2:
        //
        // import sys
        // input = '';
        // for line in sys.stdin:
        //     input += line;
        // sys.stdout.write(line)

        // write to the output
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt

        $idx = 0;
        while (true)
        {
            $status = proc_get_status($process);
            $read = array($pipes[1]);
            $write = array();
            $except = array();
            if (stream_select($read, $write, $except, 1) > 0)
            {
                $content = fread($pipes[1], 1024);
                if (!$content)
                    break;

                $streamwriter->write($content);
            }

            //if (!$status['running'])
            //    break;
        }

        if ($streamwriter !== false)
        {
            $streamwriter->close();
            $outstream->setSize($streamwriter->getBytesWritten());
        }

        // TODO: set appropriate mime type based on content?

        fclose($pipes[1]);
        @unlink($filename);

        // It is important that you close any pipes before calling
        // proc_close in order to avoid a deadlock
        $return_value = proc_close($process);
    }


    // job definition info
    const MIME_TYPE = 'flexio.execute';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.execute",
        "params": {
            "lang": "python",
            "code": "<base64 encoded>"
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.execute"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
