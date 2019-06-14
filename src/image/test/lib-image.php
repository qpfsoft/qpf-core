<?php
use qpf\image\lib\Image;

include __DIR__ . '/../../unit.php';

$imagePath = __DIR__ . '/image';

$filename = $imagePath . '/b1.png';

$image = new Image($filename);

//echor($image);

// 锁定比例, 尺寸修改
//$image->setSize(false, 100, 100);

// 生成略缩图, 并保存
$image->crop($imagePath . '/tmp/a1.png', 50, 50);