<?php
namespace Itxiao6\Live\Bridge;

/**
 * 工具类
 * Class HTTP
 * @package Itxiao6\Live\Bridge
 */
class HTTP
{
    /**
     * 发起一个同步请求
     * @param $uri
     * @param string $method
     * @param array $header
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public static function request($uri,$method = 'GET',$header = []){
        try{
            /**
             * 定义附件选项
             */
            $options = [];
            /**
             * 判断自定义协议头
             */
            if(count($header) > 0){
                $options['headers'] = $header;
            }
            /**
             * 请求
             */
            $response = (new \GuzzleHttp\Client)->request($method, $uri,$options);
            /**
             * 获取响应的内容
             */
            return $response->getBody()->getContents();
        }catch (\Throwable $exception){
            throw $exception;
        }
    }
}