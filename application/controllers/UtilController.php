<?php
/**
 *
 * Copyright (c) 2009-2012, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2012-04-12
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Controllers;


class UtilController extends \Flexio\System\FxControllerAction
{
    public function init() : void
    {
        parent::init();
    }

    public function indexAction() : void
    {
        // render the page
        $this->render();
    }

    public function generatespriteAction() : void
    {
        // render the page
        $this->renderRaw();

        $srcdir = '/src/img';
        $destfile = '/src/img/sprite.png';
        $cssdestfile = '/src/less/sprites.less';
        $sprite_w = 200;
        $sprite_o = 0.3;
        $max_size = 127;
        $exclude = '';
        $include = '';

        if (isset($_GET['srcdir']) && strlen($_GET['srcdir']) > 0)
            $srcdir = $_GET['srcdir'];
        if (isset($_GET['destfile']) && strlen($_GET['destfile']) > 0)
            $destfile = $_GET['destfile'];
        if (isset($_GET['cssdestfile']) && strlen($_GET['cssdestfile']) > 0)
            $cssdestfile = $_GET['cssdestfile'];
        if (isset($_GET['width']) && strlen($_GET['width']) > 0)
            $sprite_w = $_GET['width'];
        if (isset($_GET['opacity']) && strlen($_GET['opacity']) > 0)
            $sprite_o = $_GET['opacity'];
        if (isset($_GET['maxsize']) && strlen($_GET['maxsize']) > 0)
            $max_size = $_GET['maxsize'];
        // explicit exclude (only overridden by 'include')
        if (isset($_GET['exclude']) && strlen($_GET['exclude']) > 0)
            $exclude = $_GET['exclude'];
        // explicit include (overrides everything)
        if (isset($_GET['include']) && strlen($_GET['include']) > 0)
            $include = $_GET['include'];

        // calculate opacity (hex value) for IE8
        $sprite_ie_o = dechex((1-$sprite_o) * 255);

        $base_dir = \Flexio\System\System::getBaseDirectory();
        $full_srcdir = $base_dir.DIRECTORY_SEPARATOR.'public'.$srcdir;

        // get all files in the following folders
        $files = $this->getAllFiles($full_srcdir);
        $files = $this->cleanFileList($files);

        $file_objs = array();
        foreach ($files as $f)
        {
            $filename = basename($f);
            $filesize = filesize($f);
            $attrs = getimagesize($f);
            $mime = $attrs[2];

            $exclude_match = false;
            $include_match = false;

            // don't include files we explicitly don't want
            if (strlen($exclude) > 0)
            {
                $exclude_strs = explode(',', $exclude);

                foreach ($exclude_strs as $s)
                {
                    if (strpos($filename, $s) !== false)
                        $exclude_match = true;
                }
            }

            // make sure we include files we explicitly want
            if (strlen($include) > 0)
            {
                $include_strs = explode(',', $include);

                foreach ($include_strs as $s)
                {
                    if (strpos($filename, $s) !== false)
                        $include_match = true;
                }
            }

            if (!$exclude_match || $include_match)
            {
                $w = $attrs[0];
                $h = $attrs[1];

                $meets_constraints = false;

                // only sprite small png files
                if (strpos($filename, 'sprite') === false &&
                    $w <= $max_size &&
                    $h <= $max_size &&
                    $filesize < 10000 &&
                    $mime == IMAGETYPE_PNG)
                {
                    $meets_constraints = true;
                }

                if ($include_match || $meets_constraints)
                {
                    // make sure the sprite width is at least as wide
                    // as the widest image we're going to add to it
                    if ($w > $sprite_w)
                        $sprite_w = $w;

                    $file_objs[] = array(
                        'file' => $f,
                        'filename' => $filename,
                        'w' => $w,
                        'h' => $h
                    );
                }
            }
        }

        // sort the file object array by image dimensions
        usort($file_objs, __CLASS__ . '::sortFileObjectsByDimensions');

        $x_off = 0;
        $y_off = 0;
        $last_w = 0;
        $row_h = 0;
        $sprite_h = 0;
        foreach ($file_objs as &$fo)
        {
            $x_off += $last_w;
            if (($x_off + $fo['w']) > $sprite_w)
            {
                $x_off = 0;
                $y_off += $row_h;
                $row_h = 0;
            }

            $fo['x'] = $x_off;
            $fo['y'] = $y_off;

            $last_w = $fo['w'];
            if ($fo['h'] > $row_h)
                $row_h = $fo['h'];

            // calculate the overall sprite height
            if (($fo['y'] + $fo['h']) > $sprite_h)
                $sprite_h = $fo['y'] + $fo['h'];
        }

        // write the source images to the sprite image
        $css_text = <<<EOT
.fx-img {
    display: inline-block;
    width: 1px;
    height: 1px;
    vertical-align: text-top;
    background: transparent;
    background-image: url($destfile);
    background-position: 0 0;
    background-repeat: no-repeat;
}
.fx-img-spinner-sm {
    width: 16px;
    height: 16px;
    background-image: url(/src/img/spinner-16.gif);
}
.fx-img-toggle {
    opacity: $sprite_o;
    cursor: pointer;
}
.fx-img-toggle.fx-img-active {
    opacity: 1;
}
.ie8 .fx-img-toggle {
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#{$sprite_ie_o}FFFFFF,endColorstr=#{$sprite_ie_o}FFFFFF)"; /* IE8 */
    zoom: 1;
}
.ie8 .fx-img-toggle.fx-img-active {
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#00FFFFFF,endColorstr=#00FFFFFF)"; /* IE8 */
}\n
EOT;

        // create CSS text to ouput on this page
        $css_page_text = "";

        // create a 24-bit PNG
        $dest_img = imagecreatetruecolor($sprite_w, $sprite_h);

        // make sure the background is transparent
        imagesavealpha($dest_img, true);
        $trans_color = imagecolorallocatealpha($dest_img, 255, 0, 255, 127);
        imagefill($dest_img, 0, 0, $trans_color);

        foreach ($file_objs as &$fo)
        {
            $x = $fo['x'];
            $y = $fo['y'];
            $w = $fo['w'];
            $h = $fo['h'];
            $css_suffix = substr($fo['filename'], 0, strrpos($fo['filename'], '.'));
            $src_img  = imagecreatefrompng($fo['file']);

            $css_text .= ".fx-img-$css_suffix { background-position: -{$x}px -{$y}px; width: {$w}px; height: {$h}px; }\n";
            $css_page_text .= ".fx-img-$css_suffix { background-position: -{$x}px -{$y}px; width: {$w}px; height: {$h}px; }<br>";

            imagecopyresampled($dest_img, $src_img, $x, $y, 0, 0, $w, $h, $w, $h);
            imagedestroy($src_img);
        }

        // write out the LESS and CSS files
        $less_destfile = $base_dir.DIRECTORY_SEPARATOR.'public'.$cssdestfile;
        file_put_contents($less_destfile, $css_text);

        // create the sprite image file
        $full_destfile = $base_dir.DIRECTORY_SEPARATOR.'public'.$destfile;
        imagepng($dest_img, $full_destfile);
        imagedestroy($dest_img);

        $img_filesize = filesize($full_destfile);

        $output = <<<EOT
<h2>All image files in '$srcdir' have been combined together as an image sprite in '$destfile'</h2>
<p>The sprite file has been saved to <a href="$cssdestfile">$cssdestfile.</a></p>
<p>The following image sprite has been saved to <a href="$destfile">$destfile</a> and is $img_filesize bytes in size</a>:</p>
<img src="$destfile"></img>
<p style="font-family: monospace">$css_page_text</p>
EOT;

        echo $output;
    }

    public function generatebase64pngAction() : void
    {
        // render the page
        $this->renderRaw();

        $srcdir = 'img';
        if (isset($_GET['srcdir']) && strlen($_GET['srcdir']) > 0)
            $srcdir = $_GET['srcdir'];

        $base_dir = \Flexio\System\System::getBaseDirectory();
        $full_srcdir = $base_dir.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$srcdir;

        // get all files in the following folders
        $files = $this->getAllFiles($full_srcdir);
        $files = $this->cleanFileList($files);

        $output = "<h2>All PNG files in \"/$srcdir\" encoded using base 64 encoding</h2>";

        foreach ($files as $file)
        {
            $filename = basename($file);
            if (strpos($filename, '.png') === false)
                continue;
            if (strpos($filename, 'sprite') !== false)
                continue;

            $css_class = '.fx-' . substr($filename, 0, strrpos($filename, '.'));
            $base64_str = $this->getBase64DataUri($file, 'image/png');
            $png64_str = "background: transparent url($base64_str) top left no-repeat;";

            $output .= <<<EOT
<div style="padding-bottom: 1em; margin-bottom: 1em; border-bottom: 1px solid #999999">
    <h3>$filename</h3></img><pre>$css_class { $png64_str }</pre>
    <img src="/$srcdir/$filename">
</div>
EOT;
        }

        echo $output;
    }

    public function findtrailingcommasAction() : void
    {
        // render the page
        $this->renderRaw();

        $app_dir = \Flexio\System\System::getApplicationDirectory();
        //$include_dir = $app_dir . DIRECTORY_SEPARATOR . 'include';
        $views_dir = $app_dir . DIRECTORY_SEPARATOR . 'views';

        // get all files in the following folders
        //$include_files = $this->getAllFiles($include_dir, true);
        $views_files = $this->getAllFiles($views_dir, true);

        // pare the list down
        //$include_files = $this->cleanFileList($include_files);
        $views_files = $this->cleanFileList($views_files);

        // create views object
        $views_obj = (object)'';
        $views_obj->title = "Files in '/views' folder";
        $views_obj->files = $views_files;

        // create output groups
        $output_groups = array();
        $output_groups[] = $views_obj;

        $output = '';

        foreach ($output_groups as $group)
        {
            $group_title = $group->title;

            $output .= "<h2>$group_title</h2><ol>";

            foreach ($group->files as $file)
            {
                $new_file = $this->findTrailingCommas($file);

                $pos = stripos($new_file, 'TRAILING COMMA ALERT');
                if ($pos !== false)
                    $found = true;
                     else
                    $found = false;

                if ($found)
                {
                    $file_name = substr($file, strlen($app_dir));
                    $html_entities = htmlentities($new_file);

                    $output .= <<<EOT
<li style="padding-bottom: 2em; margin-bottom: 2em; border-bottom: 1px solid #999999">
    <h3>$file_name</h3>
    <pre>
        $html_entities
    </pre>
</li>
EOT;
                }
            }

            $output .= '</ol>';

            next($output_groups);
        }

        echo $output;
    }

    private function getBase64DataUri($file, $mime) {
        $contents = file_get_contents($file);
        $base64 = base64_encode($contents);
        return "data:$mime;base64,$base64";
    }

    private function findTrailingCommas(string $file) : string
    {
        $notice = <<<EOT
\n---------- TRAILING COMMA ALERT ---------- TRAILING COMMA ALERT ---------- TRAILING COMMA ALERT ---------- TRAILING COMMA ALERT ---------- TRAILING COMMA ALERT ----------\n
EOT;

        // get the file contents
        $result = file_get_contents($file);

        // find trailing commas
        $result = preg_replace("/(,\\s*?\n*?[\\}\\]])/", "$1$notice", $result);
        return $result;
    }

    private function getAllFiles(string $dir, bool $recursive = false, bool $include_dirs = false) : array
    {
        $output = array();

        if ($handle = opendir($dir))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != '.' && $file != '..')
                {
                    if (is_dir($dir . '/' . $file))
                    {
                        if ($recursive)
                            $output = array_merge($output, $this->getAllFiles($dir. '/' . $file, $recursive));

                        if ($include_dirs)
                        {
                            $file = $dir . '/' . $file;
                            $output[] = preg_replace('/\\/\\//si', '/', $file);
                        }
                    }
                     else
                    {
                        $file = $dir . '/' . $file;
                        $output[] = preg_replace('/\\/\\//si', '/', $file);
                    }
                }
            }

            closedir($handle);
        }

        return $output;
    }

    private function cleanFileList(array $files) : array
    {
        foreach ($files as $i => $file)
        {
            // don't include hidden metadata files
            if (strpos($file, ".DS_Store") !== false)
                unset($files[$i]);

            // don't include subversion files
            if (strpos($file, ".svn") !== false)
                unset($files[$i]);
        }

        return $files;
    }

    // file object array sort function
    private static function sortFileObjectsByDimensions(array $a, array $b) : bool
    {
        // sort by filename if image dimensions are the same
        if ($a['h'] == $b['h'] && $a['w'] == $b['w'])
            return strcmp($a['filename'], $b['filename']);

        // sort by height if image widths are the same
        if ($a['w'] == $b['w'])
            return $a['h'] < $b['h'];

        // sort by image width
        return $a['w'] < $b['w'];
    }
}
