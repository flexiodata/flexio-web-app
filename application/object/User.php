<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-30
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class User extends \Flexio\Object\Base implements \Flexio\IFace\IObject
{
    const MEMBER_UNDEFINED = '';
    const MEMBER_OWNER     = 'owner';
    const MEMBER_GROUP     = 'member';
    const MEMBER_PUBLIC    = 'public';

    public function __construct()
    {
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function getEidFromIdentifier(string $identifier) // TODO: add return type
    {
        $object = new static();
        $user_model = $object->getModel()->user;
        return $user_model->getEidFromIdentifier($identifier);
    }

    public static function getEidFromUsername(string $identifier) // TODO: add return type
    {
        $object = new static();
        $user_model = $object->getModel()->user;
        return $user_model->getEidFromUsername($identifier);
    }

    public static function getEidFromEmail(string $identifier) // TODO: add return type
    {
        $object = new static();
        $user_model = $object->getModel()->user;
        return $user_model->getEidFromEmail($identifier);
    }

    public static function list(array $filter) : array
    {
        // make sure we have a filter on one of the indexed fields
        foreach ($filter as $key => $value)
        {
            if (isset($filter['eid'])) break;
            if (isset($filter['owned_by'])) break;
            if (isset($filter['user_name'])) break;
            if (isset($filter['email'])) break;

            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $object = new static();
        $user_model = $object->getModel()->user;
        $items = $user_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $local_properties = self::formatProperties($i);
            $o->properties = $local_properties;
            $o->setEid($local_properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\User
    {
        $object = new static();
        $user_model = $object->getModel()->user;

        $status = $user_model->getStatus($eid);
        if ($status === \Model::STATUS_UNDEFINED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object->setEid($eid);
        $object->clearCache();
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\User
    {
        // config is stored as a json string, so it needs to be encoded
        if (isset($properties) && isset($properties['config']))
            $properties['config'] = json_encode($properties['config']);

        // a unique username is required for users; if a username
        // isn't specified, then supply a default
        $username = \Flexio\Base\Util::generateHandle();
        $email= $username.'@flex.io';
        if (!isset($properties))
        {
            $properties = array('user_name' => $username, 'email' => $email);
        }
        if (is_array($properties) && !isset($properties['user_name']))
        {
            if (!isset($properties['user_name']))
                $properties['user_name'] = $username;
            if (!isset($properties['email']))
                $properties['email'] = $email;
        }

        $object = new static();
        $user_model = $object->getModel()->user;
        $local_eid = $user_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\User
    {
        $this->clearCache();
        $user_model = $this->getModel()->user;
        $user_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\User
    {
        // TODO: add properties check

        // config is stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['config']))
            $properties['config'] = json_encode($properties['config']);

        $this->clearCache();
        $user_model = $this->getModel()->user;
        $user_model->set($this->getEid(), $properties);
        return $this;
    }

    public function getType() : string
    {
        return \Model::TYPE_USER;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\User
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['owned_by']['eid'];
    }

    public function setStatus(string $status) : \Flexio\Object\Base
    {
        $this->clearCache();
        $user_model = $this->getModel()->user;
        $result = $user_model->setStatus($this->getEid(), $status);
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function getStoreRoot() : \Flexio\Object\Stream
    {
        // get the store root for the current user; if we can't find one, create one
        $user_eid = $this->getEid();

        // see if we already have a store root; if we do, return it
        $items = $this->getModel()->assoc_range($user_eid, \Model::EDGE_HAS_STORE);
        if (count($items) > 0)
        {
            $store_root_eid = $items[0]['eid'];
            $stream = \Flexio\Object\Stream::load($store_root_eid);

            // the following line makes sure that tbl_stream record actually exists
            // and will throw an exception if it doesn't
            $stream->get();

            return $stream;
        }

        // we don't have a root; so create one
        $properties = array();
        $properties['name'] = '';
        $properties['path'] = '';
        $properties['stream_type'] = \Flexio\Object\Stream::TYPE_DIRECTORY;
        $stream = \Flexio\Object\Stream::create($properties);

        $this->getModel()->assoc_add($user_eid, \Model::EDGE_HAS_STORE, $stream->getEid());
        $this->getModel()->assoc_add($stream->getEid(), \Model::EDGE_STORE_FOR, $user_eid);

        return $stream;
    }

    public function getRightsList(array $filter = null) : array
    {
        // TODO: old implementation is deprecated
        return array();
    }

    public function getTokenList() : array
    {
        $res = array();
        $token_items = $this->getModel()->token->getInfoFromUserEid($this->getEid());
        if ($token_items === false)
            return $res;

        foreach ($token_items as $token_info)
        {
            $token_eid = $token_info['eid'];
            $token = \Flexio\Object\Token::load($token_eid);

            // only show tokens that are available; note: token list will return
            // all tokens, including ones that have been deleted, so this check
            // is important
            if ($token->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            $res[] = $token;
        }

        return $res;
    }

    public function getVerifyCode() : string
    {
        $user_model = $this->getModel()->user;
        $verify_code = $user_model->getVerifyCodeFromEid($this->getEid());

        if ($verify_code === false)
            return '';

        return $verify_code;
    }

    public function checkPassword(string $password) : bool
    {
        return $this->getModel()->user->checkUserPasswordByEid($this->getEid(), $password);
    }

    public function isAdministrator() : bool
    {
        // TODO: for now, administrators is any user with an email address for flexio,
        // except for certain reserved test emails

        $user_info = $this->get();
        $user_email = $user_info['email'];

        // exclude these flexio email addresses:
        if (preg_match('/^example@flex\.io$/i', $user_email))
            return false;
        if (preg_match('/^test@flex\.io$/i', $user_email))
            return false;

        // include these flexio email addresses:
        if (preg_match('/^[a-z]+@flex\.io$/i', $user_email))
            return true;

        return false;
    }

    public function getProfilePicture()
    {
        $eid = $this->getEid();
        $updated = $this->getModel()->registry->getUpdateTime($eid, 'profile.picture');
        $etag = is_null($updated) ? null : md5("$eid;profile.picture;$updated");

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        $data = $this->getModel()->registry->getBinary($eid, 'profile.picture', $mime_type);
        if (is_null($data))
            return false;

        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");
        echo $data;
    }

    public function changeProfilePicture(string $filename, string $mime_type)
    {
        $eid = $this->getEid();
        $size = @filesize($filename);
        if ($size === false)
            return false;

        if ($size > 2097152) // 2MB
            return false;

        $contents = file_get_contents($filename);
        $result = $this->getModel()->registry->setBinary($eid, 'profile.picture', $contents, null, $mime_type);
        return $result;
    }

    public function getProfileBackground()
    {
        $eid = $this->getEid();
        $updated = $this->getModel()->registry->getUpdateTime($eid, 'profile.background');
        $etag = is_null($updated) ? null : md5("$eid;profile.background;$updated");

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        $data = $this->getModel()->registry->getBinary($eid, 'profile.background', $mime_type);
        if (is_null($data))
            return false;

        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");
        echo $data;
    }

    public function changeProfileBackground(string $filename, string $mime_type)
    {
        $eid = $this->getEid();
        $size = @filesize($filename);
        if ($size === false)
            return false;

        if ($size > 2097152) // 2MB
            return false;

        $contents = file_get_contents($filename);
        $result = $this->getModel()->registry->setBinary($eid, 'profile.background', $contents, null, $mime_type);
        return $result;
    }

    public function cropPicture(string $type, int $src_x, int $src_y, int $src_w, int $src_h) : bool
    {
        $eid = $this->getEid();
        $mime_type = 'text/plain';

        if ($type == 'profile')
        {
            $type = 'profile.picture';
            $dest_w = 200;
            $dest_h = 200;
        }
         else if ($type == 'profilebackground')
        {
            $type = 'profile.background';
            $dest_w = 1170;
            $dest_h = 351;
        }
         else
        {
            return false;
        }

        $data = $this->getModel()->registry->getBinary($eid, $type, $mime_type);
        if (is_null($data))
            return false;

        $src_img = @imagecreatefromstring($data);
        if ($src_img === false)
            return false;

        // create a 24-bit PNG
        $dest_img = imagecreatetruecolor($dest_w, $dest_h);

        // make sure the background is transparent
        imagesavealpha($dest_img, true);
        //$trans_color = imagecolorallocatealpha($dest_img, 255, 0, 255, 127);
        //imagefill($dest_img, 0, 0, $trans_color);
        $white_color = imagecolorallocate($dest_img, 255, 255, 255);
        imagefill($dest_img, 0, 0, $white_color);

        // copy the source image to the destination image
        imagecopyresampled($dest_img, $src_img, $src_x * -1, $src_y * -1, 0, 0, $src_w, $src_h, $src_w, $src_h);
        imagedestroy($src_img);

        ob_start();
        //imagepng($dest_img);
        imagejpeg($dest_img, null, 75);
        $data = ob_get_clean();

        // free up memory
        imagedestroy($dest_img);

        //$this->getModel()->registry->setBinary($eid, $type, $data, null, 'image/png');
        $this->getModel()->registry->setBinary($eid, $type, $data, null, 'image/jpeg');

        return true;
    }

    private function isCached() : bool
    {
        if ($this->properties === false)
            return false;

        return true;
    }

    private function clearCache() : bool
    {
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        $user_model = $this->getModel()->user;
        $local_properties = $user_model->get($this->getEid());
        $this->properties = self::formatProperties($local_properties);
        return true;
    }

    private static function formatProperties(array $properties) : array
    {
/*
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_USER.'",
            "eid_status" : null,
            "user_name" : null,
            "first_name" : null,
            "last_name" : null,
            "email" : null,
            "email_hash" : null,
            "phone" : null,
            "location_city" : null,
            "location_state" : null,
            "location_country" : null,
            "company_name" : null,
            "company_url" : null,
            "locale_language" : null,
            "locale_decimal" : null,
            "locale_thousands" : null,
            "locale_dateformat" : null,
            "timezone" : null,
            "config" : null,
            "created" : null,
            "updated" : null
        }
        ';
*/
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "user_name" => null,
                "first_name" => null,
                "last_name" => null,
                "email" => null,
                "email_hash" => null,
                "phone" => null,
                "location_city" => null,
                "location_state" => null,
                "location_country" => null,
                "company_name" => null,
                "company_url" => null,
                "locale_language" => null,
                "locale_decimal" => null,
                "locale_thousands" => null,
                "locale_dateformat" => null,
                "timezone" => null,
                "config" => null,
                "owned_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // expand the owner info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );

        // unpack the config
        $config = $mapped_properties['config'] ?? '{}';
            $config = @json_decode($config, true);
            if (!is_array($config))
                $config = new Object;
            $mapped_properties['config'] = $config;

        return $mapped_properties;
    }
}
