<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;

/**
 * 一直播
 * Class YiZhiBo
 * @package Itxiao6\Live\Driver
 */
class YiZhiBo
{

    public static function getInterface($live_url)
    {
        preg_match("/\/l\/(.*?).html/is", $live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        $roomid = $matchs[1];
        $html = HTTP::request('http://www.yizhibo.com/l/'. $roomid. '.html');
        preg_match('@play_url:"(.*?)",@is', $html, $hls_url);
        preg_match('@covers:"(.*?)",@is', $html, $poster);
        preg_match('@status:(.*?),@is', $html, $status);
        $resultArr = array('status'=>$status[1]==10?1:0, 'poster'=>$poster[1], 'hls_url'=>$hls_url[1]);
        return $resultArr;
    }
}