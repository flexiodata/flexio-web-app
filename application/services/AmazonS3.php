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


namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class AmazonS3 implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;
    private $bucket = '';
    private $accesskey = '';
    private $secretkey = '';
    private $region = '';

    private $aws = null;
    private $s3 = null;

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create($params = null)
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect($params)
    {
        $this->close();

        $validator = \Flexio\Base\Validator::getInstance();
        if (($params = $validator->check($params, array(
                'region' => array('type' => 'string', 'required' => true),
                'bucket' => array('type' => 'string', 'required' => true),
                'accesskey' => array('type' => 'string', 'required' => true),
                'secretkey' => array('type' => 'string', 'required' => true)
            ))) === false)
            return false;

        $region = $params['region'];
        $bucket = $params['bucket'];
        $accesskey = $params['accesskey'];
        $secretkey = $params['secretkey'];
        $this->initialize($region, $bucket, $accesskey, $secretkey);
        return $this->isOk();
    }

    public function isOk()
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->region = '';
        $this->bucket = '';
        $this->accesskey = '';
        $this->secretkey = '';
        $this->s3 = null;
   }

    public function listObjects($path = '')
    {
        if (!$this->isOk())
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

        $s3 = $this->s3;

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

            $result = $s3->listObjects($params);


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
            if (!$is_truncated || is_null($key))
                break;

            $marker = $maxkey;
        }

        $pathlen = strlen($path);
        foreach ($arr as &$a)
        {
            $a['path'] = '/' . $a['name'];

            if (substr($a['name'], -1) == '/')
                $a['name']= substr($a['name'], 0, strlen($a['name'])-1);
            if ($pathlen > 0 && substr($a['name'], 0, $pathlen) == $path)
            {
                $a['name'] = substr($a['name'], $pathlen);
                if ($a['name'] === false) $a['name'] = '';
            }
        }
        unset($a);



        $objects = array();

        foreach ($arr as $a)
        {
            $objects[] = array(
                'name' => $a['name'],
                'path' => $a['path'],
                'size' => $a['size'],
                'modified' => $a['modified'],
                'is_dir' => ($a['type'] == 'DIR') ? true:false,
                'root' => 'amazons3'
            );
        }

        return $objects;
    }

    public function exists($path)
    {
        if (substr($path,0,1) == '/')
            $path = substr($path,1);

        try
        {
            $s3 = self::getS3();
            return $s3->doesObjectExist($this->bucket, $path);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function getInfo($path)
    {
        // TODO: implement
        return false;
    }

    public function read($params, $callback)
    {
        $path = $params['path'] ?? '';

        if (!$this->isOk())
            return false;

        if (substr($path,0,1) == '/')
            $path = substr($path,1);

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
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function write($params, $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;

        if (!$this->isOk())
            return false;

        if (substr($path,0,1) == '/')
            $path = substr($path,1);

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
            while (true)
            {
                $chunk = $callback(5 * 1024 * 1024);

                if ($chunk === false)
                    break;

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


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize($region, $bucket, $accesskey, $secretkey)
    {
        $this->close();
        $this->region = $region;
        $this->bucket = $bucket;
        $this->accesskey = $accesskey;
        $this->secretkey = $secretkey;
        $this->is_ok = true;

        if (strlen($this->region) == 0)
            $this->region = 'us-east-1';

        require_once dirname(dirname(__DIR__)) . '/library/aws/aws.phar';

        //setAutoloaderIgnoreErrors(true);
        $credentials = new \Aws\Credentials\Credentials($this->accesskey, $this->secretkey);

        $this->s3 = new \Aws\S3\S3Client([
            'version'     => 'latest',
            'region'      => $this->region,
            'credentials' => $credentials
        ]);

        if ($this->s3)
        {
            $this->is_ok = true;
        }
   }
}
