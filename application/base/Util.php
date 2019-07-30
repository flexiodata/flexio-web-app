<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie LLC. All rights reserved.
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
    public static function rmtree(string $dir) : bool
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

    public static function zlstrpos(string $haystack, string $needle, int $offset = 0) // TODO: add return type
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

    private static function json_strpos(string $haystack, string $needle, int $offset = 0) // TODO: add return type
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

    public static function array_new_items(array $array1, array $array2, array $keys) : array
    {
        // takes two sequential arrays of key/value arrays; returns the
        // array rows in array1 that aren't in array2 based on the value
        // of the supplied keys

        // TODO: right now, algorithm is o(n2); fine for small array comparisons

        // create an array of keys where the keys are the actual keys
        $array_keys = array_flip($keys);

        // iterate over the rows in array 1 and see if the row is in array2
        $result = array();
        foreach ($array1 as $a1)
        {
            // get the key/values in for the row we want to compare
            $a1_subset = array_intersect_key($a1, $array_keys);

            $found = false;
            foreach ($array2 as $a2)
            {
                // get the key/values in for the row we want to compare
                $a2_subset = array_intersect_key($a2, $array_keys);

                // see if the row in array1 is in array2 based on a key value comparison
                // without regard to order or type
                if ($a1_subset == $a2_subset)
                {
                    $found = true;
                    break;
                }
            }

            if ($found === false)
                $result[] = $a1;
        }

        return $result;
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
        $decimal_separator = '.';
        $thousands_separator = ',';
        return number_format($num, $dec, $decimal_separator, $thousands_separator);
    }

    public static function formatDate(string $date = null) : string
    {
        if (!isset($date))
            return '';

        $datetime = new \DateTime($date);
        return $datetime->format(\DateTime::ISO8601);
    }

    public static function formatDateDiff(string $date1 = null, string $date2 = null) : ?float
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
    public static function sortByFieldAsc(array &$arr, string $field) : array
    {
        usort($arr, function($a, $b) use ($field) {
            if ($a[$field] == $b[$field]) return 0;
            return ($a[$field] < $b[$field]) ? -1 : 1;
        });

        return $arr;
    }

    // sorts an array in descending order by a specified field
    public static function sortByFieldDesc(array &$arr, string $field) : array
    {
        usort($arr, function($a, $b) use ($field) {
            if ($a[$field] == $b[$field]) return 0;
            return ($a[$field] > $b[$field]) ? -1 : 1;
        });

        return $arr;
    }

    public static function getDaysDiff(string $dt1, string $dt2) : float
    {
        // note: input are two strings of form YYYY-MM-DD
        $time1 = strtotime($dt1);
        $time2 = strtotime($dt2);

        $seconds_diff = $time2 - $time1; // allow time difference to be negative
        $days_diff = floor((float)$seconds_diff/(24*60*60));

        return $days_diff;
    }

    public static function getDateTimeParts(int $t = null, string $tz = null) : array
    {
        if (is_null($tz))
            $tz = date_default_timezone_get();

        $dt = new \DateTime('now', new \DateTimeZone($tz));
        $dt->setTimestamp(is_null($t) ? time() : $t);
        $s = $dt->format('s:i:G:j:w:n:Y:z:l:w:F:U');

        $k = array('seconds','minutes','hours','mday','wday','mon','year','yday','weekday','nweekday','month',0);

        return array_combine($k, explode(":", $s));
    }

    public static function getCurrentTimestamp() : string
    {
        // return the timestamp as accurately as we can determine
        $time_exact = microtime(true);
        $time_rounded = floor($time_exact);
        $time_micropart = sprintf("%06d", ($time_exact - $time_rounded) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, (int)$time_rounded));
        return ($date->format("Y-m-d H:i:s.u"));
    }

    public static function getCurrentTimezoneOffsetInMinutes(string $local_timezone) : float
    {
        $tz_utc = new \DateTimeZone('UTC');
        $tz_local = new \DateTimeZone($local_timezone);
        $dt_local = new \DateTime('now', $tz_utc);
        return (float)($tz_local->getOffset($dt_local) / 60);
    }

    public static function getCurrentTimezoneOffset(string $local_timezone) : string
    {
        $tz1 = new \DateTimeZone($local_timezone);
        $dt = new \DateTime('now');
        $off = $tz1->getOffset($dt);
        $sign = ($off < 0 ? '-' : '+');
        $off = abs($off);
        $minutes = $off / 60;
        $hours = floor($minutes / 60);
        $minutes -= ($hours*60);
        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }

    public static function createDateRangeArray(string $start_date, string $end_date) : array
    {
        // creates an array of dates between the start and end date;
        // the dates values are the keys and they're initialized with 0;
        // used for creating histograms based on dates

        if (strlen($start_date) == 0 || strlen($end_date) == 0)
            return array();

        try
        {
            $start_date = new \DateTime($start_date);
            $end_date = new \DateTime($end_date);
            $end_date->modify('+1 day');

            $start_date->setTime(0, 0, 0, 0); // reset time portion
            $end_date->setTime(0, 0, 0, 0);

            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($start_date, $interval, $end_date);

            $bucket = array();
            foreach($daterange as $date){
                $date = $date->format("Y-m-d");
                $bucket[$date] = 0;
            }

            return $bucket;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    public static function createPageRangeArray(string $page_string, int $page_count) : array
    {
        // takes a page string that defines a collection of pages
        // and returns an array with the indexes of pages in the
        // collection; e.g.:
        // pages: none => []
        // pages: 1 => [1]
        // pages: 1,3 => [1,3]
        // pages: 1-3 => [1,2,3]
        // pages: 1,3-5 => [1,3,4,5]
        // pages: last => [<page_count>]
        // pages: 2-last => [2,3,4,...,<page_count>]
        // pages: something-invalid => []

        $empty_result = array();

        // special case of 'none'; return empty array
        if (trim($page_string) === 'none')
            return $empty_result;

        $pages = array();
        $page_ranges = explode(',', $page_string);
        foreach ($page_ranges as $r)
        {
            $page_units = explode('-', trim($r));

            // we'll have at least one entry containing the string, and two
            // entries if a page range is specified; anything more than that
            // is invalid

            if (count($page_units) > 2)
                return $empty_result;

            // trim pages of spaces, replace any 'last' keyword with count,
            // and make sure values are positive integers within the range
            foreach ($page_units as &$u)
            {
                $u = trim($u);
                if ($u === 'last')
                    $u = $page_count;

                if (is_numeric($u) === false) // check for numeric string
                    return $empty_result;

                $uint = (int)$u;             // coerce string and make sure it's an integer
                if ($uint != $u)
                    return $empty_result;

                $u = $uint;
            }

            // if we have a single page; add any page within the page range
            if (count($page_units) === 1)
            {
                if ($page_units[0] >= 1 && $page_units[0] <= $page_count)
                    $pages[] = $page_units[0];
            }

            // if we have multiple pages, create a range of values and add any
            // that are within the range
            if (count($page_units) === 2)
            {
                $p1 = $page_units[0];
                $p2 = $page_units[1];

                if ($p1 < 1) $p1 = 1;
                if ($p1 > $page_count) $p1 = $page_count;
                if ($p2 < 1) $p2 = 1;
                if ($p2 > $page_count) $p2 = $page_count;

                if ($p1 <= $p2)
                    $pages = array_merge($pages, range($p1, $p2));
            }
        }

        // sort the array by the values and remove duplicates
        $pages = array_unique($pages);
        sort($pages);

        return $pages;
    }

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

    public static function generateRandomName(string $prefix) : string
    {
        $name = $prefix . \Flexio\Base\Util::generateRandomString(4);
        return $name;
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

    public static function encrypt(string $plaintext, string $key = null) : ?string
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

    public static function decrypt(string $ciphertext, string $key = null) : ?string
    {
        require_once dirname(dirname(__DIR__)) . '/library/sodium_compat/autoload.php';

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

    public static function safePrintCodeFilename(string $filename) : string
    {
        // mask language
        $filename = str_replace('.php', '', $filename);
        $filename = str_replace("\\", '/', $filename);
        $parts = explode('/', strtolower($filename));
        $parts = array_slice($parts, -2);
        return implode('/', $parts);
    }

    public static function getStreamContents($stream, int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // returns array for table or string for data buffer
    {
        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        if ($readsize <= 0)
            $readsize = 1;

        $mime_type = $stream->getMimeType();
        if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            return $stream->getReader()->getRows($start,$limit);
        }
        else
        {
            // read table content
            $offset1 = 0;
            $offset2 = 0;

            // the starting and ending position we want
            $range1 = $start;
            $range2 = $start + $limit;

            $result = '';
            $reader = $stream->getReader();

            while (true)
            {
                $chunk = $reader->read($readsize);
                if ($chunk === false)
                    break;

                $offset2 = $offset1 + strlen($chunk);

                // if we haven't reached the part we want, keep reading
                if ($offset2 < $range1)
                {
                    $offset1 = $offset2;
                    continue;
                }

                // if we're past the part we want, we're done
                if ($offset1 > $range2)
                    break;

                // case 1: chunk read is contained entirely in the range we want
                if ($offset1 >= $range1 && $offset2 <= $range2)
                    $result .= $chunk;

                // case 2: chunk read covers the range we want
                if ($offset1 < $range1 && $offset2 > $range2)
                    $result .= substr($chunk, $range1 - $offset1, $range2 - $range1);

                // case 3: chunk read covers first part of the range we want
                if ($offset1 < $range1 && $offset2 <= $range2)
                    $result .= substr($chunk, $range1 - $offset1);

                // case 4: chunk read covers second part of the range we want
                if ($offset1 >= $range1 && $offset2 > $range2)
                    $result .= substr($chunk, 0, $range2 - $offset1);

                // set the new starting offset position
                $offset1 = $offset2;
            }

            return $result;
        }
    }

    public static function addProcessInputFromStream($php_stream_handle, string $post_content_type, $process) : void
    {
        $stream = false;
        $streamwriter = false;
        $form_params = array();
        $post_streams = array();

        // first fetch query string parameters
        foreach ($_GET as $key => $value)
        {
            $form_params["query.$key"] = $value;
        }

        // \Flexio\Base\MultipartParser can handle application/x-www-form-urlencoded and /multipart/form-data
        // for all other types, we will take the post body and make it into the stdin

        $mime_type = $post_content_type;
        $semicolon_pos = strpos($mime_type, ';');
        if ($semicolon_pos !== false)
            $mime_type = substr($mime_type, 0, $semicolon_pos);
        if ($mime_type != 'application/x-www-form-urlencoded' && $mime_type != 'multipart/form-data')
        {
            $stream = \Flexio\Base\Stream::create();

            // post body is a data stream, post it as our pipe's stdin
            $first = true;
            $done = false;
            $streamwriter = null;
            while (!$done)
            {
                $data = fread($php_stream_handle, 32768);

                if ($data === false || strlen($data) != 32768)
                    $done = true;

                if ($first && $data !== false && strlen($data) > 0)
                {
                    $stream_info = array();
                    $stream_info['mime_type'] = $mime_type;
                    $stream->set($stream_info);
                    $streamwriter = $stream->getWriter();
                }

                if ($streamwriter)
                    $streamwriter->write($data);
            }

            $process->setParams($form_params);
            $process->setStdin($stream);

            return;
        }

        $size = 0;

        $parser = \Flexio\Base\MultipartParser::create();
        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params, &$size, &$post_streams) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Base\Stream::create();

                if ($content_type === false)
                    $content_type = \Flexio\Base\ContentType::getMimeType($filename, '');

                $size = 0;

                $stream_info = array();
                $stream_info['name'] = $filename;
                $stream_info['mime_type'] = $content_type;

                $stream->set($stream_info);
                $streamwriter = $stream->getWriter();
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                    $size += strlen($data);
                }
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $streamwriter = false;
                $stream->setSize($size);
                $process->addFile($name, $stream);
                $stream = false;
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params['form.' . $name] = $data;
            }
        });
        fclose($php_stream_handle);

        $process->setParams($form_params);
    }

    public static function handleStreamUpload(array $params, \Flexio\IFace\IStreamWriter $streamwriter, string &$filename, string &$mime_type) : void
    {
        $filename = '';
        $mime_type = '';

        // get the information the parser needs to parse the content
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($post_content_type, 'multipart/form-data') !== false)
        {
            // multipart form-data upload
            $php_stream_handle = fopen('php://input', 'rb');

            // parse the content and set the stream info
            $part_data_snippet = false;
            $part_filename = false;
            $part_mimetype = false;
            $part_active = false;
            $part_succeeded = false;

            $parser = \Flexio\Base\MultipartParser::create();

            $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$streamwriter, &$part_data_snippet, &$part_filename, &$part_mimetype, &$part_active, &$part_succeeded) {
                if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
                {
                    if ($name == 'media' || $name == 'file') // we're looking for an element named 'media'; 'file' for temporary backward-compatibility
                    {
                        $part_active = true;
                        $part_filename = $filename;
                        $part_mimetype = $content_type;
                        $part_succeeded = true;
                    }
                }
                else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA && $part_active)
                {
                    // get a sample of the data for mime sensing
                    if ($part_data_snippet === false)
                        $part_data_snippet = $data;

                    // write out the data
                    $streamwriter->write($data);
                }
                else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
                {
                    $part_active = false;
                }
            });
            fclose($php_stream_handle);

            // make sure the parse was successful
            if (!$part_succeeded)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // determine the filename, stripping off the leading path info;
            // use a default if one wasn't supplied
            $default_name = \Flexio\Base\Util::generateHandle() . '.dat';
            $filename = strlen($part_filename) > 0 ? $part_filename : $default_name;
            $name = \Flexio\Base\File::getFilename($filename);
            $ext = \Flexio\Base\File::getFileExtension($filename);
            $filename = $name . (strlen($ext) > 0 ? ".$ext" : '');

            // sense the mime type, but go with what is declared if it's available
            $mime_type = \Flexio\Base\ContentType::STREAM;
            $declared_mime_type = $part_mimetype;

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename_hint = $params['filename_hint'] ?? $filename;
        }
         else
        {
            $declared_mime_type = $_SERVER["CONTENT_TYPE"] ?? '';

            $php_stream_handle = fopen('php://input', 'rb');
            $part_data_snippet = false;

            while (true)
            {
                $data = fread($php_stream_handle, 32768);
                if ($data === false || strlen($data) == 0)
                    break;
                if ($part_data_snippet === false)
                    $part_data_snippet = $data;
                $streamwriter->write($data);
            }

            fclose($php_stream_handle);

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename = $_GET['name'] ?? \Flexio\Base\Util::generateHandle() . '.dat';

            $filename_hint = $_GET['filename_hint'] ?? $filename;
        }


        if (isset($_GET['filename_hint']))
        {
            // a hint filename was passed -- use that to try to determine the content type
            $mime_type = \Flexio\Base\ContentType::getMimeType($filename_hint, $part_data_snippet);
        }
        else
        {
            // use the Content-Type header passed to us
            if ($declared_mime_type == "application/x-www-form-urlencoded")
                $declared_mime_type = "application/octet-stream";

            if (strlen($declared_mime_type) == 0 || $declared_mime_type == "autosense")
                $mime_type = \Flexio\Base\ContentType::getMimeType($filename_hint, $part_data_snippet);
                    else
                $mime_type = $declared_mime_type;
        }
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


class Profiler
{
    var $start_time;
    var $last_time;
    var $times = array();

    function __construct()
    {
        $this->start_time =  microtime(true);
        $this->last_time = $this->start_time;
    }

    function log($message = '')
    {
        $current_time = microtime(true);
        $elapsed_time = round(($current_time - $this->last_time)*1000,2);
        $this->last_time = $current_time;
        $this->times[] = array('message' => $message, 'time' => $elapsed_time);
    }

    function report()
    {
        $str = "\n\n";

        foreach($this->times as $t)
        {
            $time = $t['time'];
            $message = $t['message'];
            $str .= "$time ms \t $message\n";
        }

        $str .= "\n";
        $str .= "Total time: " . round(($this->last_time - $this->start_time)*1000,2);

        return $str;
    }
}
