<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams, David Z. Williams
 * Created:  2016-12-08
 *
 * @package flexio
 * @subpackage Api
 */

namespace Flexio\Api;

class ProfileApi
{
    public static function getprofilepicture($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'eid', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $eid = $params['eid'];
        $updated = \System::getModel()->registry->getUpdateTime($eid, 'profile.picture');
        $etag = is_null($updated) ? null : md5("$eid;profile.picture;$updated");

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        $data = \System::getModel()->registry->getBinary($eid, 'profile.picture', $mime_type);
        if (is_null($data))
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");
        echo $data;
    }

    public static function changeprofilepicture($params, $request)
    {
        if (!isset($_FILES['file']['tmp_name']) || !isset($_FILES['file']['type']))
            return $request->getValidator()->fail(Api::ERROR_MISSING_PARAMETER);

        $fname = $_FILES['file']['tmp_name'];
        $mime_type = $_FILES['file']['type'];
        $size = @filesize($fname);
        if ($size === false)
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER);

        if ($size > 2097152) // 2MB
            return $request->getValidator()->fail(Api::ERROR_SIZE_LIMIT_EXCEEDED);

        $contents = file_get_contents($_FILES['file']['tmp_name']);

        $user_eid = \System::getCurrentUserEid();
        $result = \System::getModel()->registry->setBinary($user_eid, 'profile.picture', $contents, null, $mime_type);
        if ($result === false)
            return false;

        $result = array();
        $result['profile_picture_url'] = self::getProfilePictureUrl($user_eid);

        return $result;
    }

    public static function getprofilebackground($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'eid', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $eid = $params['eid'];
        $updated = \System::getModel()->registry->getUpdateTime($eid, 'profile.background');
        $etag = is_null($updated) ? null : md5("$eid;profile.background;$updated");

        if (!is_null($etag) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH'])
        {
            header("HTTP/1.1 304 Not Modified");
            exit(0);
        }

        $mime_type = 'text/plain';
        $data = \System::getModel()->registry->getBinary($eid, 'profile.background', $mime_type);
        if (is_null($data))
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        header('Content-Type: ' . $mime_type);
        if (!is_null($etag))
            header("Etag: $etag");
        echo $data;
    }

    public static function changeprofilebackground($params, $request)
    {
        if (!isset($_FILES['file']['tmp_name']) || !isset($_FILES['file']['type']))
            return $request->getValidator()->fail(Api::ERROR_MISSING_PARAMETER);

        $fname = $_FILES['file']['tmp_name'];
        $mime_type = $_FILES['file']['type'];
        $size = @filesize($fname);
        if ($size === false)
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER);

        if ($size > 2097152) // 2MB
            return $request->getValidator()->fail(Api::ERROR_SIZE_LIMIT_EXCEEDED);

        $contents = file_get_contents($_FILES['file']['tmp_name']);

        $user_eid = \System::getCurrentUserEid();
        $result = \System::getModel()->registry->setBinary($user_eid, 'profile.background', $contents, null, $mime_type);
        if ($result === false)
            return false;

        $result = array();
        $result['profile_background_url'] = self::getProfileBackgroundUrl($user_eid);

        return $result;
    }

    public static function croppicture($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'    => array('type' => 'eid', 'required' => true),
                'type'   => array('type' => 'string', 'required' => true),
                'x'      => array('type' => 'integer', 'required' => true),
                'y'      => array('type' => 'integer', 'required' => true),
                'width'  => array('type' => 'integer', 'required' => true),
                'height' => array('type' => 'integer', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $eid = $params['eid'];
        $type = $params['type'];
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
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER);
        }

        $data = \System::getModel()->registry->getBinary($eid, $type, $mime_type);
        if (is_null($data))
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        $src_img = @imagecreatefromstring($data);
        if ($src_img === false)
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER);

        $src_x = $params['x'];
        $src_y = $params['y'];
        $src_w = $params['width'];
        $src_h = $params['height'];

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

        //\System::getModel()->registry->setBinary($eid, $type, $data, null, 'image/png');
        \System::getModel()->registry->setBinary($eid, $type, $data, null, 'image/jpeg');

        return true;
    }

    public static function getProfilePictureUrl($user_eid)
    {
        if (!\System::getModel()->registry->entryExists($user_eid, 'profile.picture'))
            return false;

        return '/api/v1/users/' . $user_eid . '/profilepicture';
    }

    public static function getProfileBackgroundUrl($user_eid)
    {
        if (!\System::getModel()->registry->entryExists($user_eid, 'profile.background'))
            return false;

        return '/api/v1/users/' . $user_eid . '/profilebackground';
    }
}
