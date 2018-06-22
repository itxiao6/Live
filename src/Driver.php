<?php
namespace Itxiao6\Live;

/**
 * 驱动接口
 * Interface Driver
 * @package Itxiao6\Live
 */
abstract class Driver
{
    /**
     * 直播地址
     * @var string
     */
    protected $live_url = '';
    /**
     * 状态
     * @var int
     */
    protected $status = 0;
    /**
     * 封面
     * @var string
     */
    protected $poster = '';
    /**
     * HLS_URL
     * @var string
     */
    protected $hls_url = '';
    /**
     * FLV_URL
     * @var string
     */
    protected $flv_url = '';
    /**
     * RTMP URL
     * @var string
     */
    protected $rtmp_url = '';

    /**
     * 获取flv 播放地址
     * @return mixed|void
     */
    public function get_flv()
    {
        return $this -> flv_url;
    }

    /**
     * 获取hls 播放地址
     * @return mixed|string
     */
    public function get_hls()
    {
        return $this -> hls_url;
    }

    /**
     * 获取rtmp 播放地址
     * @return mixed|string
     */
    public function get_rtmp()
    {
        return $this -> rtmp_url;
    }


    /**
     * YiZhiBo 构造器
     * @param $live_url
     */
    protected function __construct($live_url)
    {
        /**
         * 设置直播地址
         */
        $this -> live_url = $live_url;
        /**
         * 解析直播信息
         */
        $this -> analysis();
    }

    /**
     * 获取一直播实例
     * @param $live_url
     * @return YiZhiBo|mixed
     */
    public static function getInterface($live_url)
    {
        return new static($live_url);
    }

    /**
     * 解析直播
     * @return mixed
     */
    abstract protected function analysis();

    /**
     * 获取video source
     * @return string
     */
    public abstract function get_source();

}