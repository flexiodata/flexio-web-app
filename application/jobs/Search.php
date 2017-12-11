<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-01-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.search",
    "params": {
    }
}
*/

class Search extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\BIFacease\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }
}
