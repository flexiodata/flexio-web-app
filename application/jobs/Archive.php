<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        $format = $params['format'] ?? 'zip';

        if (is_string($files))
            $files = \Flexio\Base\Util::filterArrayEmptyValues(explode(',', $files));


        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        if ($format == 'zip')
        {
            if (!isset($files) || !is_array($files) || count($files) == 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "No files specified.");

            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $archive_fname = $storage_tmpbase . "tmparchive-" . \Flexio\Base\Util::generateRandomString(30);
            $this->to_delete[] = $archive_fname;

            $zip = new \ZipArchive();
            $zip->open($archive_fname, \ZipArchive::CREATE);

            foreach ($files as $filespec)
            {
                $list = $vfs->listWithWildcard($filespec);

                foreach ($list as $fileinfo)
                {
                    $fname = $storage_tmpbase . "tmparchive-file-" . \Flexio\Base\Util::generateRandomString(30);
                    $this->to_delete[] = $fname;

                    $f = fopen($fname, 'wb');

                    $vfs->read($fileinfo['path'], function($data) use (&$f) {
                        fwrite($f, $data);
                    });

                    fclose($f);

                    $zip->addFile($fname, $fileinfo['name']);
                }
            }

            $zip->close();
            $zip = null;


            $f = fopen($archive_fname, 'rb');

            if (strlen($path) > 0)
            {
                $vfs->write($path, function($length) use (&$f) {
                    if (feof($f))
                        return false;
                    return $f->fread($length);
                });
            }
            else
            {
                $outstream = $process->getStdout();
                $outstream->setMimeType(\Flexio\Base\ContentType::ZIP);
                $writer = $outstream->getWriter();

                while (!feof($f))
                {
                    $buf = fread($f, 32768);
                    $writer->write($buf);
                }
            }

            fclose($f);
        }
        else if ($format == 'gzip')
        {
            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $archive_fname = $storage_tmpbase . "tmpgz-" . \Flexio\Base\Util::generateRandomString(30) . ".gz";
            $this->to_delete[] = $archive_fname;


            $f = gzopen($archive_fname, 'wb9');

            if (strlen($path) > 0)
            {
                $vfs->read($path, function($data) use (&$f) {
                    gzwrite($f, $data);
                });
            }
             else
            {
                $reader = $instream->getReader();
                while (($data = $reader->read(32768)) !== false)
                {
                    gzwrite($f, $data);
                }
            }

            gzclose($f);

            $f = fopen($archive_fname, 'rb');

            if (strlen($path) > 0)
            {
                $vfs->write($path, function($length) use (&$f) {
                    if (feof($f))
                        return false;
                    return $f->fread($length);
                });
            }
            else
            {
                $outstream = $process->getStdout();
                $outstream->setMimeType(\Flexio\Base\ContentType::GZIP);
                $writer = $outstream->getWriter();

                while (!feof($f))
                {
                    $buf = fread($f, 32768);
                    $writer->write($buf);
                }
            }

            fclose($f);

        }
        else
        {
            // unknown format
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Unknown value for 'format' parameter.");




        }
    }
}
