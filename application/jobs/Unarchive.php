<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-05-22
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Unarchive extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $outstream->copyFrom($instream);

        $params = $this->getJobParameters();
        $path = $params['path'] ?? '';
        $files = $params['files'] ?? '';
        $format = $params['format'] ?? 'zip';

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        if ($format == 'zip')
        {
        }
        else if ($format == 'gz' || $format == 'gzip')
        {
            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $archive_fname = $storage_tmpbase . "tmpgz-" . \Flexio\Base\Util::generateRandomString(30) . ".gz";
            register_shutdown_function('unlink', $archive_fname);

            $f = fopen($archive_fname, 'wb');
    
            if (isset($params['path']))
            {
                $vfs->read($path, function($data) use (&$f) {
                    fwrite($f, $data);
                });
            }
             else
            {
                $reader = $instream->getReader();
                while (($data = $reader->read(16384)) !== false)
                {
                    fwrite($f, $data);
                }
            }

            fclose($f);
    
            $writer = $outstream->getWriter();

            $f = gzopen($archive_fname, 'rb');

            while (!gzeof($f)) {
                $buf = gzread($f, 16384);
                if (strlen($buf) > 0)
                {
                    $writer->write($buf);
                }
            }
            
            gzclose($f);
            $writer->close();
            unset($writer);
        }
    }
}
