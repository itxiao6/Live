<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;

/**
 * 映客直播
 * Class YingKe
 * @package Itxiao6\Live\Driver
 */
class YingKe
{

    public static function getInterface($live_url)
    {
        preg_match("/.*id=(\d+)/is", $live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        $roomid = $matchs[1];
        $roomInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/Get_live_addr?liveid='. $roomid),1);
        if(empty($roomInfo) || !empty($roomInfo['error_code'])){
            throw new \Exception('获取房间信息失败');
        }
        $resultArr = array('status'=>$roomInfo['data']['status']);
        if(!empty($roomInfo['data']['status'])){
            /*$resultArr['hls_url'] = $roomInfo['data']['file'][0]. '/playlist.m3u8';*/
            $resultArr['hls_url'] = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
            $resultArr['hls_url'] = str_replace('rtmp://', 'http://', $resultArr['hls_url']);
            $resultArr['rtmp_url'] = $roomInfo['data']['file'][0];
            $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/user_info?liveid='. $roomid),1);
            if(!empty($userInfo) && empty($userInfo['error_code']) && !empty($userInfo['data'])){
                $resultArr['poster'] = $userInfo['data']['image'];
            }
        }else{
            //回放
            preg_match("/.*uid=(\d+)/is", $live_url, $matchs);
            $uid = $matchs[1];

            /*$resultArr['hls_url'] = $roomInfo['data']['file'][0]. '/playlist.m3u8';*/
            $resultArr['hls_url'] = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
            $resultArr['hls_url'] = str_replace('rtmp://', 'http://', $resultArr['hls_url']);
            $resultArr['rtmp_url'] = $roomInfo['data']['file'][0];
            $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/mobile_share_api?uid='.$uid.'&liveid='. $roomid),1);

            $resultArr['hls_url'] = $userInfo['data']['media_info']['file'][0];
            $resultArr['poster'] = $userInfo['data']['media_info']['image'];
            $resultArr['status'] = 1;

            if(empty($userInfo['data']['media_info']['file'])){
                $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/web/live_share_pc?uid='.$uid.'&id='. $roomid),1);
                $resultArr['hls_url'] = $userInfo['data']['file']['record_url'];
                $resultArr['poster'] = $userInfo['data']['file']['pic'];
                $resultArr['status'] = 1;
            }
        }
        return $resultArr;
    }
}