<?php
/**
 * @package     Helpers - Text Helpers
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 **/

if ( ! function_exists('_stringify_attributes'))
{
    /**
     * Stringify attributes for use in HTML tags.
     *
     * Helper function used to convert a string, array, or object
     * of attributes to a string.
     *
     * @param	mixed	string, array, object
     * @param	bool
     * @return	string
     */
    function _stringify_attributes($attributes, $js = FALSE)
    {
        $atts = NULL;

        if (empty($attributes))
        {
            return $atts;
        }

        if (is_string($attributes))
        {
            return ' '.$attributes;
        }

        $attributes = (array) $attributes;

        foreach ($attributes as $key => $val)
        {
            $atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
        }

        return rtrim($atts, ',');
    }
}

if ( ! function_exists('_attributes_to_string'))
{
    /**
     * Attributes To String
     *
     * Helper function used by some of the form helpers
     *
     * @param	mixed
     * @return	string
     */
    function _attributes_to_string($attributes)
    {
        if (empty($attributes))
        {
            return '';
        }

        if (is_object($attributes))
        {
            $attributes = (array) $attributes;
        }

        if (is_array($attributes))
        {
            $atts = '';

            foreach ($attributes as $key => $val)
            {
                $atts .= ' '.$key.'="'.$val.'"';
            }

            return $atts;
        }

        if (is_string($attributes))
        {
            return ' '.$attributes;
        }

        return FALSE;
    }
}

if ( ! function_exists('_parse_form_attributes'))
{
    /**
     * Parse the form attributes
     *
     * Helper function used by some of the form helpers
     *
     * @param	array	$attributes	List of attributes
     * @param	array	$default	Default values
     * @return	string
     */
    function _parse_form_attributes($attributes, $default)
    {
        if (is_array($attributes))
        {
            foreach ($default as $key => $val)
            {
                if (isset($attributes[$key]))
                {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }

            if (count($attributes) > 0)
            {
                $default = array_merge($default, $attributes);
            }
        }

        $att = '';

        foreach ($default as $key => $val)
        {
            if ($key === 'value')
            {
                $val = html_escape($val);
            }
            elseif ($key === 'name' && ! strlen($default['name']))
            {
                continue;
            }

            $att .= $key.'="'.$val.'" ';
        }

        return $att;
    }
}

if ( ! function_exists('word_limiter'))
{
    /**
     * Word Limiter
     *
     * Limits a string to X number of words.
     *
     * @param	string
     * @param	int
     * @param	string	the end character. Usually an ellipsis
     * @return	string
     */
    function word_limiter($str, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($str) === '')
        {
            return $str;
        }

        preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

        if (strlen($str) === strlen($matches[0]))
        {
            $end_char = '';
        }

        return rtrim($matches[0]).$end_char;
    }
}

if ( ! function_exists('character_limiter'))
{
    /**
     * Character Limiter
     *
     * Limits the string based on the character count.  Preserves complete words
     * so the character count may not be exactly as specified.
     *
     * @param	string
     * @param	int
     * @param	string	the end character. Usually an ellipsis
     * @return	string
     */
    function character_limiter($str, $n = 500, $end_char = '&#8230;')
    {
        if (mb_strlen($str) < $n)
        {
            return $str;
        }

        // a bit complicated, but faster than preg_replace with \s+
        $str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\x0B", "\x0C"), ' ', $str));

        if (mb_strlen($str) <= $n)
        {
            return $str;
        }

        $out = '';
        foreach (explode(' ', trim($str)) as $val)
        {
            $out .= $val.' ';

            if (mb_strlen($out) >= $n)
            {
                $out = trim($out);
                return (mb_strlen($out) === mb_strlen($str)) ? $out : $out.$end_char;
            }
        }
    }
}

if ( ! function_exists('ascii_to_entities'))
{
    /**
     * High ASCII to Entities
     *
     * Converts high ASCII text and MS Word special characters to character entities
     *
     * @param	string	$str
     * @return	string
     */
    function ascii_to_entities($str)
    {
        $out = '';
        for ($i = 0, $s = strlen($str) - 1, $count = 1, $temp = array(); $i <= $s; $i++)
        {
            $ordinal = ord($str[$i]);

            if ($ordinal < 128)
            {
                /*
                    If the $temp array has a value but we have moved on, then it seems only
                    fair that we output that entity and restart $temp before continuing. -Paul
                */
                if (count($temp) === 1)
                {
                    $out .= '&#'.array_shift($temp).';';
                    $count = 1;
                }

                $out .= $str[$i];
            }
            else
            {
                if (count($temp) === 0)
                {
                    $count = ($ordinal < 224) ? 2 : 3;
                }

                $temp[] = $ordinal;

                if (count($temp) === $count)
                {
                    $number = ($count === 3)
                        ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
                        : (($temp[0] % 32) * 64) + ($temp[1] % 64);

                    $out .= '&#'.$number.';';
                    $count = 1;
                    $temp = array();
                }
                // If this is the last iteration, just output whatever we have
                elseif ($i === $s)
                {
                    $out .= '&#'.implode(';', $temp).';';
                }
            }
        }

        return $out;
    }
}

if ( ! function_exists('entities_to_ascii'))
{
    /**
     * Entities to ASCII
     *
     * Converts character entities back to ASCII
     *
     * @param	string
     * @param	bool
     * @return	string
     */
    function entities_to_ascii($str, $all = TRUE)
    {
        if (preg_match_all('/\&#(\d+)\;/', $str, $matches))
        {
            for ($i = 0, $s = count($matches[0]); $i < $s; $i++)
            {
                $digits = $matches[1][$i];
                $out = '';

                if ($digits < 128)
                {
                    $out .= chr($digits);

                }
                elseif ($digits < 2048)
                {
                    $out .= chr(192 + (($digits - ($digits % 64)) / 64)).chr(128 + ($digits % 64));
                }
                else
                {
                    $out .= chr(224 + (($digits - ($digits % 4096)) / 4096))
                        .chr(128 + ((($digits % 4096) - ($digits % 64)) / 64))
                        .chr(128 + ($digits % 64));
                }

                $str = str_replace($matches[0][$i], $out, $str);
            }
        }

        if ($all)
        {
            return str_replace(
                array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'),
                array('&', '<', '>', '"', "'", '-'),
                $str
            );
        }

        return $str;
    }
}

if ( ! function_exists('word_censor'))
{
    /**
     * Word Censoring Function
     *
     * Supply a string and an array of disallowed words and any
     * matched words will be converted to #### or to the replacement
     * word you've submitted.
     *
     * @param	string	the text string
     * @param	string	the array of censored words
     * @param	string	the optional replacement value
     * @return	string
     */
    function word_censor($str, $censored, $replacement = '')
    {
        if ( ! is_array($censored))
        {
            return $str;
        }

        $str = ' '.$str.' ';

        // \w, \b and a few others do not match on a unicode character
        // set for performance reasons. As a result words like über
        // will not match on a word boundary. Instead, we'll assume that
        // a bad word will be bookeneded by any of these characters.
        $delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

        foreach ($censored as $badword)
        {
            if ($replacement !== '')
            {
                $str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/i", "\\1{$replacement}\\3", $str);
            }
            else
            {
                $str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
            }
        }

        return trim($str);
    }
}

if ( ! function_exists('highlight_code'))
{
    /**
     * Code Highlighter
     *
     * Colorizes code strings
     *
     * @param	string	the text string
     * @return	string
     */
    function highlight_code($str)
    {
        /* The highlight string function encodes and highlights
         * brackets so we need them to start raw.
         *
         * Also replace any existing PHP tags to temporary markers
         * so they don't accidentally break the string out of PHP,
         * and thus, thwart the highlighting.
         */
        $str = str_replace(
            array('&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>'),
            array('<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
            $str
        );

        // The highlight_string function requires that the text be surrounded
        // by PHP tags, which we will remove later
        $str = highlight_string('<?php '.$str.' ?>', TRUE);

        // Remove our artificially added PHP, and the syntax highlighting that came with it
        $str = preg_replace(
            array(
                '/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
                '/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
                '/<span style="color: #[A-Z0-9]+"\><\/span>/i'
            ),
            array(
                '<span style="color: #$1">',
                "$1</span>\n</span>\n</code>",
                ''
            ),
            $str
        );

        // Replace our markers back to PHP tags.
        return str_replace(
            array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
            array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'),
            $str
        );
    }
}

if ( ! function_exists('highlight_phrase'))
{
    /**
     * Phrase Highlighter
     *
     * Highlights a phrase within a text string
     *
     * @param	string	$str		the text string
     * @param	string	$phrase		the phrase you'd like to highlight
     * @param	string	$tag_open	the openging tag to precede the phrase with
     * @param	string	$tag_close	the closing tag to end the phrase with
     * @return	string
     */
    function highlight_phrase($str, $phrase, $tag_open = '<mark>', $tag_close = '</mark>')
    {
        return ($str !== '' && $phrase !== '')
            ? preg_replace('/('.preg_quote($phrase, '/').')/i'.(UTF8_ENABLED ? 'u' : ''), $tag_open.'\\1'.$tag_close, $str)
            : $str;
    }
}

if ( ! function_exists('word_wrap'))
{
    /**
     * Word Wrap
     *
     * Wraps text at the specified character. Maintains the integrity of words.
     * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
     * will URLs.
     *
     * @param	string	$str		the text string
     * @param	int	$charlim = 76	the number of characters to wrap at
     * @return	string
     */
    function word_wrap($str, $charlim = 76)
    {
        // Set the character limit
        is_numeric($charlim) OR $charlim = 76;

        // Reduce multiple spaces
        $str = preg_replace('| +|', ' ', $str);

        // Standardize newlines
        if (strpos($str, "\r") !== FALSE)
        {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }

        // If the current word is surrounded by {unwrap} tags we'll
        // strip the entire chunk and replace it with a marker.
        $unwrap = array();
        if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches))
        {
            for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
            {
                $unwrap[] = $matches[1][$i];
                $str = str_replace($matches[0][$i], '{{unwrapped'.$i.'}}', $str);
            }
        }

        // Use PHP's native function to do the initial wordwrap.
        // We set the cut flag to FALSE so that any individual words that are
        // too long get left alone. In the next step we'll deal with them.
        $str = wordwrap($str, $charlim, "\n", FALSE);

        // Split the string into individual lines of text and cycle through them
        $output = '';
        foreach (explode("\n", $str) as $line)
        {
            // Is the line within the allowed character count?
            // If so we'll join it to the output and continue
            if (mb_strlen($line) <= $charlim)
            {
                $output .= $line."\n";
                continue;
            }

            $temp = '';
            while (mb_strlen($line) > $charlim)
            {
                // If the over-length word is a URL we won't wrap it
                if (preg_match('!\[url.+\]|://|www\.!', $line))
                {
                    break;
                }

                // Trim the word down
                $temp .= mb_substr($line, 0, $charlim - 1);
                $line = mb_substr($line, $charlim - 1);
            }

            // If $temp contains data it means we had to split up an over-length
            // word into smaller chunks so we'll add it back to our current line
            if ($temp !== '')
            {
                $output .= $temp."\n".$line."\n";
            }
            else
            {
                $output .= $line."\n";
            }
        }

        // Put our markers back
        if (count($unwrap) > 0)
        {
            foreach ($unwrap as $key => $val)
            {
                $output = str_replace('{{unwrapped'.$key.'}}', $val, $output);
            }
        }

        return $output;
    }
}

if ( ! function_exists('ellipsize'))
{
    /**
     * Ellipsize String
     *
     * This function will strip tags from a string, split it at its max_length and ellipsize
     *
     * @param	string	string to ellipsize
     * @param	int	max length of string
     * @param	mixed	int (1|0) or float, .5, .2, etc for position to split
     * @param	string	ellipsis ; Default '...'
     * @return	string	ellipsized string
     */
    function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;')
    {
        // Strip tags
        $str = trim(strip_tags($str));

        // Is the string long enough to ellipsize?
        if (mb_strlen($str) <= $max_length)
        {
            return $str;
        }

        $beg = mb_substr($str, 0, floor($max_length * $position));
        $position = ($position > 1) ? 1 : $position;

        if ($position === 1)
        {
            $end = mb_substr($str, 0, -($max_length - mb_strlen($beg)));
        }
        else
        {
            $end = mb_substr($str, -($max_length - mb_strlen($beg)));
        }

        return $beg.$ellipsis.$end;
    }
}

if ( ! function_exists('assign_rand_value'))
{
    /**
     * generate char by parameter
     *
     * @param $num
     * @return string
     */
    function assign_rand_value($num) {

        $rand_value = '';

        // accepts 1 - 36
        switch($num) {
            case "1" : $rand_value = "a"; break;
            case "2" : $rand_value = "b"; break;
            case "3" : $rand_value = "c"; break;
            case "4" : $rand_value = "d"; break;
            case "5" : $rand_value = "e"; break;
            case "6" : $rand_value = "f"; break;
            case "7" : $rand_value = "g"; break;
            case "8" : $rand_value = "h"; break;
            case "9" : $rand_value = "i"; break;
            case "10" : $rand_value = "j"; break;
            case "11" : $rand_value = "k"; break;
            case "12" : $rand_value = "l"; break;
            case "13" : $rand_value = "m"; break;
            case "14" : $rand_value = "n"; break;
            case "15" : $rand_value = "o"; break;
            case "16" : $rand_value = "p"; break;
            case "17" : $rand_value = "q"; break;
            case "18" : $rand_value = "r"; break;
            case "19" : $rand_value = "s"; break;
            case "20" : $rand_value = "t"; break;
            case "21" : $rand_value = "u"; break;
            case "22" : $rand_value = "v"; break;
            case "23" : $rand_value = "w"; break;
            case "24" : $rand_value = "x"; break;
            case "25" : $rand_value = "y"; break;
            case "26" : $rand_value = "z"; break;
            case "27" : $rand_value = "A"; break;
            case "28" : $rand_value = "B"; break;
            case "29" : $rand_value = "C"; break;
            case "30" : $rand_value = "D"; break;
            case "31" : $rand_value = "E"; break;
            case "32" : $rand_value = "F"; break;
            case "33" : $rand_value = "G"; break;
            case "34" : $rand_value = "H"; break;
            case "35" : $rand_value = "I"; break;
            case "36" : $rand_value = "J"; break;
            case "37" : $rand_value = "K"; break;
            case "38" : $rand_value = "L"; break;
            case "39" : $rand_value = "M"; break;
            case "40" : $rand_value = "N"; break;
            case "41" : $rand_value = "O"; break;
            case "42" : $rand_value = "P"; break;
            case "43" : $rand_value = "Q"; break;
            case "44" : $rand_value = "R"; break;
            case "45" : $rand_value = "S"; break;
            case "46" : $rand_value = "T"; break;
            case "47" : $rand_value = "U"; break;
            case "48" : $rand_value = "V"; break;
            case "49" : $rand_value = "W"; break;
            case "50" : $rand_value = "X"; break;
            case "51" : $rand_value = "Y"; break;
            case "52" : $rand_value = "Z"; break;
            case "53" : $rand_value = "0"; break;
            case "54" : $rand_value = "1"; break;
            case "55" : $rand_value = "2"; break;
            case "56" : $rand_value = "3"; break;
            case "57" : $rand_value = "4"; break;
            case "58" : $rand_value = "5"; break;
            case "59" : $rand_value = "6"; break;
            case "60" : $rand_value = "7"; break;
            case "61" : $rand_value = "8"; break;
            case "62" : $rand_value = "9"; break;
        }
        return $rand_value;
    }
}

if ( ! function_exists('get_rand_alpha_numeric'))
{
    /**
     * generate random alpha numeric
     *
     * @param $length
     * @return bool|string
     */
    function get_rand_alpha_numeric($length) {
        $rand_id = false;
        if ($length>0) {
            $rand_id="";
            for ($i=1; $i<=$length; $i++) {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,62);
                $rand_id .= assign_rand_value($num);
            }
        }
        return $rand_id;
    }
}

if ( ! function_exists('get_rand_numeric'))
{
    /**
     * generate random numeric
     *
     * @param $length
     * @return bool|string
     */
    function get_rand_numeric($length) {
        $rand_id = false;
        if ($length>0) {
            $rand_id="";
            for($i=1; $i<=$length; $i++) {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(53,62);
                $rand_id .= assign_rand_value($num);
            }
        }
        return $rand_id;
    }
}

if ( ! function_exists('get_rand_no_zero'))
{
    /**
     * generate random numeric without zero
     *
     * @param $length
     * @return bool|string
     */
    function get_rand_no_zero($length) {
        $rand_id = false;
        if ($length>0) {
            $rand_id="";
            for($i=1; $i<=$length; $i++) {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(54,62);
                $rand_id .= assign_rand_value($num);
            }
        }
        return $rand_id;
    }
}

if ( ! function_exists('get_rand_alpha'))
{
    /**
     * generate rand alphabet
     *
     * @param $length
     * @return bool|string
     */
    function get_rand_alpha($length) {
        $rand_id = false;
        if ($length>0) {
            $rand_id="";
            for($i=1; $i<=$length; $i++) {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,52);
                $rand_id .= assign_rand_value($num);
            }
        }
        return $rand_id;
    }
}

if ( ! function_exists('random_string'))
{
    /**
     * random string
     *
     * this function to generate random string
     *
     * @param string $type
     * @param int $length
     * @return mixed
     */
    function random_string($type = 'alnum',$length = 8)
    {
        $random = '';

        switch ($type)
        {
            case 'alpha':
                $random = get_rand_alpha($length);
                break;
            case 'alnum':
                $random = get_rand_alpha_numeric($length);
                break;
            case 'numeric':
                $random = get_rand_numeric($length);
                break;
            case 'nozero':
                $random = get_rand_no_zero($length);
                break;
        }

        return $random;
    }
}

if ( ! function_exists('br'))
{
    /**
     * Generates HTML BR tags based on number supplied
     *
     * @param	int	$count	Number of times to repeat the tag
     * @return	string
     */
    function br($count = 1)
    {
        return str_repeat('<br />', $count);
    }
}

if ( ! function_exists('nbs'))
{
    /**
     * Generates non-breaking space entities based on number supplied
     *
     * @param	int
     * @return	string
     */
    function nbs($num = 1)
    {
        return str_repeat('&nbsp;', $num);
    }
}

if ( ! function_exists('facebook_timespan'))
{
    /**
     * facebook time format
     *
     * generate format time like on facebook
     *
     * @param $date
     * @return string
     */
    function facebook_timespan($date)
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    }
}

if ( ! function_exists('strposa'))
{
    /**
     * multiple find character from string
     *
     * @param $haystack
     * @param array $needles
     * @param int $offset
     * @return bool|mixed
     */
    function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
    }
}

if ( ! function_exists('num_format'))
{
    function num_format($num = 0, $decimal = 0, $filesize = FALSE, $complex = FALSE)
    {
        $title = null;
        if($complex == FALSE)
        {
            if($num >= 1000000000)
            {
                $num = ($num/1000000000);
                (!is_float($num)) ? $decimal = 0 : $decimal = 2;
                $title = 'B';

                if($filesize)
                {
                    $title = 'GB';
                }
            }
            if($num >= 1000000)
            {
                $num = ($num/1000000);
                (!is_float($num)) ? $decimal = 0 : $decimal = 2;
                $title = 'M';

                if($filesize)
                {
                    $title = 'MB';
                }
            }
            if($num >= 1000)
            {
                $num = ($num/1000);
                (!is_float($num)) ? $decimal = 0 : $decimal = 2;
                $title = 'K';

                if($filesize)
                {
                    $title = 'KB';
                }
            }
        }

        $num = number_format($num, $decimal, '.', ',');

        return $num.$title;
    }
}

if ( ! function_exists('currency_format'))
{
    function currency_format($num = 0, $decimal = 0)
    {
        $minus = $num < 0 ? true : false;

        if ($minus)
        {
            $num = num_format($num, $decimal, FALSE, TRUE);

            $num = '<span style="color: red">'.$num.'</span>';
        }
        else
        {
            $num = num_format($num, $decimal, FALSE, TRUE);
        }

        return $num;
    }
}

if ( ! function_exists('heading'))
{
    /**
     * Heading
     *
     * Generates an HTML heading tag.
     *
     * @param	string	content
     * @param	int	heading level
     * @param	string
     * @return	string
     */
    function heading($data = '', $h = 1, $attributes = '')
    {
        return '<h'.$h._stringify_attributes($attributes).'>'.$data.'</h'.$h.'>';
    }
}

if ( ! function_exists('format_date'))
{
    function format_date($date,$format = 'default')
    {
        switch ($format)
        {
            case 'short':
                $format = 'd M Y';
                break;
            case 'full_date':
                $format = 'l, d F Y';
                break;
            case 'default':
                $format = 'l, d F Y - g:i A';
                break;
        }
        return \Carbon\Carbon::parse(date('Y-m-d H:i:s',strtotime($date)))->format($format);
    }
}

