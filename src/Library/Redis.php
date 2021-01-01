<?php
/**
 * @package     IdnPlay\Utils\Library\Redis
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-02-07
 * @updated     2021-01-01
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
            return LibRedis::set($key,$value,'EX',$expire_in, 'NX');
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

    /**
     * [zrange description]
     * return the members of a sorted set by index
     * @param  [str] $keyname [keyname]
     * @param  [int] $start   [start index]
     * @param  [int] $stop    [stop index]
     * @return [arr]          [array of objects]
     */
    public function z_range($key, $start, $stop)
    {
        return LibRedis::zrange($key, $start, $stop);
    }

    /**
     * [zaddNx description]
     * create a new sorted set and/or insert new value to sorted set
     * @param  [str] $keyName [key name]
     * @param  [float] $score   [score]
     * @param  [str] $member  [member value]
     * @return [arr]          [array of object]
     */
    public function z_add_nx($key, $score, $member)
    {
        return LibRedis::zadd($key, 'NX', $score, $member);;
    }

    /**
     * [zscore description]
     * return the score of a member from sorted set
     * @param  [str] $member [member value]
     * @return [arr]         [array of object]
     */
    public function z_score($key, $member)
    {
        return LibRedis::zscore($key, $member);
    }

    /**
     * [zrangebyscore description]
     * return list of member from a set with score filter
     * @param  [string] $keyName [key name]
     * @param  [string] $min     [min cap]
     * @param  [string] $max     [max cap]
     * @return [arr]             [array of object]
     */
    public function z_range_by_score($key, $min, $max)
    {
        return LibRedis::zrangebyscore($key, $min, $max);
    }

    /**
     * [zrem description]
     * remove member from a sorted set
     * @param  [string] $keyName [key name]
     * @param  [string] $member  [member name]
     * @return [bool]            [description]
     */
    public function zrem($key, $member)
    {
        return LibRedis::zrem($key, $member);
    }

    /**
     * [zaddCh description]
     * create a new sorted set and/or insert and/or update value to sorted set
     * @param  [str] $keyName [key name]
     * @param  [float] $score   [score]
     * @param  [str] $member  [member value]
     * @return [arr]          [array of object]
     */
    public function z_add_ch($key, $score, $member)
    {
        return LibRedis::zadd($key, 'CH', $score, $member);
    }

    /**
     * [zrangebyscore description]
     * return list of member from a set with score filter
     * @param  [string] $keyName [key name]
     * @param  [string] $min     [min cap]
     * @param  [string] $max     [max cap]
     * @return [boolean]         
     */
    public function z_rem_range_by_score($key, $min, $max)
    {
        return LibRedis::zremrangebyscore($key, $min, $max);
    }
}
