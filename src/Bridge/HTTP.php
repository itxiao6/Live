<?php
namespace Itxiao6\Live\Bridge;

/**
 * 工具类
 * Class HTTP
 * @package Itxiao6\Live\Bridge
 */
class HTTP
{
    public static function request($uri,$method = 'GET'){
        try{
            $response = (new \GuzzleHttp\Client)->request($method, $uri);
            return $response->getBody()->getContents();
        }catch (\Throwable $exception){
            throw $exception;
        }
    }
}