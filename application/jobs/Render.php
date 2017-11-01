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
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        $input = $context->getStreams();
        $context->clearStreams();

        $job_definition = $this->getProperties();
        $items = $job_definition['params']['items'] ?? null;
        $url = $job_definition['params']['url'] ?? null;
        $format = $job_definition['params']['format'] ?? 'pdf';
        $width = $job_definition['params']['width'] ?? null;
        $height = $job_definition['params']['height'] ?? null;
        $scrollbars = $job_definition['params']['scrollbars'] ?? true;
        $scrollbars = toBoolean($scrollbars);

        if ($format == 'pdf')
            $content_type = 'application/pdf';
        else if ($format == 'png')
            $content_type = 'image/png';
        else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid 'format' parameter. Value must be either 'pdf' or 'png'");

        // get docker binary
        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if ($url === null && isset($items[0]['path']))
            $url = $items[0]['path'];

        if ($url === null)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $outstream = $context->getStdout();
        $outstream_properties = array(
            'mime_type' => $content_type
        );
        $outstream->set($outstream_properties);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        $windowsize = '';
        if (isset($width) && isset($height))
            $windowsize = '--window-size="'.$width.'x'.$height.'"';

        $hide_scrollbars = '';
        if (!$scrollbars)
            $hide_scrollbars = '--hide-scrollbars';

        if ($format == 'pdf')
            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxchrome sh -c 'timeout 30s google-chrome --headless --disable-gpu --print-to-pdf --no-sandbox $windowsize $hide_scrollbars $url && cat output.pdf'";
        else if ($format == 'png')
            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxchrome sh -c 'timeout 30s google-chrome --headless --disable-gpu --screenshot --no-sandbox $windowsize $hide_scrollbars $url && cat screenshot.png'";

        $fp = popen($cmd, "r");

        $totlen = 0;

        while (!feof($fp))
        {
            $buf = fread($fp, 1024);
            $streamwriter->write($buf);

            $totlen += strlen($buf);
        }

        fclose($fp);
/*
        $counter = 0;
        foreach ($input as $item)
        {
            $counter++;
            $output_name = $item['name'] ?? "output-$counter.$format";
            $url = $item['path'] ?? 'about:blank';

            // create the output stream
            $outstream_properties = array(
                'name' => $output_name,
                'mime_type' => $content_type
            );
            $outstream = \Flexio\Object\StreamMemory::create($outstream_properties);
            $streamwriter = $outstream->getWriter();

            $windowsize = '';
            if (isset($width) && isset($height))
                $windowsize = '--window-size="'.$width.'x'.$height.'"';

            $hide_scrollbars = '';
            if (!$scrollbars)
                $hide_scrollbars = '--hide-scrollbars';

            if ($format == 'pdf')
                $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxchrome sh -c 'timeout 30s google-chrome --headless --disable-gpu --print-to-pdf --no-sandbox $windowsize $hide_scrollbars $url && cat output.pdf'";
            else if ($format == 'png')
                $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxchrome sh -c 'timeout 30s google-chrome --headless --disable-gpu --screenshot --no-sandbox $windowsize $hide_scrollbars $url && cat screenshot.png'";
            else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $fp = popen($cmd, "r");

            while (!feof($fp))
            {
                $buf = fread($fp, 1024);
                $streamwriter->write($buf);
            }
            fclose($fp);

            $context->addStream($outstream);
        }
*/
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
