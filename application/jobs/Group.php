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
// EXAMPLE:
{
    "op": "group",
    "params": {
        "group": [
            "vend_no"
        ],
        "columns": [{
            "name": "vend_no",
            "function": "",
            "expression": "vend_no"
        },
        {
            "name": "row_count",
            "function": "count",
            "expression": ""
        },
        {
            "name": "sum_gross_amt",
            "function": "sum",
            "expression": "gross_amt"
        }],
        "where" : "",
        "having" : "",
        "unique" : false,
        "detail" : false
    }
}
*/

class Group extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    public function getOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }
}
