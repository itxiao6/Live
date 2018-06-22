<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;
use Itxiao6\Live\Driver;

/**
 * 熊猫直播
 * Class XiongMao
 * @package Itxiao6\Live\Driver
 */
class XiongMao extends Driver
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
     * 解析
     * @return $this
     * @throws \Throwable
     */
    protected function analysis()
    {
        preg_match("/.*panda.tv\/(\d+)/is", $this -> live_url, $matchs);
        /**
         * 校验视频地址是否支持
         */
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        /**
         * 判断直播类型
         */
        if(strpos($matchs[0],'xingyan')!==false){
            $apiResult = json_decode(HTTP::request('http://m.api.xingyan.panda.tv/room/baseinfo?xid='. $matchs[1]),1);
        }else{
            $apiResult = json_decode(HTTP::request('https://room.api.m.panda.tv/index.php?callback=&method=room.shareapi&roomid='. $matchs[1]),1);
        }
        /**
         * 获取其他信息
         */
        $apiotherResult = json_decode(HTTP::request('https://api.m.panda.tv/stream/room/pull/get?roomid='.$matchs[1].'&roomkey='.$apiResult['data']['videoinfo']['room_key'].'&definition_option=1&hardware=1'),1);

        $sign = $apiotherResult['data'][$matchs[1]]['sign'];
        $ts = $apiotherResult['data'][$matchs[1]]['ts'];
        if(!empty($apiResult['errno'])){
            throw new \Exception('获取房间信息失败');
        }
        /**
         * 判断直播类型
         */
        if(strpos($matchs[0],'xingyan')!==false){
            $this -> status = $apiResult['data']['roominfo']['playstatus']==1?1:0;
            $this -> poster = $apiResult['data']['roominfo']['photo'];
            $this -> hls_url = $apiResult['data']['videoinfo']['hlsurl'];
        }else{
            $this -> status = $apiResult['data']['roominfo']['status']==2?1:0;
            $this -> poster = $apiResult['data']['roominfo']['pictures']['img'];
            $this -> hls_url = $apiResult['data']['videoinfo']['address']."?sign=".$sign."".$ts;
        }
        return $this;
    }

}