<?php
use qpf\image\EasyChart;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

$image = $gd->createTrueColor(200, 200);

$image->setBgColor('#EDEDED');

// 生成颜色
$col_hei = $image->buildColor('#000');

// 画横线
//$image->tHr(0, 50, 150, $col_hei);


// 使用间隔管理器, 快捷的生成间隔插值
for ($i = 0; $i <= 5; $i++) {
    $image->tHr(0, $image->getSpace('hr', 5, 0, 'y') + 50, $image->getSpace('hr', 20, 0, 'h') + 50, $col_hei);
}

$image->makePng();