<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;

/**
 * 斗鱼直播
 * Class DouYv
 * @package Itxiao6\Live\Driver
 */
class DouYv
{

    public static function getInterface($live_url)
    {
        /**
         * 获取内容
         */
        preg_match("/.*douyu.com\/(\d+)/is", $live_url, $matchs);
        /**
         * 判断url 是否合法
         */
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        /**
         * 获取数据
         */
        $apiResult =  json_decode(HTTP::request('https://m.douyu.com/html5/live?roomId='. $matchs[1]),1);
        if(empty($apiResult['data'])){
            throw new \Exception('获取房间信息失败');
        }
        if(!empty($apiResult['error'])){
            throw new \Exception( $apiResult['data']);
        }
        $resultArr = array(
            'status'=>isset($apiResult['data']['error'])?$apiResult['data']['error']:1==0?1:0,
            'poster'=>'../addons/ewei_shopv2/static/images/nopic.png',
            'hls_url'=>$apiResult['data']['hls_url']
        );
        if(!empty($resultArr['status'])){
            $html  = $apiResult = json_decode(HTTP::request('https://m.douyu.com/'. $matchs[1]));
        }
        return $resultArr;
    }
}