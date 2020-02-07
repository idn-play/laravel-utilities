<?php
/**
 * @package     IdnPlay\Utils\Library\Response
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-01-20
 * @updated     2020-01-20
 **/
namespace IdnPlay\Laravel\Utils\Library;

class Response
{
    static function api($data = NULL, $http_code = NULL)
    {
        $debug = request()->header('debug') ?? false;

        if ($http_code == 200 || $http_code == NULL)
        {
            $data = is_array($data) || is_object($data) ? $data : array('message'=>$data);

            $response = [
                'success'       => true,
                'response_code' => $http_code != NULL ? $http_code : 200,
                'data'          => $data
            ];

            if (!!$debug)
            {
                $response['debug'] = [
                    'query' => request()->all(),
                    'header' => request()->header()
                ];
            }

            return response()->json($response,$http_code,[],JSON_PRETTY_PRINT);
        }
        else{
            $response = [
                'success'=>false,
                'response_code' => $http_code != NULL ? $http_code : 500,
                'error' => [
                    'error_code' => $http_code != NULL ? $http_code : 500,
                    'error_message' => $data
                ]
            ];

            if (!!$debug)
            {
                $response['debug'] = [
                    'query' => request()->all(),
                    'header' => request()->header()
                ];
            }

            return response()->json($response,$http_code,[],JSON_PRETTY_PRINT);
        }
    }
}
