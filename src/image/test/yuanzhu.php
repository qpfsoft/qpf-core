<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->create(200, 200);

// 设置背景色
$image->setBgColor([255, 255, 255]);

$image->tYuanZhu(20, 50, 50, 100, '#4284F3');

$image->tYuanZhu(100, 50, 50, 100, '#4284F3', true);


$image->makePng();