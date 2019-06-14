<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

/* <IMG SRC =“image.php”>
 * 动态生成图像对于缓存有点问题, 除非激活缓存，否则IE在尝试保存时似乎会对图像类型感到困惑
 * 创建的.GIF会导致浏览器建议使用.BMP保存图像，而不是.GIF
 * 
 * 解决方案是使用session_cache_limiter（'public'）激活缓存; 
 * 
 */

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->createTrueColor(200, 200);

// 设置背景色
$image->setBgColor([0, 0, 0], 100);


//$image->makeGif();

$image->makeGifAlpha();