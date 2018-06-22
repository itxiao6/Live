<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;

/**
 * 辣椒直播
 * Class LaJiao
 * @package Itxiao6\Live\Driver
 */
class HuaJiao
{

    public static function getInterface($live_url)
    {
        preg_match("/.*huajiao.com\/l\/(\d+)/is", $live_url, $matchs);
        /**
         * 判断url 是否合法
         */
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        /**
         * 获取数据
         */
        $apiResult =  HTTP::request('http://h.huajiao.com/l/index?liveid='. $matchs[1]);
        preg_match('@"feed":(.*?)"title"@is', $apiResult, $feedInfo);
        if(empty($feedInfo)){
            throw new \Exception('获取房间信息失败');
        }
        $feedInfo = rtrim($feedInfo[1], ',');
        $feedInfo .= '}';
        $feedInfo = json_decode($feedInfo, true);
        $resultArr = array(
            'status'=>$feedInfo['paused']=='N'?1:0,
            'poster'=>$feedInfo['image'],
            /*'hls_url'=>'http://qh.cdn.huajiao.com/live_huajiao_v2/'. $feedInfo['sn']. '/index.m3u8'*/
            'hls_url'=>!empty($feedInfo['m3u8']) ? $feedInfo['m3u8'] : 'http://qh.cdn.huajiao.com/live_huajiao_v2/'. $feedInfo['sn']. '/index.m3u8'
        );
        return $resultArr;
    }
}