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
 * @subpackage System
 */


class Util
{
    public static function isPlatformWindows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") ? true : false;
    }

    public static function isPlatformMac()
    {
        return (strtoupper(substr(PHP_OS, 0, 6)) == "DARWIN") ? true : false;
    }

    public static function isPlatformLinux()
    {
        return (strtoupper(substr(PHP_OS, 0, 5)) == "LINUX") ? true : false;
    }

    public static function isXampp()
    {
        return (strpos(strtoupper(PHP_BINDIR), "XAMPP") !== false) ? true : false;
    }

    public static function exec($cmdline, $wait = false)
    {
        if (Util::isPlatformWindows())
        {
            $wsh_shell = new COM("WScript.Shell");
            $exec = $wsh_shell->Run("$cmdline", 0, $wait);
        }
         else
        {
            $cmd = "$cmdline > /dev/null";
            if (!$wait)
                $cmd .= " &";
            exec($cmd);
        }
    }

    public static function runInBackground($code, $wait = false)
    {
        $phpbin = Util::getBinaryPath('php');

        $stubphp = System::getBaseDirectory();
        $stubphp = str_replace("\\", "/", $stubphp);
        $stubphp .= '/scripts/stub.php';

        $curidentity = System::serializeGlobalVars();
        $curlang = $GLOBALS['g_store']->lang;
        $cursessid = session_id();
        $curservername = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

        $httphost = GET_HTTP_HOST();
        if (is_null($httphost))
            $httphost = 'null';
             else
            $httphost = "'$httphost'";

        $runcode = <<<EOT
            include_once '$stubphp';
            class DeleteOnExit {
                function __destruct() {
                    unlink(__FILE__);
                }
            }
            \$g_delete_on_exit = new DeleteOnExit();
            session_id('$cursessid');
            if (strlen('$curservername') > 0)
                \$_SERVER['SERVER_NAME'] = '$curservername';
            System::unserializeGlobalVars('$curidentity');
            System::setCurrentLanguage('$curlang');
            \$GLOBALS['g_store']->http_host = $httphost;
            $code;
EOT;

        $runcode = ("<" . "?" . "php " . $runcode);

        $tmpfname = tempnam(sys_get_temp_dir(), 'cde');
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $runcode);
        fclose($handle);
        chmod($tmpfname, 0600);


        $command = "$phpbin -f \"$tmpfname\"";
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
        {
            $wsh_shell = new COM("WScript.Shell");
            $exec = $wsh_shell->Run($command, 0, $wait);
        }
         else
        {
            if ($wait)
                $suffix = '';
                 else
                $suffix = '&';

            exec("$command > /dev/null $suffix");
        }
    }

    public static function customAction($action, &$params)
    {
        if (!(class_exists($action, false) || interface_exists($action, false)))
        {
            //if (file_exists(System::getApplicationDirectory() . DIRECTORY_SEPARATOR . "custom" . DIRECTORY_SEPARATOR . "$action.php"))
            //    Framework::loadClass($action);
        }

        if (class_exists($action, false) || interface_exists($action, false))
        {
            $obj = new $action;
            $obj->action($params);
            return true;
        }

        return false;
    }

    // this is a function that will pop up a traditional
    // GUI message box, which can be useful for debugging
    // At present, it only works on Win32
    public static function messageBox($msg, $caption = 'Message')
    {
        // to show object data, use var_export (similar to var_dump):
        // Util::messageBox(var_export($validation_info,true));

        if (Util::isPlatformWindows())
        {
            $wsh_shell = new COM('WScript.Shell');
            $wsh_shell->Popup($msg, 0, $caption, 0x1040);
        }
    }

    public static function notepad($msg)
    {
        $filename = Util::createTempFile('', 'txt');
        file_put_contents($filename, $msg);

        $wsh_shell = new COM("WScript.Shell");
        $exec = $wsh_shell->Run("notepad $filename", 1, true);

        unlink($filename);
    }

    public static function getFilename($filename)
    {
        // pathinfo will parse paths differently, depending on the
        // platform being run on

        if (!Util::isPlatformWindows())
            $filename = str_replace("\\", "/", $filename); // parse using linux-style paths
             else
            $filename = str_replace("/", "\\", $filename); // parse using windows-style paths

        return pathinfo($filename, PATHINFO_FILENAME);
    }

    public static function getFileExtension($filename)
    {
        // pathinfo will parse paths differently, depending on the
        // platform being run on

        if (!Util::isPlatformWindows())
            $filename = str_replace("\\", "/", $filename); // parse using linux-style paths
             else
            $filename = str_replace("/", "\\", $filename); // parse using windows-style paths

        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function rmtree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file)
        {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $file))
                Util::rmtree($dir . DIRECTORY_SEPARATOR . $file);
                 else
                unlink($dir . DIRECTORY_SEPARATOR . $file);
        }

        return rmdir($dir);
    }

    public static function beforeFirst($str, $search)
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

    public static function afterFirst($str, $search)
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

    public static function beforeLast($str, $search)
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

    public static function afterLast($str, $search)
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

    public static function matchPath($str, $pattern, $case_sensitive)
    {
        return fnmatch($pattern, $str, $case_sensitive ? 0 : FNM_CASEFOLD);  // FNM_CASEFOLD triggers caseless match
    }

    public static function lpad($str, $n, $ch = ' ')
    {
        return str_pad($str, $n, $ch, STR_PAD_LEFT);
    }

    public static function rpad($str, $n, $ch = ' ')
    {
        return str_pad($str, $n, $ch, STR_PAD_RIGHT);
    }

    public static function zlstrpos($haystack, $needle, $offset = 0)
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

    private static function json_strpos($haystack, $needle, $offset = 0)
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

    public static function isAssociativeArray($arr)
    {
        // tests whether or  not an array is sequential or associative;
        // (e.g. ["a","b","c"] or ["k1"=>"a","k2"=>"b","k3"=>"c"])

        if (count($arr) === 0)
            return false;

        $is_associative = array_keys($arr) !== range(0, count($arr) - 1);
        return $is_associative;
    }

    public static function diff($array1, $array2)
    {
        // note: this function takes two arrays and finds the difference
        // between them and reports the results similarly to how a text
        // diff program would, with + and - for the insertions/deletions;
        // for example, if we have the following:
        //     Util::diff(array('b','c','d'),array('a','b','c','e'))
        // we get:
        //     [{"+":"a"},{"=":"b"},{"=":"c"},{"-":"d"},{"+":"e"}]

        if (!is_array($array1) || !is_array($array2))
            return null;

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
            Util::diff(array_slice($array1, 0, $offset1), array_slice($array2, 0, $offset2)),
            $result_array,
            Util::diff(array_slice($array1, $offset1 + $max_length), array_slice($array2, $offset2 + $max_length))
        );
    }

    public static function filterChars($str, $allowed_chars)
    {
        if (!is_string($str) || !is_string($allowed_chars))
            return '';

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

    public static function filterAlphaNumeric($str, $additional_allowed = '')
    {
        if (!is_string($str) || !is_string($additional_allowed))
            return '';

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

    public static function filterDigits($str, $additional_allowed = '')
    {
        if (!is_string($str) || !is_string($additional_allowed))
            return '';

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

    public static function mapArray($arr1, $arr2)
    {
        // returns an array with the values of $arr2 mapped onto $arr1 based
        // on keys; if $arr2 has keys that aren't in $arr1, they are excluded
        // from the output; if $arr1 has keys that aren't in $arr2, the key/value
        // pair as included as it exists in $arr1

        if (!is_array($arr1) || !is_array($arr2))
            return false;

        $result = array();
        foreach ($arr1 as $key => $value)
        {
            $result[$key] = isset_or($arr2[$key], $value);
        }

        return $result;
    }

    public static function filterArray($arr, $allowed_keys)
    {
        return array_intersect_key($arr, array_flip($allowed_keys));
    }

    public static function filterArrayEmptyValues($arr)
    {
        return array_filter($arr, 'strlen');
    }

    public static function removeArrayKeys($arr, $remove_keys)
    {
        return array_diff_key($arr, array_flip($remove_keys));
    }

    public static function filterArrayofArrays($arr, $allowed_keys)
    {
        $result = array();
        foreach ($arr as $a)
        {
            $result[] = array_intersect_key($a, array_flip($allowed_keys));
        }
        return $result;
    }

    public static function formatNumber($num, $dec = 0)
    {
        if (is_null($num))
            return null;
        global $g_store;
        return number_format($num, $dec, $g_store->decimal_separator, $g_store->thousands_separator);
    }

    public static function formatDate($date)
    {
        $datetime = new DateTime($date);
        return $datetime->format(DateTime::ISO8601);
    }

    public static function formateDateDiff($date1, $date2)
    {
        // returns the difference between two datetimes in seconds (including
        // fractions of a second)

        if (!isset($date1) || !isset($date2))
            return null;

        // get the difference between two dates, excluding the microtime portion
        $d1 = DateTime::createFromFormat("Y-m-d H:i:s.u", $date1);
        $d2 = DateTime::createFromFormat("Y-m-d H:i:s.u", $date2);

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

    public static function formatJson($json)
    {
        // if input isn't a string, then encode it; if it's
        // a string, decode/encode it to make sure it's valid
        // json and to pre-process it for formatting
        if (!is_string($json))
        {
            // if input isn't a string, then encode it
            $json = json_encode($json);
        }
         else
        {
            if (strlen($json) == 0)
                return "\n";

            $obj = json_decode($json);
            if (!isset($obj))
                return $json;
            $json = json_encode($obj);
        }

        $result = '';
        $in_quotes = false;
        $fresh_line = true;

        $level = 0;
        $len = strlen($json);
        for ($i = 0; $i <= $len; $i++)
        {
            $ch = substr($json, $i, 1);

            if ($ch == '"' && ($i > 0 && substr($json, $i-1, 1) != '\\'))
            {
                $in_quotes = !$in_quotes;
            }
             else
            {
                if (($ch == '}' || $ch == ']') && !$in_quotes)
                {
                    $level--;
                    $result .= "\n";

                    for ($j = 0; $j < $level; $j++)
                        $result .= '    ';
                }
            }

            if ($in_quotes)
            {
                $result .= $ch;
                $fresh_line = false;
            }
             else
            {
                if (($ch == '{' || $ch == '[') && !$fresh_line)
                {
                    $result .= "\n";
                    for ($j = 0; $j < $level; $j++)
                        $result .= '    ';
                }

                $result .= $ch;
                $fresh_line = false;

                if ($ch == ':')
                    $result .= ' ';

                if ($ch == '{' || $ch == '[' || $ch == ',')
                {
                    if ($ch == '{' || $ch == '[')
                        $level++;

                    $result .= "\n";

                    for ($j = 0; $j < $level; $j++)
                        $result .= '    ';
                    $fresh_line = true;
                }
            }
        }

        return $result .= "\n";
    }

    // sorts an array in ascending order by a specified field
    public static function sortByFieldAsc(&$arr, $field)
    {
        $code="if (\$a['$field'] == \$b['$field']) return 0;".
            "return (\$a['$field'] < \$b['$field']) ? -1 : 1;";
        usort($arr, create_function('$a,$b',$code));
    }

    // sorts an array in descending order by a specified field
    public static function sortByFieldDesc(&$arr, $field)
    {
        $code="if (\$a['$field'] == \$b['$field']) return 0;".
            "return (\$a['$field'] > \$b['$field']) ? -1 : 1;";
        usort($arr, create_function('$a,$b',$code));
    }

    public static function getDaysDiff($dt1, $dt2)
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
        $tz1 = new DateTimeZone($GLOBALS['g_store']->timezone);
        $dt = new DateTime('now');
        $off = $tz1->getOffset($dt);
        $sign = ($off < 0 ? '-' : '+');
        $off = abs($off);
        $minutes = $off / 60;
        $hours = floor($minutes / 60);
        $minutes -= ($hours*60);
        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }
    */

    public static function isPositiveInteger($value)
    {
        // verifies if an id is a positive integer
        $value_pure = (int)$value;
        if (!is_int($value_pure) || $value_pure <= 0)
            return false;

        return true;
    }

    public static function isValidDateTime($value)
    {
        // verifies if the value is a valid ISO 8601 datetime
        if (!is_string($value))
            return false;

        // TODO: the DateTime::createFromFormat() can't handle certain
        // types of date time formats allowed by the standard (i.e, partial
        // times given by ".ttt" in the following: YYYY-MM-DDThh:mm:ss.tttZ)
        $dt = DateTime::createFromFormat(DateTime::ISO8601, $value);
        if ($dt !== false)
            return true;

        return false;
    }

    public static function isValidEmail($value)
    {
        // verifies if a value is a valid email
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_EMAIL);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidHostName($value)
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

    public static function isValidIPV4($value)
    {
        // verifies if a value is a valid ipv4 address
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidIPV6($value)
    {
        // verifies if a value is a valid ipv4 address
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidUrl($value)
    {
        // verifies if a value is a valid url
        if (!is_string($value))
            return false;

        $result = filter_var($value, FILTER_VALIDATE_URL);
        if ($result !== false)
            return true;

        return false;
    }

    public static function isValidPassword($value)
    {
        // make sure a password is a string that's a minimum
        // of 8 characters
        if (!is_string($value))
            return false;

        if (strlen($value) >= 8)
            return true;

        return false;
    }

    public static function generateRandomString($length)
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

    public static function generateHandle()
    {
        return Util::generateRandomString(20);
    }

    public static function generatePassword()
    {
        $pw = Util::generateRandomString(10);
        $pw[2] = ''.random_int(0, 9);
        $pw[6] = ''.random_int(0, 9);
        $i = random_int(0, 9);
        if ($i == 2 || $i == 6)
            $i--;
        $pw[$i] = strtoupper($pw[$i]);

        return $pw;
    }

    public static function encrypt($plaintext, $key)
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
        catch (Error $e)
        {
            return null;
        }

        return 'ZZXV2/'.base64_encode($enc);
    }

    public static function decrypt($ciphertext, $key)
    {
        require_once dirname(dirname(__DIR__)) . '/library/sodium_compat/autoload.php';

        if (!is_string($ciphertext))
            return null;

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
        catch (Error $e)
        {
            return null;
        }
    }

    public static function oldEncrypt($string, $key)
    {
        if (is_null($string))
            return null;

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

    public static function oldDecrypt($string, $key)
    {
        if ($key == '') // empty keys not allowed in mcrypt
            $key = 'A';

        if (is_null($string))
            return null;

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

    public static function getBinaryPath($bin)
    {
        $fxhome = System::getBaseDirectory();
        $base_path = dirname($fxhome);

        // on some newer windows setups, we've been installing the server software
        // separately from the code tree
        // \fxsite\flexio - code
        // \fxsite\server\php\php.exe

        if (is_dir($base_path . DIRECTORY_SEPARATOR . 'server'))
            $base_path .= (DIRECTORY_SEPARATOR . 'server');

        if (Util::isPlatformWindows())
        {
            // running on windows -- we need to fully qualify the exe path
            switch ($bin)
            {
                case 'grep':        return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'unixutil'        . DIRECTORY_SEPARATOR . 'grep.exe';
                // TODO: add paths for python, nodejs, golang
                case 'r':           return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'rlang'           . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'r-portable' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'x64' . DIRECTORY_SEPARATOR . 'rscript.exe';
                case 'gs':          return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'gs'              . DIRECTORY_SEPARATOR . 'gswin32c.exe';
                case 'unzip':       return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'zipwin32'        . DIRECTORY_SEPARATOR . 'unzip.exe';
                case 'zip':         return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'zipwin32'        . DIRECTORY_SEPARATOR . 'zip.exe';
                case 'php':         return $base_path . DIRECTORY_SEPARATOR . 'php'     . DIRECTORY_SEPARATOR . 'php.exe';
                case 'mysql':       return $base_path . DIRECTORY_SEPARATOR . 'mysql'   . DIRECTORY_SEPARATOR . 'bin'             . DIRECTORY_SEPARATOR . 'mysql.exe';
                case 'mysqldump':   return $base_path . DIRECTORY_SEPARATOR . 'mysql'   . DIRECTORY_SEPARATOR . 'bin'             . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                case 'pdftk':       return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'pdftk'           . DIRECTORY_SEPARATOR . 'pdftk.exe';
                case 'phantomjs':   return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phantomjs-1.9.1' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'windows'     . DIRECTORY_SEPARATOR . 'phantomjs.exe';
                default:            return null;
            }
        }
         else
        {
            $xtra_bin_dir = '';
            if (Util::isXampp() && Util::isPlatformMac())
                $xtra_bin_dir = PHP_BINDIR.'/';

            $phantom_js_platform_folder = 'linux64';
            if (Util::isPlatformMac())
                $phantom_js_platform_folder = 'macosx';

            // running on linux, no need to fully qualify exe path
            switch ($bin)
            {
                case 'grep':        return 'grep';
                case 'python':      return 'python3';
                case 'javascript':  return 'nodejs';
                case 'go':          return 'go run';
                case 'r':           return 'rscript';

                case 'php':
                    if (strlen($xtra_bin_dir) > 0)
                        return $xtra_bin_dir . 'php';
                         else
                        return '/usr/bin/php';

                case 'gs':          return 'gs';
                case 'unzip':       return $xtra_bin_dir . 'unzip';
                case 'zip':         return $xtra_bin_dir . 'zip';
                case 'mysql':       return $xtra_bin_dir . 'mysql';
                case 'mysqldump':   return $xtra_bin_dir . 'mysqldump';
                case 'pdftk':       return 'pdftk';
                case 'phantomjs':   return 'phantomjs';
                default:            return null;
            }
        }

        return null;
    }

    // shorthand for getting a temporary file
    public static function getTempFilename($ext = null)
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'fx_');
        if (isset($ext))
        {
            @unlink($temp_file);
            $temp_file .= ('.' . $ext);

            $fp = fopen($temp_file, "w");
            fclose($fp);

            chmod($temp_file, 0600);
        }

        return $temp_file;
    }

    public static function appendPath($path, $part)
    {
        $result = $path;
        $len = strlen($result);
        if ($len == 0)
            return $part;
        if (substr($part, $len - 1, 1) != DIRECTORY_SEPARATOR)
            $result .= DIRECTORY_SEPARATOR;
        return $result . $part;
    }

    public static function appendUrlPath($url, $part)
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

    public static function getGitRevision()
    {
        $path = dirname(dirname(__DIR__)) . '/.git/refs/heads/develop';
        $str = @file_get_contents($path);
        if (!$str)
            $str = '';
        return trim($str);
    }

    // mode should be either 'inline' or 'download'
    public static function headersPdf($output_filename, $file_location, $mode = 'inline')
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
    }

    public static function headersCsv($output_filename)
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
    }

    public static function header_error($code, $text = null)
    {
        switch ($code) {
            default:
            case 400: isset_or($text, 'Bad Request'); break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . $code . ' ' . $text);

        return $code;
    }

    public static function createTempFile($prefix = '', $extension = 'tmp')
    {
        $fname = tempnam(sys_get_temp_dir(), $prefix);
        $new_name = str_replace('.tmp', '_tmp', $fname) . ".$extension";
        rename($fname, $new_name);
        return $new_name;
    }

    public static function createThumbnail($input_path, $output_path, $new_max_size = 256)
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

    public static function zipFile($target_zip, $file)
    {
        $exe = Util::getBinaryPath('zip');

        if (is_array($file))
            $arr = $file;
             else
            $arr = array($file);

        // make sure specified input files exist
        foreach ($arr as $f)
        {
            if (!file_exists($f))
                return false;
        }

        foreach ($arr as $f)
        {
            Util::exec("$exe -j $target_zip \"$f\"", true);
        }

        return true;
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
