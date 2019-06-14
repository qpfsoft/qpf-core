<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布
$image = $gd->create(200, 200);

// 设置背景颜色
$image->setBgColor('#4284F3');

// 不支持中文
$image->tA('hello!', 5, 80, 100, '#FFF');

// 以make开头的方法, 用于显示图片到游览器
$image->makeJpg();
