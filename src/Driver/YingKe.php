<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;
use Itxiao6\Live\Driver;

/**
 * 映客直播
 * Class YingKe
 * @package Itxiao6\Live\Driver
 */
class YingKe extends Driver
{
    /**
     * 解析直播
     * @return mixed|void
     * @throws \Throwable
     */
    protected function analysis()
    {
        preg_match("/.*id=(\d+)/is", $this -> live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception( '视频地址参数错误或所选来源错误');
        }
        $roomid = $matchs[1];
        $roomInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/Get_live_addr?liveid='. $roomid),1);
//        $roomInfo = json_decode($roomInfo['content'], true);
        if(empty($roomInfo) || !empty($roomInfo['error_code'])){
            throw new \Exception( '获取房间信息失败');
        }
        $resultArr = array('status'=>$roomInfo['data']['status']);
        if(!empty($roomInfo['data']['status'])){
            /*$resultArr['hls_url'] = $roomInfo['data']['file'][0]. '/playlist.m3u8';*/
            $resultArr['hls_url'] = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
            $resultArr['hls_url'] = str_replace('rtmp://', 'http://', $resultArr['hls_url']);
            $resultArr['rtmp_url'] = $roomInfo['data']['file'][0];
            $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/user_info?liveid='. $roomid),1);
//            $userInfo = json_decode($userInfo['content'], true);
            if(!empty($userInfo) && empty($userInfo['error_code']) && !empty($userInfo['data'])){
                $resultArr['poster'] = $userInfo['data']['image'];
            }
        }else{
            //回放
            preg_match("/.*uid=(\d+)/is", $this -> live_url, $matchs);
            $uid = $matchs[1];

            /*$resultArr['hls_url'] = $roomInfo['data']['file'][0]. '/playlist.m3u8';*/
//            $resultArr['hls_url'] = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
//            var_dump($resultArr['hls_url']);die();
//            $resultArr['hls_url'] = str_replace('rtmp://', 'http://', $resultArr['hls_url']);
//            $resultArr['rtmp_url'] = $roomInfo['data']['file'][0];
            $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/mobile_share_api?uid='.$uid.'&liveid='. $roomid),1);
//            $userInfo = json_decode($userInfo['content'], true);
//            var_dump($userInfo);die();
            $resultArr['hls_url'] = count($userInfo['data']['media_info']['file'])>0?$userInfo['data']['media_info']['file'][0]:null;
            $resultArr['poster'] = $userInfo['data']['media_info']['image'];
            $resultArr['status'] = 1;

            if(empty($userInfo['data']['media_info']['file'])){
                $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/web/live_share_pc?uid='.$uid.'&id='. $roomid),1);
//                $userInfo = json_decode($userInfo['content'], true);
                $resultArr['hls_url'] = $userInfo['data']['file']['record_url'];
                $resultArr['poster'] = $userInfo['data']['file']['pic'];
                $resultArr['status'] = 1;
            }
            /*return $userInfo;
            if(!empty($userInfo) && empty($userInfo['error_code']) && !empty($userInfo['data'])){
                $resultArr['poster'] = $userInfo['data']['image'];
            }*/
        }
        $this -> hls_url = $resultArr['hls_url'];
    }
    protected function test()
    {
        preg_match("/.*id=(\d+)/is", $this -> live_url, $matchs);
        /**
         * 获取房间id
         */
        $room_id = isset($matchs[1])?$matchs[1]:0;
        /**
         * 判断地址是否合法
         */
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        /**
         * 获取房间信息
         */
        $roomInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/Get_live_addr?liveid='. $room_id),1);
        /**
         * 判断获取房间信息是否成功
         */
        if(empty($roomInfo) || !empty($roomInfo['error_code'])){
            throw new \Exception('获取房间信息失败');
        }
        /**
         * 获取房间状态
         */
        $this -> status = $roomInfo['data']['status'];
        /**
         * 判断是否为直播
         */
        if(!empty($roomInfo['data']['status'])){
            $hls_stream_addr = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
            /**
             * 获取hls_url 播放地址
             */
            $this -> hls_url = str_replace('rtmp://', 'http://', $hls_stream_addr);
            /**
             * 获取rtmp 拉流地址
             */
            $this -> rtmp_url = $roomInfo['data']['file'][0];
            /**
             * 获取用户信息
             */
            $userInfo = json_decode(HTTP::request('http://webapi.busi.inke.cn/mobile/user_info?liveid='. $room_id),1);
            /**
             * 判断房间信息是否获取成功
             */
            if(!empty($userInfo) && empty($userInfo['error_code']) && !empty($userInfo['data'])){
                $this -> poster = $userInfo['data']['image'];
            }
        }
        /**
         * 回放
         */
        else{
            //回放
            preg_match("/.*uid=(\d+)/is", $this -> live_url, $matchs);
            $uid = $matchs[1];

            /*$resultArr['hls_url'] = $roomInfo['data']['file'][0]. '/playlist.m3u8';*/
            $resultArr['hls_url'] = $roomInfo['data']['live_addr'][0]['hls_stream_addr'];
            $resultArr['hls_url'] = str_replace('rtmp://', 'http://', $resultArr['hls_url']);
            $resultArr['rtmp_url'] = $roomInfo['data']['file'][0];
            $userInfo = ihttp_get('http://webapi.busi.inke.cn/mobile/mobile_share_api?uid='.$uid.'&liveid='. $room_id);
            $userInfo = json_decode($userInfo['content'], true);

            $resultArr['hls_url'] = $userInfo['data']['media_info']['file'][0];
            $resultArr['poster'] = $userInfo['data']['media_info']['image'];
            $resultArr['status'] = 1;

            if(empty($userInfo['data']['media_info']['file'])){
                $userInfo = ihttp_get('http://webapi.busi.inke.cn/web/live_share_pc?uid='.$uid.'&id='. $room_id);
                $userInfo = json_decode($userInfo['content'], true);
                $resultArr['hls_url'] = $userInfo['data']['file']['record_url'];
                $resultArr['poster'] = $userInfo['data']['file']['pic'];
                $resultArr['status'] = 1;
            }
        }
    }
    /**
     * 获取video source
     * @return string
     */
    public function get_source()
    {
        return '<source src="'.$this -> get_hls().'" type="application/x-mpegURL">';
    }
}