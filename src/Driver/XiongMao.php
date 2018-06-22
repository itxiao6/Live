<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;

/**
 * 熊猫直播
 * Class XiongMao
 * @package Itxiao6\Live\Driver
 */
class XiongMao
{
    private static $api = 'https://xingyan.panda.tv/28722838';

    public static function getInterface($live_url)
    {
        preg_match("/.*panda.tv\/(\d+)/is", $live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        if(strpos($matchs[0],'xingyan')!==false){
            $apiResult = json_decode(HTTP::request('http://m.api.xingyan.panda.tv/room/baseinfo?xid='. $matchs[1]),1);
        }else{
            $apiResult = json_decode(HTTP::request('https://room.api.m.panda.tv/index.php?callback=&method=room.shareapi&roomid='. $matchs[1]),1);
        }
        /**
         * 获取其他信息
         */
        $apiotherResult = json_decode(HTTP::request('https://api.m.panda.tv/stream/room/pull/get?roomid='.$matchs[1].'&roomkey='.$apiResult['data']['videoinfo']['room_key'].'&definition_option=1&hardware=1'),1);

//        $apiotherResult = json_decode($apiother['content'], true);
        $sign = $apiotherResult['data'][$matchs[1]]['sign'];
        $ts = $apiotherResult['data'][$matchs[1]]['ts'];
        /*dump($sign,$ts);
        die();*/
        if(!empty($apiResult['errno'])){
            throw new \Exception('获取房间信息失败');
        }
        //dump($apiResult);
        if(strpos($matchs[0],'xingyan')!==false){
            $resultArr = array(
                'status'=>$apiResult['data']['roominfo']['playstatus']==1?1:0,
                'poster'=>$apiResult['data']['roominfo']['photo'],
                'hls_url'=>$apiResult['data']['videoinfo']['hlsurl'],
            );
        }else{
            $resultArr = array(
                'status'=>$apiResult['data']['roominfo']['status']==2?1:0,
                'poster'=>$apiResult['data']['roominfo']['pictures']['img'],
                'hls_url'=>$apiResult['data']['videoinfo']['address']."?sign=".$sign."".$ts,
            );
        }
        return $resultArr;
    }
}