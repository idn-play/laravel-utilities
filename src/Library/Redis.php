<?php
/**
 * @package     IdnPlay\Utils\Library\Redis
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-02-07
 * @updated     2020-02-07
 **/
namespace IdnPlay\Laravel\Utils\Library;

use Illuminate\Support\Facades\Redis as LibRedis;

class Redis
{
    /**
     * set redis data permanently and replace if exist old data
     *
     * @param $key
     * @param $value
     * @param int $expire_in [second]
     * @return mixed
     */
    public function set($key,$value,$expire_in = null)
    {
        if (is_null($expire_in))
        {
            return LibRedis::set($key,$value);
        }
        else
        {
            return LibRedis::set($key,$value,'EX',$expire_in);
        }
    }

    /**
     * set redis data permanently if old data not exist
     *
     * @param $key
     * @param $value
     * @param int $expire_in [second]
     * @return mixed
     */
    public function set_if_not_exist($key,$value,$expire_in = null)
    {
        if (is_null($expire_in))
        {
            return LibRedis::setnx($key,$value);
        }
        else
        {
            return LibRedis::setnx($key,$value,'EX',$expire_in);
        }
    }

    /**
     * get redis value by key
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return LibRedis::get($key);
    }

    /**
     * delete redis data
     *
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        return LibRedis::delete($key);
    }

    /**
     * check redis data is exist or not
     *
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return LibRedis::exists($key);
    }
}
