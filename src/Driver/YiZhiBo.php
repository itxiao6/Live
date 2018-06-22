<?php
namespace Itxiao6\Live\Driver;
use Itxiao6\Live\Bridge\HTTP;
use Itxiao6\Live\Driver;

/**
 * 一直播
 * Class YiZhiBo
 * @package Itxiao6\Live\Driver
 */
class YiZhiBo extends Driver
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
     * 解析直播 信息
     * @throws \Throwable
     */
    public function analysis()
    {
        preg_match("/\/l\/(.*?).html/is", $this -> live_url, $matchs);
        if(empty($matchs)){
            throw new \Exception('视频地址参数错误或所选来源错误');
        }
        $roomid = $matchs[1];
        $html = HTTP::request('http://www.yizhibo.com/l/'. $roomid. '.html');
        preg_match('@play_url:"(.*?)",@is', $html, $hls_url);
        preg_match('@covers:"(.*?)",@is', $html, $poster);
        preg_match('@status:(.*?),@is', $html, $status);
        $this -> status = $status[1]==10?1:0;
        $this -> poster = $poster[1];
        $this -> hls_url = $hls_url[1];
    }

}