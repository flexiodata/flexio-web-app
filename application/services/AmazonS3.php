<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-28
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class AmazonS3 implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $bucket = '';
    private $accesskey = '';
    private $secretkey = '';
    private $region = '';

    private $aws = null;
    private $s3 = null;

    public static function create(array $params = null) : \Flexio\Services\AmazonS3
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'region'    => array('type' => 'string', 'required' => true),
                'bucket'    => array('type' => 'string', 'required' => true),
                'accesskey' => array('type' => 'string', 'required' => true),
                'secretkey' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $region = $validated_params['region'];
        $bucket = $validated_params['bucket'];
        $accesskey = $validated_params['accesskey'];
        $secretkey = $validated_params['secretkey'];

        $service = new self;
        if ($service->initialize($region, $bucket, $accesskey, $secretkey) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
    }

    private function getS3(string $path) // TODO: add return type
    {
        if (strpos($path, "s3://") !== 0)
        {
            return $this->s3;
        }
         else
        {
            $urlparts = parse_url($path);

            $s3 = new \Aws\S3\S3Client([
                'version'     => 'latest',
                'region'      => $this->region,
                'endpoint'    => $urlparts['host'],
                'credentials' => false
            ]);

            return $s3;
        }
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        $s3 = $this->getS3($path);

        if (!$this->authenticated())
            return array();

        $path = trim($path);

        // trim off preceding slash
        if (strlen($path) > 0 && $path[0] == '/')
            $path = substr($path, 1);
        if ($path === false)
            $path = '';

        // add a trailing slash if necessary
        if (strlen($path) > 0 && substr($path, -1) != '/')
            $path .= '/';
        $single_file = false;

        $arr = array();
        $maxkey = '';

        $marker = null;
        while (true)
        {
            $params = array(
                'Bucket' => $this->bucket,
                'Prefix' => $path,
                'Delimiter' => '/'
            );
            if (!is_null($marker))
                $params['Marker'] = $marker;

            try
            {
                $result = $s3->listObjects($params);
            }
            catch (\Aws\Exception\AwsException $e)
            {

                $message = $e->getAwsErrorMessage();
                if (strlen($message) == 0)
                    $message = "An error occurred while attempting to access the requested resource";
                     else
                    $message = "AWS Error Message: $message";

                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
            }
            catch (\Exception $e)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "An error occurred while attempting to access the requested resource");
            }


            $common_prefixes = $result->get('CommonPrefixes');
            if ($common_prefixes)
            {
                $dir = null;
                foreach ($common_prefixes as $object)
                {
                    $key = $object['Prefix'];
                    $maxkey = max($maxkey, $key);
                    $arr[] = array('name' => $key, 'type' => 'DIR', 'size' => 0, 'modified' => null);
                }
            }

            $objects = $result->get('Contents');
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $key = $object['Key'];
                    $maxkey = max($maxkey, $key);
                    $arr[] = array('name' => $key, 'type' => 'FILE', 'size' => $object['Size'], 'modified' => $object['LastModified']);
                }
            }


            $is_truncated = $result->get('IsTruncated');

            if (count($arr) == 0 && !$is_truncated && substr($path, -1) == '/')
            {
                // no results for the folder, try listing a single file
                $single_file = true;
                $path = rtrim($path, '/');
                continue;
            }

            if (!$is_truncated || is_null($key))
                break;

            $marker = $maxkey;
        }


        $pathlen = strlen($path);
        foreach ($arr as &$a)
        {
            $a['path'] = '/' . $a['name'];

            if (substr($a['name'], -1) == '/')
                $a['name'] = substr($a['name'], 0, strlen($a['name'])-1);

            if ($single_file)
            {
                $a['name'] = rtrim($a['name'],'/');
                $sl = strpos($a['name'], '/');
                if ($sl !== false)
                    $a['name'] = substr($a['name'], $sl+1);
            }
             else
            {
                if ($pathlen > 0 && substr($a['name'], 0, $pathlen) == $path)
                {
                    $a['name'] = substr($a['name'], $pathlen);
                    if ($a['name'] === false) $a['name'] = '';
                }
            }
        }
        unset($a);


        $objects = array();

        foreach ($arr as $a)
        {
            $objects[] = array(
                'name' => trim($a['name'],'/'),
                'path' => rtrim($a['path'],'/'),
                'size' => (int)$a['size'],
                'modified' => $a['modified'],
                'type' => ($a['type'] == 'DIR') ? 'DIR':'FILE'
            );
        }

        return $objects;
    }

    public function getFileInfo(string $path) : array
    {
        $path = $this->getS3KeyFromPath($path);
        $path = rtrim($path, '/');

        // look for a file
        try
        {
            $arr = \Flexio\Base\File::splitBasePathAndName($path);
            $base = $arr['base'];
            $name = $arr['name'];

            $result = $this->s3->headObject([ 'Bucket' => $this->bucket,
                                              'Key' =>  $path  ]);

            $ret = [
                'name' => $name,
                'path' => $path,
                'size' => $result['ContentLength'] ?? null,
                'modified' => (string)$result['LastModified'],
                'type' => 'FILE'
            ];

            return $ret;
        }
        catch (\Exception $e)
        {
        }


        // now look for a folder
        $path .= '/';

        $params = array(
            'Bucket' => $this->bucket,
            'Prefix' => $path,
            'Delimiter' => '/',
            'MaxKeys' => 1
        );



        try
        {
            $result = $this->s3->listObjects($params);

            $common_prefixes = $result->get('CommonPrefixes');
            if ($common_prefixes)
            {
                $dir = null;
                foreach ($common_prefixes as $object)
                {
                    $key = $object['Prefix'];
                    if (substr($key, 0, strlen($path)) == $path)
                    {
                        $path = rtrim($path, '/');
                        if (substr($path, 0, 1) != '/')
                            $path = '/' . $path;

                        $arr = \Flexio\Base\File::splitBasePathAndName($path);
                        $base = $arr['base'];
                        $name = $arr['name'];

                        $ret = [
                            'name' => $name,
                            'path' => $path,
                            'size' => 0,
                            'modified' => '2017-02-23T19:00:43+00:0',
                            'type' => 'DIR'
                        ];

                        return $ret;
                    }

                }
            }

            $objects = $result->get('Contents');
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $key = $object['Key'];
                    if (substr($key, 0, strlen($path)) == $path)
                    {
                        $path = rtrim($path, '/');
                        if (substr($path, 0, 1) != '/')
                            $path = '/' . $path;

                        $arr = \Flexio\Base\File::splitBasePathAndName($path);
                        $base = $arr['base'];
                        $name = $arr['name'];

                        $ret = [
                            'name' => $name,
                            'path' => $path,
                            'size' => (int)$object['Size'] ?? null,
                            'modified' => (string)$object['LastModified'],
                            'type' => 'DIR'
                        ];

                        return $ret;
                    }
                }
            }
        }
        catch (\Aws\Exception\AwsException $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL);
        }

        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
    }

    public function exists(string $path) : bool
    {
        $path = $this->getS3KeyFromPath($path);

        try
        {
            $s3 = self::getS3($path);
            return $s3->doesObjectExist($this->bucket, $path);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $this->write([ 'path' => $path ], function($length) { return false; });
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // S3 directories are created by adding an object with a '/' as the last character
        if (substr($path,-1) != '/')
            $path .= '/';

        if ($this->exists($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Object already exists");

        if (!$this->write([ 'path' => $path ], function($length) { return false; }))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return true;
    }

    public function unlink(string $path) : bool
    {
        if (!$this->authenticated())
            return false;

        $path = $this->getS3KeyFromPath($path);

        try
        {
            $response = $this->s3->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $path
            ));
        }
        catch (\Aws\Exception\AwsException $e)
        {
            $message = $e->getAwsErrorMessage();
            if (strlen($message) == 0)
                $message = "An error occurred while attempting to delete the specified resource";
                 else
                $message = "AWS Error Message: $message";

            die($message);

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED, $message);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED, "An error occurred while attempting to delete the specified resource");
        }

        return true;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        // TODO: let exceptions through on failure?

        $path = $params['path'] ?? '';
        $path = $this->getS3KeyFromPath($path);

        if (!$this->authenticated())
            return false;

        try
        {
            $result = $this->s3->getObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $path
            ));

            if (!isset($result['Body']))
                return false;

            $result['Body']->rewind();
            while ($data = $result['Body']->read(16384)) {
                $callback($data);
            }
        }
        catch (\Aws\Exception\AwsException $e)
        {
            $message = $e->getAwsErrorMessage();
            if (strlen($message) == 0)
                $message = "An error occurred while attempting to access the requested resource";
                 else
                $message = "AWS Error Message: $message";

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $message);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "An error occurred while attempting to access the requested resource");
        }

        return true;
    }

    public function write(array $params, callable $callback) : bool
    {
        if (isset($params['structure']))
        {
            $callback = \Flexio\Services\Util::tableToCsvCallbackAdaptor($params['structure'], $callback);
        }

        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;



        $dest_is_folder = false;
        try
        {
            $info = $this->getFileInfo($path);
            if (isset($info['type']) && $info['type'] == 'DIR')
                $dest_is_folder = true;
        }
        catch (\Exception $e)
        {
        }
        if ($dest_is_folder)
        {
            // destination path is a folder
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }




        $path = $this->getS3KeyFromPath($path);

        if (!$this->authenticated())
            return false;

        try
        {
            $response = $this->s3->createMultipartUpload(array(
                'Bucket' => $this->bucket,
                'Key'    => $path,
                'ContentType' => $content_type
            ));
            $upload_id = $response['UploadId'];

            // 3. Upload the file in parts.
            $parts = array();
            $part_number = 1;
            $done = false;
            while (!$done)
            {
                $chunk = $callback(5 * 1024 * 1024);

                if ($chunk === false)
                {
                    $done = true;
                    $chunk = '';
                    if ($part_number > 1)
                        break; // there must be at least one part
                }

                $result = $this->s3->uploadPart(array(
                    'Bucket'     => $this->bucket,
                    'Key'        => $path,
                    'UploadId'   => $upload_id,
                    'PartNumber' => $part_number,
                    'Body'       => $chunk
                ));

                $parts[] = array(
                    'PartNumber' => $part_number++,
                    'ETag'       => $result['ETag']
                );
            }

            // complete multipart upload
            $result = $this->s3->completeMultipartUpload(array(
                'Bucket'   => $this->bucket,
                'Key'      => $path,
                'UploadId' => $upload_id,
                'MultipartUpload' => array(
                    'Parts'    => $parts
                )
            ));
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    private function getS3KeyFromPath(string $path) : string
    {
        while (false !== strpos($path,'//'))
            $path = str_replace('//','/',$path);
        if (substr($path, 0, 1) == '/')
            $path = substr($path, 1);
        return $path;
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function connect() : bool
    {
        $region = $this->region;
        $bucket = $this->bucket;
        $accesskey = $this->accesskey;
        $secretkey = $this->secretkey;

        if ($this->initialize($region, $bucket, $accesskey, $secretkey) === false)
            return false;

        return true;
    }

    private function initialize(string $region, string $bucket, string $accesskey, string $secretkey) : bool
    {
        $this->authenticated = false;
        $this->region = $region;
        $this->bucket = $bucket;
        $this->accesskey = $accesskey;
        $this->secretkey = $secretkey;
        $this->s3 = null;

        if (strlen($this->region) == 0)
            $this->region = 'us-east-1';

        if ($accesskey == '' && $secretkey == '')
        {
            // no key specified, don't use any credentials
            $credentials = false;
        }
         else
        {
            $credentials = new \Aws\Credentials\Credentials($this->accesskey, $this->secretkey);
        }

        $this->s3 = new \Aws\S3\S3Client([
            'version'     => 'latest',
            'region'      => $this->region,
            'credentials' => $credentials
        ]);

        if (!$this->s3)
            return false;

        try
        {
            $result = $this->s3->headBucket([
                'Bucket' => $bucket,
            ]);
            $this->authenticated = true;
            return true;
        }
        catch (\Aws\Exception\AwsException $e)
        {
        }

        return false;
   }
}
