<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->create(200, 200);

// 设置背景色
$image->setBgColor([255, 255, 255]);

$group = [];

// 随机生成圆柱, 圆心x,y
for ($i = 0; $i <= 5; $i++) {
    $group[] = mt_rand(10, 50); //高度
}

// 刷新查看, 响音乐播放器的声波效果
$image->tYuanZhuGroup($group, 10,  100, 10, 10, '#4284F3');


$image->makePng();