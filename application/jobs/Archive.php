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



class Archive extends \Flexio\Jobs\Base
{
    private $to_delete = [];

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $outstream->copyFrom($instream);

        $params = $this->getJobParameters();
        $path = $params['path'] ?? '';
        $files = $params['files'] ?? '';

        if (is_string($files))
            $files = \Flexio\Base\Util::filterArrayEmptyValues(explode(',', $files));


        $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $archive_fname = $storage_tmpbase . "tmparchive-" . \Flexio\Base\Util::generateRandomString(30);

        $this->to_delete[] = $archive_fname;



        $zip = new \ZipArchive();
        $zip->open($archive_fname, \ZipArchive::CREATE);





        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        foreach ($files as $filespec)
        {
            $list = $vfs->listWithWildcard($filespec);

            foreach ($list as $fileinfo)
            {
                $fname = $storage_tmpbase . "tmparchive-file-" . \Flexio\Base\Util::generateRandomString(30);
                $this->to_delete[] = $fname;

                $f = fopen($fname, 'wb');

                $files = $vfs->read($fileinfo['path'], function($data) use (&$f) {
                    fwrite($f, $data);
                });

                fclose($f);

                $zip->addFile($fname, $filespec['name']);
            }
        }

        $zip->close();
        $zip = null;



        $f = fopen($archive_fname, 'rb');

        if (strlen($path) > 0)
        {
            $vfs->write($path, function($length) use (&$f) {
                return $f->fread($length);
            });
        }
         else
        {
            $outstream = $process->getStdout();
            $writer = $outstream->getWriter();

            while (($buf = fread($f, 32768)) !== false)
            {
                $writer->write($buf);
            }
        }

        fclose($f);
    }
}
