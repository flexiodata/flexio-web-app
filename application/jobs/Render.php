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
        'op'         => array('type' => 'string',     'required' => true),
        'url'        => array('type' => 'string',     'required' => true),
        'format'     => array('type' => 'string',     'required' => false),
        'paper'      => array('type' => 'string',     'required' => false),
        'width'      => array('type' => 'integer',    'required' => false),
        'height'     => array('type' => 'integer',    'required' => false),
        'scrollbars' => array('type' => 'boolean',    'required' => false)
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Render extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid 'format' parameter. Value must be 'pdf', 'jpeg', or 'pdf'");

        // get docker binary
        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if ($url === null && isset($items[0]['path']))
            $url = $items[0]['path'];

        if ($url === null || strlen($url) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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
               "--url $url ".
               "--format $format ".
               "--paper $paper ".
               (isset($scale) ? "--scale $scale ":"").
               ($landscape ? "--landscape ":"").
               ($full ? "--fullPage ":"").
               (isset($width) && isset($height) ? "--viewport.width $width --viewport.height $height ":"").
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
