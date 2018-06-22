<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;
use Itxiao6\Live\Driver;

/**
 * 辣椒直播
 * Class LaJiao
 * @package Itxiao6\Live\Driver
 */
class HuaJiao extends Driver
{
    /**
     * 获取video source
     * @return string
     */
    public function get_source()
    {
        return '<source src="'.$this -> get_hls().' type="application/x-mpegURL"">';
    }
    /**
     * 解析直播
     * @throws \Throwable
     */
    public function analysis(){
        preg_match("/.*huajiao.com\/l\/(\d+)/is", $this -> live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        $roomid = $matchs[1];
        $html = HTTP::request('http://h.huajiao.com/l/index?liveid='. $roomid);
//        $html = $apiResult['content'];
        preg_match('@"feed":(.*?)"title"@is', $html, $feedInfo);
        if(empty($feedInfo)){
            throw new \Exception( '获取房间信息失败');
        }
        $feedInfo = rtrim($feedInfo[1], ',');
        $feedInfo .= '}';
        $feedInfo = json_decode($feedInfo, true);
        $resultArr = array(
            'status'=>$feedInfo['paused']=='N'?1:0,
//            'poster'=>$feedInfo['image'],
            /*'hls_url'=>'http://qh.cdn.huajiao.com/live_huajiao_v2/'. $feedInfo['sn']. '/index.m3u8'*/
            'hls_url'=>!empty($feedInfo['m3u8']) ? $feedInfo['m3u8'] : 'http://qh.cdn.huajiao.com/live_huajiao_v2/'. $feedInfo['sn']. '/index.m3u8'
        );
        $this -> hls_url = $resultArr['hls_url'];
        $this -> status = $feedInfo['paused']=='N'?1:0;
    }
    public function analysis1()
    {
        preg_match("/.*huajiao.com\/[v|l]\/(\d+)/is", $this -> live_url, $matchs);
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

        preg_match('!<script type="text/javascript">[\s\S]*?window._DATA = ([\s\S]*?)</script>!', $apiResult, $feedInfo);
//        preg_match('@"feed":(.*?)"title"@is', $apiResult, $feedInfo);
        if(empty($feedInfo)){
            throw new \Exception('获取房间信息失败');
        }
//        $feedInfo[1];
        var_dump(self::filterNickname($feedInfo[1]));die();
        $boom_info = json_decode(self::filterNickname($feedInfo[1]),1);
        var_dump($boom_info);die();
        $feedInfo = rtrim($feedInfo[1], ',');
        $feedInfo .= '}';
        $feedInfo = json_decode($feedInfo, true);
        $this -> status = $feedInfo['paused']=='N'?1:0;
        $this -> hls_url = !empty($feedInfo['m3u8']) ? $feedInfo['m3u8'] : 'http://qh.cdn.huajiao.com/live_huajiao_v2/'. $feedInfo['sn']. '/index.m3u8';
    }
    /**
     * 过滤微信昵称中的表情（不过滤 HTML 符号）
     */
    public static function filterNickname($nickname)
    {
        $pattern = array(
            '/\xEE[\x80-\xBF][\x80-\xBF]/',
            '/\xEF[\x81-\x83][\x80-\xBF]/',
            '/[\x{1F600}-\x{1F64F}]/u',
            '/[\x{1F300}-\x{1F5FF}]/u',
            '/[\x{1F680}-\x{1F6FF}]/u',
            '/[\x{2600}-\x{26FF}]/u',
            '/[\x{2700}-\x{27BF}]/u',
            '/[\x{20E3}]/u'
        );
        $nickname = preg_replace($pattern, '', $nickname);
        return trim($nickname);
    }
}