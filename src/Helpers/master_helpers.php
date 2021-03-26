<?php
/**
 * @package     Helpers - Master Helpers
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 **/

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use IdnPlay\Laravel\Utils\Library\Encryption\Aes;
use IdnPlay\Laravel\Utils\Library\Response;
use Illuminate\Http\JsonResponse;

if ( ! function_exists('response_api'))
{
    /**
     * helper for responing api
     *
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    function response_api($data = null,$code = 200)
    {
        return Response::api($data,$code);
    }
}

if ( ! function_exists('response_gzip'))
{
    /**
     * helper for responing gzip
     *
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    function response_gzip($data = null,$code = 200)
    {
        return Response::gzip($data,$code);
    }
}

if ( ! function_exists('aes_encrypt'))
{
    /**
     * aes encrypt
     *
     * for encrypt string
     *
     * @param $str
     * @param bool $keyDefault
     * @return string
     */
    function aes_encrypt($str,$keyDefault = false)
    {
        if (!$keyDefault)
        {
            Aes::setKey(env('ENCRYPT_KEY'));
        }

        return Aes::enkrip($str);
    }
}

if ( ! function_exists('aes_decrypt'))
{
    /**
     * aes decrypt
     *
     * for decrypt string from aec encryption
     *
     * @param $str
     * @param bool $keyDefault
     * @return string
     */
    function aes_decrypt($str,$keyDefault = false)
    {
        if (!$keyDefault)
        {
            Aes::setKey(env('ENCRYPT_KEY'));
        }

        return Aes::dekrip($str);
    }
}

if ( ! function_exists('convert_date')) {

    /**
     * convert date from some format to different format
     *
     * @param $from
     * @param $to
     * @param $date
     * @return string
     */
    function convert_date($from, $to, $date)
    {
        return Carbon::createFromFormat($from,$date)->format($to);
    }
}

if ( ! function_exists('is_json')) {

    /**
     * function to check string is json or not
     *
     * @param $string
     * @return string
     */
    function is_json($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}

if ( ! function_exists('get_browser_data'))
{
    function get_browser_data()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $platform = 'Unknown';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/OPR/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        else
        {
            $bname = 'Other';
            $ub = "Other";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern,
            'alias' 	=> strtolower(str_replace(' ', '-', $ub).'-'.$version.'-'.$platform)
        );
    }
}

if ( ! function_exists('date_range'))
{
    /**
     * date range list
     *
     * @param $date_start
     * @param $date_end
     * @param string $format
     * @return CarbonInterface[]
     */
    function date_range($date_start, $date_end, $format = 'Y-m-d')
    {
        $date_start = format_date($date_start,'Y-m-d H:i:s');
        $date_end = format_date($date_end,'Y-m-d H:i:s');

        $period = CarbonPeriod::create($date_start, $date_end);

        foreach ($period as $date) {
            echo $date->format($format);
        }

        return $period->toArray();
    }
}

if ( ! function_exists('diff_date'))
{

    function diff_date($date_start, $date_end, $format = 'year')
    {
        $date_start = format_date($date_start,'Y-m-d H:i:s');
        $date_end = format_date($date_end,'Y-m-d H:i:s');

        $date_start = Carbon::parse($date_start);
        $date_end   = Carbon::parse($date_end);

        switch ($format)
        {
            case 'year' :
                return $date_start->diffInYears($date_end);
                break;
            case 'month' :
                return $date_start->diffInMonths($date_end);
                break;
            case 'day' :
                return $date_start->diffInDays($date_end);
                break;
            case 'week' :
                return $date_start->diffInWeeks($date_end);
                break;
            case 'hour' :
                return $date_start->diffInHours($date_end);
                break;
            case 'minute' :
                return $date_start->diffInMinutes($date_end);
                break;
            case 'second' :
                return $date_start->diffInSeconds($date_end);
                break;
            default :
                return $date_start->diffInYears($date_end);
                break;
        }
    }
}
