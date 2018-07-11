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

        $params = $this->getJobParameters();
        $path = $params['path'] ?? '';
        $files = $params['files'] ?? '';
        $format = $params['format'] ?? 'zip';
        $target = $params['target'] ?? '';

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        if ($format == 'zip')
        {
            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $archive_fname = $storage_tmpbase . "tmpzip-" . \Flexio\Base\Util::generateRandomString(30) . ".zip";
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

            $zip = new \ZipArchive();
            if (!$zip->open($archive_fname))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);


            for ($i=0; $i < $zip->numFiles; $i++)
            {
                $entry = $zip->getNameIndex($i);
                //if ( substr( $entry, -1 ) == '/' ) continue; // skip directories
               
                $f = $zip->getStream($entry);
                if (!$f)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Read failed on ZIP entry " . $entry);

                $target_file_path = $target;
                if (substr($target_file_path, -1) != '/')
                    $target_file_path .= '/';
                $target_file_path .= trim($entry,'/');

                $vfs->write($target_file_path, function($length) use (&$f) {
                    if (feof($f))
                        return false;
                    $buf = fread($f, $length);
                    if ($buf === false)
                        return false;
                    return $buf;
                });


                fclose($f);
            }


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
                if ($buf !== false && $buf !== null)
                    $writer->write($buf);
            }
            
            gzclose($f);
            $writer->close();
            unset($writer);
        }
    }
}
