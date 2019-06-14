<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->create(200, 200);

// 白色背景
$image->buildColor('#FFF');

// 画点, 一般需要画很多点. 才明显
$image->tDian(1, 1, '#000');

$color = $image->buildColor($image->getRandColor());

for($i = 0; $i < 10000; $i++) {
    $x = mt_rand(2, 200);
    $y = mt_rand(2, 200);
    $image->tDian($x, $y, $color);
}

$image->makeJpg();