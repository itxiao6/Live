<?php
namespace Itxiao6\Live;

use Itxiao6\Live\Driver\DouYv;
use Itxiao6\Live\Driver\HuaJiao;
use Itxiao6\Live\Driver\XiongMao;
use Itxiao6\Live\Driver\YingKe;
use Itxiao6\Live\Driver\YiZhiBo;

class Live
{

    /**
     * 获取接口
     */
    public static function getInterface()
    {
        return new static();
    }

    /**
     * 自动检测直播类型
     * @param $url
     * @return array
     * @throws \Exception
     */
    function auto($url)
    {
        if(preg_match('!douyu\.com!',$url)){
            return DouYv::getInterface($url);
        }
        else if(preg_match('!panda\.tv!',$url)){
            return XiongMao::getInterface($url);
        }
        else if(preg_match('!huajiao\.com!',$url)){
            return HuaJiao::getInterface($url);
        }
        else if(preg_match('!yizhibo\.com!',$url)){
            return YiZhiBo::getInterface($url);
        }
        else if(preg_match('!inke\.cn!',$url)){
            return YingKe::getInterface($url);
        }
    }
}