<?php
/**
 *
 * Copyright (c) 2009-2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams
 * Created:  2017-04-06
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Image
{
    public static function createThumbnail(string $input_path, string $output_path, int $new_max_size = 256)
    {
        if (!function_exists('imagecreatefrompng'))
            return false; // gd library is not available

        if (!file_exists($input_path))
            return false;

        if (!is_writeable(dirname($output_path)))
            return false;

        // get input file image info
        $image_info = $image_info = @getimagesize($input_path);
        list($width_orig, $height_orig, $image_type) = $image_info;

        if ($width_orig == 0 || $height_orig == 0)
            return false;

        // determine output dimensions
        if ($width_orig > $height_orig)
        {
            $width_new = $new_max_size;
            $height_new = floor((($width_new / $width_orig) * $height_orig));
        }
         else
        {
            $height_new = $new_max_size;
            $width_new = floor((($height_new / $height_orig) * $width_orig));
        }

        switch ($image_type)
        {
            case IMAGETYPE_GIF:  $image_orig = imagecreatefromgif($input_path); break;
            case IMAGETYPE_JPEG: $image_orig = imagecreatefromjpeg($input_path); break;
            case IMAGETYPE_PNG:  $image_orig = imagecreatefrompng($input_path); break;
            default: return false; // unsupported image type
        }

        $image_new = imagecreatetruecolor($width_new, $height_new);
        imagealphablending($image_new, false);
        imagesavealpha($image_new, true);

        imagecopyresampled($image_new, $image_orig,
                           0, 0, 0, 0,
                           $width_new, $height_new,
                           $width_orig, $height_orig);

        $res = imagepng($image_new, $output_path, 9);

        imagedestroy($image_orig);
        imagedestroy($image_new);

        return $res;
    }
}
