<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;
use Itxiao6\Live\Driver;

/**
 * 斗鱼直播
 * Class DouYv
 * @package Itxiao6\Live\Driver
 */
class DouYv extends Driver
{
    /**
     * 获取video source
     * @return string
     */
    public function get_source()
    {
        return '<source src="'.$this -> get_hls().'" type="application/x-mpegURL">';
    }

    /**
     * 解析直播
     * @return mixed|void
     * @throws \Throwable
     */
    public function analysis()
    {
        /**
         * 获取内容
         */
        preg_match("/.*douyu.com\/(\d+)/is", $this -> live_url, $matchs);
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
        /**
         * 房间数据是否正确
         */
        if(empty($apiResult['data'])){
            throw new \Exception('获取房间信息失败');
        }
        /**
         * 是否有其他 错误
         */
        if(!empty($apiResult['error'])){
            throw new \Exception( $apiResult['data']);
        }
        /**
         * 获取状态
         */
        $this -> status = isset($apiResult['data']['error'])?$apiResult['data']['error']:1==0?1:0;
        /**
         * 获取直播封面
         */
        $this -> poster = '';
        /**
         * 获取hls 播放地址
         */
        $this -> hls_url = $apiResult['data']['hls_url'];
    }

}