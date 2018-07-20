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
    public const MEMBER_UNDEFINED = '';
    public const MEMBER_OWNER     = 'owner';
    public const MEMBER_GROUP     = 'member';
    public const MEMBER_PUBLIC    = 'public';

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

        $properties = $user_model->get($eid);
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\User
    {
        if (!isset($properties))
            $properties = array();

        // config is stored as a json string, so it needs to be encoded
        if (isset($properties) && isset($properties['config']))
            $properties['config'] = json_encode($properties['config']);

        // a unique username is required for users; if a username
        // isn't specified, then supply a default
        $username = \Flexio\Base\Identifier::generate();
        $email= $username.'@flex.io';
        if (!isset($properties))
        {
            $properties = array('username' => $username, 'email' => $email);
        }
        if (is_array($properties) && !isset($properties['username']))
        {
            if (!isset($properties['username']))
                $properties['username'] = $username;
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

    public function purge() : bool
    {
        // purges all rows a given user owns, including the user itself;
        // note: can't be undone; this is a physical delete

        $owner_eid = $this->getEid();

        // physically delete the database records owned by the user
        $this->getModel()->action->purge($owner_eid);
        $this->getModel()->comment->purge($owner_eid);
        $this->getModel()->connection->purge($owner_eid);
        $this->getModel()->pipe->purge($owner_eid);
        $this->getModel()->process->purge($owner_eid);
        $this->getModel()->registry->purge($owner_eid);
        $this->getModel()->right->purge($owner_eid);
        $this->getModel()->stream->purge($owner_eid);
        $this->getModel()->token->purge($owner_eid);

        // delete user last since this is the last hook
        $this->getModel()->user->purge($owner_eid);

        return true;
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

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
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

    public function getStoreRoot() : \Flexio\Object\Stream
    {
        // get the store root for the current user; if we can't find one, create one
        $user_eid = $this->getEid();

        $filter = array('parent_eid' => '', 'stream_type' => \Flexio\Object\Stream::TYPE_DIRECTORY,
                        'eid_status' => \Model::STATUS_AVAILABLE, 'owned_by' => $user_eid);
        $streams = \Flexio\Object\Stream::list($filter);
        if (count($streams) > 0)
            return $streams[0];

        // we don't have a root; so create one
        $properties = array();
        $properties['name'] = '';
        $properties['path'] = '';
        $properties['stream_type'] = \Flexio\Object\Stream::TYPE_DIRECTORY;
        $properties['owned_by'] = $user_eid;
        $stream = \Flexio\Object\Stream::create($properties);

        return $stream;
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
        return $this->getModel()->user->isAdministrator($this->getEid());
    }

    public function setProfilePicture(string $filename, string $mime_type) : bool
    {
        $eid = $this->getEid();
        $size = @filesize($filename);
        if ($size === false)
            return false;

        if ($size > 2097152) // 2MB
            return false;

        $contents = file_get_contents($filename);
        return $this->getModel()->registry->setBinary($eid, 'profile.picture', $contents, null, $mime_type);
    }

    public function getProfilePicture(&$data) : string
    {
        // note: sets the content in the data param; returns an appropriate string
        // that can be used an etag

        $eid = $this->getEid();
        $updated = $this->getModel()->registry->getUpdateTime($eid, 'profile.picture');
        $etag = is_null($updated) ? null : md5("$eid;profile.picture;$updated");

        $mime_type = 'text/plain';
        $data = $this->getModel()->registry->getBinary($eid, 'profile.picture', $mime_type);

        return $etag;
    }

    public function setProfileBackground(string $filename, string $mime_type) : bool
    {
        $eid = $this->getEid();
        $size = @filesize($filename);
        if ($size === false)
            return false;

        if ($size > 2097152) // 2MB
            return false;

        $contents = file_get_contents($filename);
        return $this->getModel()->registry->setBinary($eid, 'profile.background', $contents, null, $mime_type);
    }

    public function getProfileBackground(&$data) : string
    {
        // note: sets the content in the data param; returns an appropriate string
        // that can be used an etag

        $eid = $this->getEid();
        $updated = $this->getModel()->registry->getUpdateTime($eid, 'profile.background');
        $etag = is_null($updated) ? null : md5("$eid;profile.background;$updated");

        $mime_type = 'text/plain';
        $data = $this->getModel()->registry->getBinary($eid, 'profile.background', $mime_type);

        return $etag;
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
        return $this->getModel()->registry->setBinary($eid, $type, $data, null, 'image/jpeg');
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
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" => null,
                "username" => null,
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
