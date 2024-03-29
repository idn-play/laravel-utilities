<?php
/**
 * @package     IdnPlay\Laravel\Utils\Bus\Repository
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-02-07
 * @updated     2020-02-07
 **/
namespace IdnPlay\Laravel\Utils\Bus;

use IdnPlay\Laravel\Utils\Library\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait Repository
{
    protected $db_connection;

    /**
     * manual load connection db
     *
     * @param string $connection
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function connection($connection = 'default')
    {
        $connection = $connection == 'default' ? (!is_null($this->db_connection) ? $this->db_connection : config('database.default')) : $connection;

        return DB::connection($connection);
    }

    /**
     * manual load table w/o model
     *
     * @param $table_name
     * @param string $connection
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table_name,$connection = 'default')
    {
        $connection = $connection == 'default' ? (!is_null($this->db_connection) ? $this->db_connection : config('database.default')) : $connection;

        return $this->connection($connection)->table($table_name);
    }

    /**
     * manual insert table w/o model
     *
     * @param $table_name
     * @param array $insert_data
     * @param string $connection
     * @return bool
     */
    public function insert_table($table_name,$insert_data = array(),$connection = 'default')
    {
        if (is_array($insert_data) && count($insert_data) > 0)
        {
            return $this->table($table_name,$connection)->insert($insert_data);
        }
        else
        {
            return false;
        }
    }

    /**
     * manual insert table w/o model and return insert id
     *
     * @param $table_name
     * @param array $insert_data
     * @param string $connection
     * @return bool
     */
    public function insert_return_id_table($table_name,$insert_data = array(),$connection = 'default')
    {
        if (is_array($insert_data) && count($insert_data) > 0)
        {
            return $this->table($table_name,$connection)->insertGetId($insert_data);
        }
        else
        {
            return false;
        }
    }

    /**
     * manual update table w/o model
     *
     * @param $table_name
     * @param $where
     * @param array $update_data
     * @param string $connection
     * @return bool|int
     */
    public function update_table($table_name,$where,$update_data = array(),$connection = 'default')
    {
        if (!is_array($where))
        {
            return false;
        }

        if (is_array($update_data) && count($update_data) > 0)
        {
            return $this->table($table_name,$connection)->where($where)->update($update_data);
        }
        else
        {
            return false;
        }
    }

    /**
     * manual delete table w/o model
     *
     * @param $table_name
     * @param $where
     * @param string $connection
     * @return bool|int
     */
    public function delete_table($table_name,$where,$connection = 'default')
    {
        if (!is_array($where))
        {
            return false;
        }

        return $this->table($table_name,$connection)->where($where)->delete();
    }

    /**
     * check database has table or not
     *
     * @param $table_name
     * @param string $connection (optional connection)
     * @return bool
     */
    public function has_table($table_name,$connection = 'default')
    {
        $connection = $connection == 'default' ? (!is_null($this->db_connection) ? $this->db_connection : config('database.default')) : $connection;

        return Schema::connection($connection)->hasTable($table_name);
    }

    /**
     * for execute raw query
     *
     * @param $query
     * @param string $connection
     * @return \Illuminate\Support\Collection
     */
    public function query($query,$connection = 'default')
    {
        $connection = $connection == 'default' ? (!is_null($this->db_connection) ? $this->db_connection : config('database.default')) : $connection;

        return collect($this->connection($connection)->select($query));
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw($query){
        return DB::raw($query);
    }

    /**
     * force group by statement
     *
     * @param string $connection
     * @return bool
     */
    public function mysql_force_group_by($connection = 'default')
    {
        $connection = $connection == 'default' ? (!is_null($this->db_connection) ? $this->db_connection : config('database.default')) : $connection;

        return $this->connection($connection)->unprepared('SET sql_mode=(SELECT REPLACE(@@sql_mode, \'ONLY_FULL_GROUP_BY\', \'\'));');
    }

    /**
     * laod repository redis
     *
     * @return Redis
     */
    public function redis()
    {
        return (new Redis());
    }
}
