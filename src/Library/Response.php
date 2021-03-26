<?php
/**
 * @package     IdnPlay\Utils\Library\Response
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-01-20
 * @updated     2021-03-26
 **/
namespace IdnPlay\Laravel\Utils\Library;

class Response
{
    static function api($data = NULL, $http_code = 200)
    {
        $debug = request()->header('debug') ?? false;

        if ($http_code == 200 || $http_code == NULL)
        {
            $http_code = 200;

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

            if (!!$gzip)
            {
                /*
                 * set env response
                 */
                $offset = 60 * 60;
                $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";

                $response = gzencode(json_encode($response),9);

                return response($response)->setStatusCode($http_code != NULL ? $http_code : 200)->withHeaders([
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => request()->getMethod(),
                    'Content-type' => 'application/json; charset=utf-8',
                    'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    'Content-Length' => strlen($response),
                    'Content-Encoding' => 'gzip',
                    'Vary' => 'Accept-Encoding',
                    'Pragma' => 'no-cache',
                    'expires' => $expire,
                ]);
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

            if (!!$gzip)
            {
                /*
                 * set env response
                 */
                $offset = 60 * 60;
                $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";

                $response = gzencode(json_encode($response),9);

                return response($response)->setStatusCode($http_code != NULL ? $http_code : 500)->withHeaders([
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => request()->getMethod(),
                    'Content-type' => 'application/json; charset=utf-8',
                    'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    'Content-Length' => strlen($response),
                    'Content-Encoding' => 'gzip',
                    'Vary' => 'Accept-Encoding',
                    'Pragma' => 'no-cache',
                    'expires' => $expire,
                ]);
            }

            return response()->json($response,$http_code,[],JSON_PRETTY_PRINT);
        }
    }

    static function gzip($data = NULL, $http_code = 200)
    {
        return self::api($data, $http_code,true);
    }
}
