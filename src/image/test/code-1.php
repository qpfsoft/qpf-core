<?php


use qpf\image\code\CodeImage;
use qpf\func\StrFunc;

include __DIR__ . '/TestBase.php';


$code = new CodeImage(130, 54);

$code->setBgColor('#FFF');

$str = StrFunc::randString(StrFunc::strData(true, false), 4);

// 设置文本
$code->setText($str);
// 扭曲文本
$code->setDeform();
// 干扰线
$code->setNoiseLine(8);
// 干扰点
$code->setNoisePoint(500);
// 生成
$code->build();
