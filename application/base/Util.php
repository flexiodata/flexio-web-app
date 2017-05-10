<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams
 * Created:  2009-03-31
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Util
{
    public static function rmtree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file)
        {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $file))
                self::rmtree($dir . DIRECTORY_SEPARATOR . $file);
                 else
                unlink($dir . DIRECTORY_SEPARATOR . $file);
        }

        return rmdir($dir);
    }

    public static function beforeFirst(string $str, string $search) : string
    {
        if (strlen($search) == 0)
            return $str;
        if ($str === $search)
            return '';

        $pos = strpos($str, $search);
        if ($pos === false)
            return $str;

        $chunk = substr($str, 0, $pos);
        if ($chunk === false)
            return '';

        return $chunk;
    }

    public static function afterFirst(string $str, string $search) : string
    {
        if (strlen($search) == 0)
            return $str;
        if ($str === $search)
            return '';

        $pos = strpos($str, $search);
        if ($pos === false)
            return $str;

        $chunk = substr($str, $pos+strlen($search));
        if ($chunk === false)
            return '';

        return $chunk;
    }

    public static function beforeLast(string $str, string $search) : string
    {
        if (strlen($search) == 0)
            return $str;
        if ($str === $search)
            return '';

        $pos = strrpos($str, $search);
        if ($pos === false)
            return $str;

        $chunk = substr($str, 0, $pos);
        if ($chunk === false)
            return '';

        return $chunk;
    }

    public static function afterLast(string $str, string $search) : string
    {
        if (strlen($search) == 0)
            return $str;
        if ($str === $search)
            return '';

        $pos = strrpos($str, $search);
        if ($pos === false)
            return $str;

        $chunk = substr($str, $pos+strlen($search));
        if ($chunk === false)
            return '';

        return $chunk;
    }

    public static function lpad(string $str, int $n, string $ch = ' ') : string
    {
        return str_pad($str, $n, $ch, STR_PAD_LEFT);
    }

    public static function rpad(string $str, int $n, string $ch = ' ') : string
    {
        return str_pad($str, $n, $ch, STR_PAD_RIGHT);
    }

    public static function zlstrpos(string $haystack, string $needle, int $offset = 0) // TODO: add function return type
    {
        if ($needle == '')
            return false;
        $haystack_len = strlen($haystack);
        $needle_len = strlen($needle);
        $firstch = $needle[0];

        $quotec = false;
        while (true)
        {
            if ($offset >= $haystack_len)
                return false;
            $ch = $haystack[$offset];
            if ($ch == "\\")
            {
                $offset += 2;
                continue;
            }

            if ($ch == '"')
            {
                if ($quotec !== false)
                    $quotec = false;
                     else
                    $quotec = $ch;

                $offset++;
                continue;
            }

            if ($quotec === false && $ch == $firstch)
            {
                if (substr($haystack, $offset, $needle_len) == $needle)
                    break;
            }

            ++$offset;
        }

        return $offset;
    }

    private static function json_strpos(string $haystack, string $needle, int $offset = 0) // TODO: add function return type
    {
        if ($needle == '')
            return false;
        $haystack_len = strlen($haystack);
        $needle_len = strlen($needle);
        $brace_count = 0;
        $bracket_count = 0;
        $firstch = $needle[0];

        $quotec = false;
        while (true)
        {
            if ($offset >= $haystack_len)
                return false;
            $ch = $haystack[$offset];
            if ($ch == "\\")
            {
                $offset += 2;
                continue;
            }

            if ($ch == '"')
            {
                if ($quotec !== false)
                    $quotec = false;
                     else
                    $quotec = $ch;

                $offset++;
                continue;
            }

            if ($quotec === false)
            {
                if ($ch == '[') $bracket_count++;
                if ($ch == ']') $bracket_count--;
                if ($ch == '{') $brace_count++;
                if ($ch == '}') $brace_count--;
            }


            if ($quotec === false && $bracket_count == 0 && $brace_count == 0 && $ch == $firstch)
            {
                if (substr($haystack, $offset, $needle_len) == $needle)
                    break;
            }


            ++$offset;
        }

        return $offset;
    }

    public static function isAssociativeArray(array $arr) : bool
    {
        // tests whether or  not an array is sequential or associative;
        // (e.g. ["a","b","c"] or ["k1"=>"a","k2"=>"b","k3"=>"c"])

        if (count($arr) === 0)
            return false;

        $is_associative = array_keys($arr) !== range(0, count($arr) - 1);
        return $is_associative;
    }

    public static function diff(array $array1, array $array2) : array
    {
        // note: this function takes two arrays and finds the difference
        // between them and reports the results similarly to how a text
        // diff program would, with + and - for the insertions/deletions;
        // for example, if we have the following:
        //     Util::diff(array('b','c','d'),array('a','b','c','e'))
        // we get:
        //     [{"+":"a"},{"=":"b"},{"=":"c"},{"-":"d"},{"+":"e"}]

        // handle two empty arrays
        if (empty($array1) && empty($array2))
            return array();

        $matrix = array();
        $max_length = 0;
        $offset1 = 0;
        $offset2 = 0;

        // iterate over array1
        foreach ($array1 as $index1 => $value1)
        {
            // iterate over the indexes of array1 who's values are the
            // same values as the value of the current index in array2
            $indexes2 = array_keys($array2, $value1);
            foreach ($indexes2 as $index2)
            {
                // we have a common value; if the value before it was a common value,
                // increment the count for this combination, indicating the total
                // length of the common sequence to this point; if the value before
                // the match doesn't match, it's the first in the sequence, so start
                // with 1
                $matrix[$index1][$index2] = isset($matrix[$index1-1][$index2-1]) ?
                                            $matrix[$index1-1][$index2-1] + 1 : 1;

                $current_length = $matrix[$index1][$index2];
                if ($current_length > $max_length)
                {
                    // if the length of the sequence is greater than
                    // what we've encountered before, save the sequence
                    // length and the offsets
                    $max_length = $current_length;
                    $offset1 = $index1 - $max_length + 1;
                    $offset2 = $index2 - $max_length + 1;
                }
            }
        }

        // if the maximum length is zero, there's nothing in common
        // between the two arrays, so return a merged array of the
        // elements in array1, followed by the elements in array2
        if ($max_length == 0)
        {
            $result_array = array();
            foreach ($array1 as $a)
                $result_array[] = array('-' => $a);
            foreach ($array2 as $a)
                $result_array[] = array('+' => $a);

            return $result_array;
        }

        // save array of matches
        $match_array = array_slice($array2, $offset2, $max_length);

        // convert each of the matches in the match array to an
        // array of objects denoted with an equal sign; this will
        // allow the final result to be uniformly displayed with
        // additions and deletions
        $result_array = array();
        foreach ($match_array as $m)
        {
            $result_array[] = array('=' => $m);
        }

        // we found a sequence; return the sequence plus the sequences
        // for the parts that have smaller lengths
        return array_merge(
            self::diff(array_slice($array1, 0, $offset1), array_slice($array2, 0, $offset2)),
            $result_array,
            self::diff(array_slice($array1, $offset1 + $max_length), array_slice($array2, $offset2 + $max_length))
        );
    }

    public static function filterChars(string $str, string $allowed_chars) : string
    {
        $result = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i)
        {
            $ch = substr($str, $i, 1);
            if (strpbrk($allowed_chars, $ch) !== false)
                $result .= $ch;
        }
        return $result;
    }

    public static function filterAlphaNumeric(string $str, string $additional_allowed = '') : string
    {
        $result = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i)
        {
            $ch = substr($str, $i, 1);
            if (ctype_alnum($ch) || strpbrk($additional_allowed, $ch) !== false)
                $result .= $ch;
        }
        return $result;
    }

    public static function filterDigits(string $str, string $additional_allowed = '') : string
    {
        $result = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i)
        {
            $ch = substr($str, $i, 1);
            if (ctype_digit($ch) || strpbrk($additional_allowed, $ch) !== false)
                $result .= $ch;
        }
        return $result;
    }

    public static function mapArray(array $arr1, array $arr2) : array
    {
        // returns an array with the values of $arr2 mapped onto $arr1 based
        // on keys; if $arr2 has keys that aren't in $arr1, they are excluded
        // from the output; if $arr1 has keys that aren't in $arr2, the key/value
        // pair as included as it exists in $arr1

        $result = array();
        foreach ($arr1 as $key => $value)
        {
            $result[$key] = $arr2[$key] ?? $value;
        }

        return $result;
    }

    public static function filterArray(array $arr, array $allowed_keys) : array
    {
        return array_intersect_key($arr, array_flip($allowed_keys));
    }

    public static function filterArrayEmptyValues(array $arr) : array
    {
        return array_filter($arr, 'strlen');
    }

    public static function removeArrayKeys(array $arr, array $remove_keys) : array
    {
        return array_diff_key($arr, array_flip($remove_keys));
    }

    public static function filterArrayofArrays(array $arr, array $allowed_keys) : array
    {
        $result = array();
        foreach ($arr as $a)
        {
            $result[] = array_intersect_key($a, array_flip($allowed_keys));
        }
        return $result;
    }

    public static function formatNumber(float $num, int $dec = 0) : string
    {
        global $g_store;
        return number_format($num, $dec, $g_store->decimal_separator, $g_store->thousands_separator);
    }

    public static function formatDate(string $date) : string
    {
        $datetime = new \DateTime($date);
        return $datetime->format(\DateTime::ISO8601);
    }

    public static function formatDateDiff(string $date1 = null, string $date2 = null) // TODO: set function return type
    {
        // returns the difference between two datetimes in seconds (including
        // fractions of a second)

        if (!isset($date1) || !isset($date2))
            return null;

        // get the difference between two dates, excluding the microtime portion
        $d1 = \DateTime::createFromFormat("Y-m-d H:i:s.u", $date1);
        $d2 = \DateTime::createFromFormat("Y-m-d H:i:s.u", $date2);

        if ($d1 === false || $d2 === false)
            return null;

        $t1 = floor($d1->getTimestamp()); // use floor to make sure the microtime portion wasn't included
        $t2 = floor($d2->getTimestamp()); // use floor to make sure the microtime portion wasn't included

        // get any microtime portion of hte date
        $mt1 = 0;
        $period_location = strpos($date1, '.');
        if ($period_location !== false)
            $mt1 = (float)substr($date1, $period_location);

        $mt2 = 0;
        $period_location = strpos($date2, '.');
        if ($period_location !== false)
            $mt2 = (float)substr($date2, $period_location);

        // return the time difference
        return ($t2 + $mt2) - ($t1 + $mt1);
    }

    // sorts an array in ascending order by a specified field
    public static function sortByFieldAsc(array &$arr, string $field) // TODO: set function return type
    {
        $code="if (\$a['$field'] == \$b['$field']) return 0;".
            "return (\$a['$field'] < \$b['$field']) ? -1 : 1;";
        usort($arr, create_function('$a,$b',$code));
    }

    // sorts an array in descending order by a specified field
    public static function sortByFieldDesc(array &$arr, string $field) // TODO: set function return type
    {
        $code="if (\$a['$field'] == \$b['$field']) return 0;".
            "return (\$a['$field'] > \$b['$field']) ? -1 : 1;";
        usort($arr, create_function('$a,$b',$code));
    }

    public static function getDaysDiff(string $dt1, string $dt2) // TODO: set function return type
    {
        // note: input are two strings of form YYYY-MM-DD
        $time1 = strtotime($dt1);
        $time2 = strtotime($dt2);

        $seconds_diff = $time2 - $time1; // allow time difference to be negative
        $days_diff = floor($seconds_diff/(24*60*60));

        return $days_diff;
    }

    /* // works, but currently not in use
    public static function getCurrentTimezoneOffset()
    {
        $tz1 = new \DateTimeZone($GLOBALS['g_store']->timezone);
        $dt = new \DateTime('now');
        $off = $tz1->getOffset($dt);
        $sign = ($off < 0 ? '-' : '+');
        $off = abs($off);
        $minutes = $off / 60;
        $hours = floor($minutes / 60);
        $minutes -= ($hours*60);
        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }
    */

    public static function isPositiveInteger($value) : bool
    {
        // verifies if an id is a positive integer
        $value_pure = (int)$value;
        if (!is_int($value_pure) || $value_pure <= 0)
            return false;

        return true;
    }

    public static function isValidDateTime($value) : bool
    {
        // verifies if the value is a valid ISO 8601 datetime
        if (!is_string($value))
            return false;

        // TODO: the \DateTime::createFromFormat() can't handle certain
        // types of date time formats allowed by the standard (i.e, partial
        // times given by ".ttt" in the following: YYYY-MM-DDThh:mm:ss.tttZ)
        $dt = \DateTime::createFromFormat(\DateTime::ISO8601, $value);
        if ($dt !== false)
            return true;

        return false;
    }

    public static function isValidEmail($value) : bool
    {
        // verifies if a value is a valid email
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_EMAIL);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidHostName($value) : bool
    {
        // verifies if a value is a valid hostname, per RFC 1034, section 3.1
        if (!is_string($value))
            return false;

        // try to match on a series of labels concatenated with periods where
        // each label is 1) composed of uppercase or lowercase characters,
        // numbers, or hyphens, 2) doesn't begin or end with a hypen, 3) between
        // 1 and 63 chars, 4) overall length of the value is between 1 and 253
        // chars, not including any final period

        // make sure the overall length is less than or equal to 253 characters,
        // excluding any trailing period
        $clean_value = rtrim($value, '.');
        if (strlen($clean_value) > 253)
            return false;

        // match the basic pattern
        $regex = "/^([a-z0-9](-*[a-z0-9])*\.?)+$/i";
        if (preg_match($regex, $value) === 0)
            return false;

        // make sure the individual labels are the correct length
        $regex = "/^[^\.]{1,63}(\.[^\.]{1,63})*\.?$/";
        if (preg_match($regex, $value) === 0)
            return false;

        // passed the filters, so it matches
        return true;
    }

    public static function isValidIPV4($value) : bool
    {
        // verifies if a value is a valid ipv4 address
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidIPV6($value) : bool
    {
        // verifies if a value is a valid ipv4 address
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidUrl($value) : bool
    {
        // verifies if a value is a valid url
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_URL);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidPassword($value) : bool
    {
        // make sure a password is a string that's a minimum
        // of 8 characters
        if (!is_string($value))
            return false;

        if (strlen($value) < 8)
            return false;

        // make sure we have at least one number
        $regex = "/[0-9]/";
        if (preg_match($regex, $value) === 0)
            return false;

        return true;
    }

    public static function generateRandomString(int $length) : string
    {
        $result = '';
        $chars = 'bcdfghjkmnpqrstvwxyz';  // characters to draw from
        $charcount = strlen($chars);

        for ($i = 0; $i < $length; ++$i)
        {
            $ch = random_int(0,$charcount-1);
            $result .= $chars[$ch];
        }

        return $result;
    }

    public static function generateHandle() : string
    {
        return self::generateRandomString(20);
    }

    public static function generatePassword() : string
    {
        $pw = self::generateRandomString(10);
        $pw[2] = ''.random_int(0, 9);
        $pw[6] = ''.random_int(0, 9);
        $i = random_int(0, 9);
        if ($i == 2 || $i == 6)
            $i--;
        $pw[$i] = strtoupper($pw[$i]);

        return $pw;
    }

    public static function encrypt(string $plaintext, string $key = null) // TODO: set function return type
    {
        require_once dirname(dirname(__DIR__)) . '/library/sodium_compat/autoload.php';

        $nonce = str_repeat("\x0", SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // if key is null or empty string, key will be all \0's
        if (is_null($key) || strlen($key) == 0)
            $key = "\0";

        // if key is less than the bytes required, repeat it;
        // if it's longer, chop it off

        while (strlen($key) < SODIUM_CRYPTO_SECRETBOX_KEYBYTES)
            $key = $key . $key;
        $key = substr($key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);

        try
        {
            $enc = sodium_crypto_secretbox($plaintext, $nonce, $key);
        }
        catch (\Error $e)
        {
            return null;
        }

        return 'ZZXV2/'.base64_encode($enc);
    }

    public static function decrypt(string $ciphertext, string $key = null) // TODO: set function return type
    {
        require_once dirname(dirname(__DIR__)) . '/library/sodium_compat/autoload.php';

        if (substr($ciphertext,0,5) == 'ZZXV1')
            return self::oldDecrypt($ciphertext, $key);

        if (substr($ciphertext,0,6) != 'ZZXV2/')
            return null;

        $ciphertext = base64_decode(substr($ciphertext,6));
        if ($ciphertext === false)
            return null;

        // if key is null or empty string, key will be all \0's
        if (is_null($key) || strlen($key) == 0)
            $key = "\0";

        // if key is less than the bytes required, repeat it;
        // if it's longer, chop it off

        while (strlen($key) < SODIUM_CRYPTO_SECRETBOX_KEYBYTES)
            $key = $key . $key;
        $key = substr($key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);

        try
        {
            $nonce = str_repeat("\x0", SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        }
        catch (\Error $e)
        {
            return null;
        }
    }

    public static function oldEncrypt(string $string, string $key) : string
    {
        if ($key == '') // empty keys not allowed in mcrypt
            $key = 'A';

        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, substr($key,0,8), $iv);
        $enc = @mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return 'ZZXV1'.base64_encode($enc);
    }

    public static function oldDecrypt(string $string, string $key) : string
    {
        if ($key == '') // empty keys not allowed in mcrypt
            $key = 'A';

        if (substr($string,0,5) != 'ZZXV1')
            return '';

        $string = base64_decode(substr($string,5));
        if (strlen($string) === 0)
            return '';

        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, substr($key,0,8), $iv);
        $data = @mdecrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return rtrim($data, "\0");
    }

    public static function appendUrlPath(string $url, string $part) : string
    {
        if (strlen($url) == 0)
            return $part;
        if (strlen($part) == 0)
            return $url;
        $res = $url;
        if ($res[strlen($res)-1] != '/')
            $res .= '/';
        if ($part[0] == '/')
            $res .= substr($part,1);
             else
            $res .= $part;
        return $res;
    }

    // mode should be either 'inline' or 'download'
    public static function headersPdf(string $output_filename, string $file_location, string $mode = 'inline') : bool
    {
        if ($mode != 'inline' && $mode != 'download')
            return false;

        if (stripos($_SERVER['HTTP_USER_AGENT'], 'bot') !== false)
            die('Invalid credentials.  Failure.');

        if ($mode == 'inline')
        {
            // next two lines solve problem with inline pdf+https in IE
            header('Content-Type: application/pdf');
            header('Content-Length: '. filesize($file_location));
            header('Pragma: public');
            header('Cache-Control:  maxage=1');
            header('Content-Disposition: inline; filename="'.$output_filename.'"');
        }
         else
        {
            header('Content-Type: application/pdf');
            header('Content-Length: '. filesize($file_location));
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);

            //$ie = (stripos($agent, 'win') !== false && stripos($agent, 'msie') !== false) ? true : false;
            //if ($ie)
            //    header('Content-Disposition: filename="'.$output_filename.'"');
            //     else
            header('Content-Disposition: attachment; filename="'.$output_filename.'"');
        }

        return true;
    }

    public static function headersCsv(string $output_filename) : bool
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if (stripos($agent, 'bot') === false)
        {
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: text/csv');

            if (stripos($agent, 'win') !== false && stripos($agent, 'msie') !== false)
                header('Content-Disposition: filename="' . $output_filename . '"');
                 else
                header('Content-Disposition: attachment; filename="' . $output_filename . '"');
        }
         else
        {
            die('Invalid credentials.  Failure.');
        }

        return true;
    }

    public static function header_error(int $code, string $text = null) : int
    {
        switch ($code) {
            default:
            case 400: $text = $text ?? 'Bad Request'; break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . (string)$code . ' ' . $text);

        return $code;
    }
}


class StopWatch
{
    var $start_time;
    var $label;

    function __construct($label)
    {
        $this->start_time = microtime(true);
        $this->label = $label;
    }

    function __destruct()
    {
        $diff = microtime(true) - $this->start_time;
        fbLog($this->label . ' took ' . $diff . ' seconds.');
    }
}
