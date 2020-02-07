<?php
/**
 * @package     Helpers - Mobile Detect Helpers
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 **/

if  ( ! function_exists('mobile_detect'))
{
    /**
     * load mobile detect library
     *
     * @return Mobile_Detect
     */
    function mobile_detect()
    {
        return new Mobile_Detect;
    }
}

if  ( ! function_exists('is_mobile'))
{
    /**
     * check is device mobile or not
     *
     * @return bool
     */
    function is_mobile()
    {
        return mobile_detect()->isMobile();
    }
}

if  ( ! function_exists('is_tablet'))
{
    /**
     * check device is tablet or not
     *
     * @return bool
     */
    function is_tablet()
    {
        return mobile_detect()->isTablet();
    }
}

if  ( ! function_exists('is_ios'))
{
    /**
     * check device is ios or not
     *
     * @return mixed
     */
    function is_ios()
    {
        return mobile_detect()->isiOS();
    }
}

if  ( ! function_exists('is_android'))
{
    /**
     * check device is android or not
     *
     * @return mixed
     */
    function is_android()
    {
        return mobile_detect()->isAndroidOS();
    }
}

if  ( ! function_exists('is_desktop'))
{
    /**
     * check device is desktop or not
     *
     * @return bool
     */
    function is_desktop()
    {
        $desktop = (!is_mobile() && !is_tablet()) ? true : false;
        return $desktop;
    }
}

if  ( ! function_exists('device_type'))
{
    /**
     * check device type parameter
     *
     * @param null $param |tablet|phone|computer
     * @return bool|int|string|null
     */
    function device_type($param = NULL)
    {
        if(is_null($param)){
            $device = (is_mobile() ? (is_tablet() ? 'tablet' : 'phone') : 'computer');
        }
        else
        {
            $device = mobile_detect()->is($param);
        }

        return $device;
    }
}
