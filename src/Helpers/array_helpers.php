<?php
if ( ! function_exists('array_push_multidimension'))
{
    /**
     * push array multi dimension
     *
     * @param array $array_data
     * @param array $array_push
     * @param string $position
     * @return array
     */
    function array_push_multidimension($array_data = array(), $array_push = array(), $position = 'last')
    {
        $array = array();
        if(is_array($array_data))
        {
            if($position == 'last')
            {
                $position_key = @end(array_keys($array_data));
            }
            else
            {
                $position_key = $position;
            }

            foreach($array_data as $key => $val)
            {
                if($position != 'first'){$array[$key] = $val;}

                if($key == $position_key || $position == 'first')
                {
                    foreach($array_push as $push_key => $push_val)
                    {
                        $array[$push_key] = $push_val;
                        if(is_array($push_key))
                        {
                            return array_push_multidimension($array, $push_key, $position);
                        }
                    }
                }

                if($position == 'first'){$array[$key] = $val;}
            }
        }
        else
        {
            foreach($array_push as $push_key => $push_val)
            {
                $array[$push_key] = $push_val;
                if(is_array($push_key))
                {
                    return array_push_multidimension($array, $push_key);
                }
            }
        }

        return ($array);
    }
}

if ( ! function_exists('array_search_multidimension')) {

    /**
     * search from multi dimension array
     *
     * @param $array
     * @param $key
     * @param $value
     * @return array
     */
    function array_search_multidimension($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, array_search_multidimension($subarray, $key, $value));
            }
        }

        return $results;
    }
}
