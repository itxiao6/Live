### 直播抓取
#### 支持的直播平台:YY直播、斗鱼直播、花椒直播、熊猫直播、映客直播、一直播

### 使用步骤
##### 1. 使用composer 安装
```
composer require itxiao6/live
```

##### 2. 创建test.php
```php
/**
 * 引入Composer 自动加载规则
 */
include_once('vendor/autoload.php');
/**
 * 当前支持的直播平台
 * 1.熊猫直播
 * 2.斗鱼
 * 3.花椒直播
 * 4.一直播
 * 5.映客直播
 */
/**
 * 自动分析平台 及 抓取直播流信息
 */
$res = \Itxiao6\Live\Live::getInterface() -> auto($_GET['url']);
/**
 * 输出测试页面 播放直播
 */
echo '<html>    
    <head>    
        <title>视频直播</title>    
        <meta charset="utf-8">    
        <link href="https://cdn.bootcss.com/video.js/5.5.3/video-js.css" rel="stylesheet">
        <!-- If you\'d like to support IE8 -->    
        <script src="https://cdn.bootcss.com/video.js/5.5.3/ie8/videojs-ie8.min.js"></script>   
    </head>    
<body>
  <h1>直播间</h1>    
    <video id="my-video" class="video-js" controls preload="auto" width="640" height="300" data-setup="{}"> '.$res -> get_source().' </video>    
 <script src="https://cdn.bootcss.com/video.js/5.5.3/video.js"></script>
</body>    
</html>';
```
##### 3.访问测试页面(播放器使用的 Video.js) [文档参考链接](https://github.com/videojs/video.js)
###### 访问:http://examples.com/test.php?url=正在直播的页面地址