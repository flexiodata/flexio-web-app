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


class User extends \Flexio\Object\Base
{
    const MEMBER_UNDEFINED = '';
    const MEMBER_OWNER     = 'owner';
    const MEMBER_GROUP     = 'member';
    const MEMBER_PUBLIC    = 'public';

    public function __construct()
    {
        $this->setType(\Model::TYPE_USER);
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
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public static function load(string $identifier)
    {
        // note: \User::load() differs from other load implementations
        // in that a user can be loaded either by a unique eid, by
        // the user name, or the user email, all of which are unique
        // for a user

        $object = new static();
        $model = $object->getModel();

        // assume the identifier is an eid, and try to find out the type
        $eid = $identifier;
        $local_eid_type = $model->getType($identifier);

        if ($local_eid_type !== $object->getType())
        {
            // the input isn't an eid, so it must be an identifier; try
            // to find the eid from the identifier; if we can't find it,
            // we're done
            $eid = $model->user->getEidFromIdentifier($identifier);
            if ($eid === false)
                return false;
        }

        // TODO: for now, don't allow objects that have been deleted
        // to be loaded; in general, we may want to move this to the
        // api layer, but previously, it's been in the model layer,
        // and we need to make sure the behavior is the same after the
        // model constraint is removed, and object loading is a good
        // location for this constraint
        if ($model->getStatus($eid) === \Model::STATUS_DELETED)
            return false;

        $object->setEid($eid);
        $object->clearCache();
        return $object;
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

    public function getProjects() : array
    {
        $eid = $this->getEid();

        // get the projects for the user based on projects the user owns or is following
        $search_path = "$eid->(".\Model::EDGE_OWNS.",".\Model::EDGE_FOLLOWING.")->(".\Model::TYPE_PROJECT.")";
        $projects = \Flexio\Object\Search::exec($search_path);

        $res = array();
        foreach ($projects as $p)
        {
            // load the object
            $project = \Flexio\Object\Project::load($p);
            if ($project === false)
                continue;

            // only show projects that are available
            if ($project->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            $res[] = $project;
        }

        return $res;
    }

    public function getProcesses() : array
    {
        $eid = $this->getEid();

        // get the processes for the user that are either owned (process may have been created by somebody else) or created by the user
        $search_path = "$eid->(".\Model::EDGE_OWNS.",".\Model::EDGE_CREATED.")->(".\Model::TYPE_PROCESS.")";
        $processes = \Flexio\Object\Search::exec($search_path);

        $res = array();
        foreach ($processes as $p)
        {
            // load the object
            $process = \Flexio\Object\Process::load($p);
            if ($process === false)
                continue;

            $res[] = $process;
        }

        return $res;
    }

    public function getObjects(array $filter = null) : array
    {
        // filter can be contain combinations of the following:
        //$filter = array(
        //    'target_eids' => array(/* index array of eids */),
        //    'eid_type' => array(/* index array of eid types */),
        //    'eid_status' => array(/* index array of eid statuses */)
        //);

        // get the objects owned/followed by the user
        $user_eid = $this->getEid();

        $objects_owned = $this->getModel()->assoc_range($user_eid, \Model::EDGE_OWNS, $filter);
        $objects_followed = $this->getModel()->assoc_range($user_eid, \Model::EDGE_FOLLOWING, $filter);
        $objects = array_merge($objects_owned, $objects_followed);

        $res = array();
        foreach ($objects as $object_info)
        {
            $object_eid = $object_info['eid'];
            $object_eid_type = $object_info['eid_type'];
            $object = \Flexio\Object\Store::load($object_eid, $object_eid_type);
            if ($object === false)
                continue;

            $res[] = $object;
        }

        return $res;
    }

    public function getObjectCount(array $filter = null) : int
    {
        // filter can be contain combinations of the following:
        //$filter = array(
        //    'target_eids' => array(/* index array of eids */),
        //    'eid_type' => array(/* index array of eid types */),
        //    'eid_status' => array(/* index array of eid statuses */)
        //);

        // get the counts of the objects owned/followed by the user
        $user_eid = $this->getEid();

        $objects_owned = $this->getModel()->assoc_count($user_eid, \Model::EDGE_OWNS, $filter);
        $objects_followed = $this->getModel()->assoc_count($user_eid, \Model::EDGE_FOLLOWING, $filter);
        $total_objects = $objects_owned + $objects_followed;

        return $total_objects;
    }

    public function getObjectRights(array $filter = null) : array
    {
        // get the objects for the user
        $objects = $this->getObjects($filter);

        // return the rights for the objects
        $res = array();
        foreach ($objects as $o)
        {
            $rights = $o->getRights();
            foreach ($rights as $r)
            {
                $res[] = $r;
            }
        }

        return $res;
    }

    public function getTokens() : array
    {
        $res = array();
        $token_items = $this->getModel()->token->getInfoFromUserEid($this->getEid());
        if ($token_items === false)
            return $res;

        foreach ($token_items as $token_info)
        {
            $token_eid = $token_info['eid'];
            $token = \Flexio\Object\Token::load($token_eid);
            if ($token === false)
                continue;

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
        $this->eid_status = false;
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        // get the properties
        $local_properties = $this->getProperties();
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties() : array
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_USER.'",
            "eid_status" : null,
            "ename" : null,
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

        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the config
        $config = $properties['config'] ?? '{}';
        $config = @json_decode($config, true);
        if (!is_array($config))
            $config = new Object;
        $properties['config'] = $config;

        return $properties;
    }
}
