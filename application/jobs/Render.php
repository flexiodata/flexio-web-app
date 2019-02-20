<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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

/*
// DESCRIPTION:
{
    "op": "render",      // string, required
    "url": "",           // string, required
    "format": "",        // string, enum: png|jpg|jpeg|pdf, default: png
    "paper": "",         // string, enum: paper|letter, default: letter
    "width": 0,          // integer
    "height": 0,         // integer
    "scrollbars": false  // boolean
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['render']),
        'url'        => array('required' => true,  'type' => 'string'),
        'format'     => array('required' => false, 'enum' => ['png','jpg','jpeg','pdf']),
        'paper'      => array('required' => false, 'enum' => ['paper','letter']),
        'width'      => array('required' => false, 'type' => 'integer'),
        'height'     => array('required' => false, 'type' => 'integer'),
        'scrollbars' => array('required' => false, 'type' => 'boolean')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Render extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $params = $this->getJobParameters();

        $items = $params['items'] ?? null;
        $url = $params['url'] ?? null;
        $format = $params['format'] ?? 'png';
        $paper = $params['paper'] ?? 'letter';
        $width = $params['width'] ?? null;
        $height = $params['height'] ?? null;

        $full = $params['full'] ?? false;
        $full = toBoolean($full);

        $paper = ucfirst(strtolower($paper));

        $landscape = $params['landscape'] ?? false;
        $landscape = toBoolean($landscape);

        $scrollbars = $params['scrollbars'] ?? true;
        $scrollbars = toBoolean($scrollbars);

        $scale = $params['scale'] ?? null;
        $scale = (string)$scale;
        if (!is_numeric($scale))
            $scale = null;

        if ($format == 'pdf')
            $content_type = 'application/pdf';
        else if ($format == 'png')
            $content_type = 'image/png';
        else if ($format == 'jpeg' || $format == 'jpg')
        {
            $content_type = 'image/jpeg';
            $format = 'jpeg';
        }
        else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid 'format' parameter. Value must be 'pdf', 'jpeg', or 'pdf'");

        // get docker binary
        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($url === null && isset($items[0]['path']))
            $url = $items[0]['path'];

        // TODO: test: don't require a URL to allow content to be passed in directly
        // if ($url === null || strlen($url) == 0)
        //     throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if we don't have a URL, get the content from stdin
        $content = null;
        if ($url === null)
        {
            $data = '';

            $instream = $process->getStdin();
            $reader = $instream->getReader();
            while (($chunk = $reader->read(16384)) !== false)
                $data .= $chunk;

            $content = base64_encode($data);
        }

        $outstream = $process->getStdout();
        $outstream_properties = array(
            'mime_type' => $content_type
        );
        $outstream->set($outstream_properties);

        $streamwriter = $outstream->getWriter();


        $hide_scrollbars = '';
        if (!$scrollbars)
            $hide_scrollbars = '--hide-scrollbars';

        $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c 'timeout 30s nodejs /fxnodejs/render.js ".
               "--format $format ".
               "--paper $paper ".
               (isset($scale) ? "--scale $scale ":"").
               ($landscape ? "--landscape ":"").
               ($full ? "--fullPage ":"").
               (isset($width) && isset($height) ? "--viewport.width $width --viewport.height $height ":"").
               (isset($url) ? "--url $url ":"").
               (isset($content) ? "--content $content ":"").
               "'";

        $fp = popen($cmd, "r");

        $totlen = 0;

        while (!feof($fp))
        {
            $buf = fread($fp, 1024);
            $streamwriter->write($buf);

            $totlen += strlen($buf);
        }

        fclose($fp);
    }
}
