<?php

use qpf\image\lib\Image;

// demo - 给图片打水印
include __DIR__ . '/TestBase.php';

$imagePath = __DIR__ . '/image';

// 基础图片
$filename = $imagePath . '/demo-shueiyin.jpg';

// 打开基础图片
$image = new Image($filename);

// 水印图片
$waterImage = $imagePath . '/shuiyin.png';

// 合成水印, 并保存
$image->watermark($imagePath . '/demo-shueiyin-result' . $image->info['ext'], $waterImage, 0);