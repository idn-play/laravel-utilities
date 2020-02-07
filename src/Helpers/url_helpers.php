<?php
/**
 * @package     Helpers - Url Helpers
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 **/

if ( ! function_exists('base_url'))
{
    /**
     * generator base url
     *
     * @driver from codeigniter
     * @param string $uri
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function base_url($uri = '')
    {
        return $uri == '' ? url($uri).'/' : url($uri);
    }
}

if ( ! function_exists('is_https'))
{
    /**
     * Is HTTPS?
     *
     * Determines if the application is accessed via an encrypted
     * (HTTPS) connection.
     *
     * @return	bool
     */
    function is_https()
    {
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        {
            return TRUE;
        }
        elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
        {
            return TRUE;
        }

        return FALSE;
    }
}

if ( ! function_exists('url_title'))
{
    /**
     * Create URL Title
     *
     * Takes a "title" string as input and creates a
     * human-friendly URL string with a "separator" string
     * as the word separator.
     *
     * @param	string	$str		Input string
     * @param	string	$separator	Word separator
     *			(usually '-' or '_')
     * @param	bool	$lowercase	Whether to transform the output string to lowercase
     * @return	string
     */
    function url_title($str, $separator = '-', $lowercase = FALSE)
    {
        if ($separator === 'dash')
        {
            $separator = '-';
        }
        elseif ($separator === 'underscore')
        {
            $separator = '_';
        }

        $q_separator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;'			=> '',
            '[^\w\d _-]'		=> '',
            '\s+'			=> $separator,
            '('.$q_separator.')+'	=> $separator
        );

        $str = strip_tags($str);
        foreach ($trans as $key => $val)
        {
            $str = preg_replace('#'.$key.'#i'.'u', $val, $str);
        }

        if ($lowercase === TRUE)
        {
            $str = strtolower($str);
        }

        return trim(trim($str, $separator));
    }
}
