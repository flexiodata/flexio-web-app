<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Render extends \Flexio\Jobs\Base
{
    public function run()
    {
        $this->getOutput()->setEnv($this->getInput()->getEnv());
        $input = $this->getInput()->getStreams();

        foreach ($input as $instream)
        {
            $this->createOutput($instream);
        }
    }

    private function createOutput(\Flexio\Object\Stream $instream)
    {
        $job_definition = $this->getProperties();
        $mime_type = $instream->getMimeType();

        $url = $job_definition['params']['url'] ?? null;

        if (!isset($url))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);


        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->addStream($outstream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxchrome sh -c 'timeout 30s google-chrome --headless --disable-gpu  --print-to-pdf --no-sandbox $url && cat output.pdf' > /tmp/output.pdf";

        $fp = popen($cmd, "r"); 
        while (!feof($fp)) 
        { 
            $buf = fread($fp, 1024); 
            $streamwriter->write($buf);
        } 
        fclose($fp); 
    }

    // job definition info
    const MIME_TYPE = 'flexio.render';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.render",
        "params": {
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
                "enum": ["flexio.render"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
