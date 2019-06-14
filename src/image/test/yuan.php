<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->create(200, 200);

// 设置背景色
$image->setBgColor([5,7,18]);

// 生成颜色
$baise = $image->buildColor('#FFFFFF');

// 在 100,100 圆心坐标 画 一个80x80宽高的圆
$image->tYuan(100, 100, 80, 80, $baise);

// 输出到游览器为PNG
$image->makePng();
