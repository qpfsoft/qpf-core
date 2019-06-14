<?php
use qpf\image\EasyChart;
use qpf\image\font\Font;

include __DIR__ . '/TestBase.php';

// 创建易图
$gd = new EasyChart();

// 创建画布, 注意输出前必须做点什么 不能空生成
$image = $gd->create(400, count(Font::getMaps()) * 35);

// 设置背景色
$image->setBgColor('#C1FFA5');

$col = $image->buildColor('#000');

function getH($s = 30)
{
    static $h = 0;
    return $h += $s;
}

// 默认首选字体
//$image->tATtf('中文字体 - zh', 12, 20, getH(), $col, 0, $image->getZhTTF());
//$image->tATtf('QPFabc123 - arial', 12, 20, getH(), $col, 0, $image->getUsTTF());

// 展示所有字体
foreach (Font::getMaps() as $name => $font_path) {
    $image->tATtf('中文 - abc123 - ' . $name, 16, 20, getH(), $col, 0, $font_path);
}




// 输出到游览器为PNG
$image->makePng();