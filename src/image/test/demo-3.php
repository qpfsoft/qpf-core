<?php
use qpf\image\font\Font;
use qpf\image\lib\ImageBase;

include __DIR__ . '/TestBase.php';

// demo: 计算文本盒子边框. 计算文本垂直水平居中

/**
 * 计算文本边框
 * @param int $font_size 字体大小
 * @param int $font_angle 字体角度
 * @param string $font_file 字体文件
 * @param string $text 文本
 * @return array 返回关联数组,
 * left(x), top(y) : 传递给imagettftext的坐标, 左上角
 * width, height : 必须创建的图像尺寸, 文本占用的宽高
 */
function calculateTextBox($font_size, $font_angle, $font_file, $text)
{
    $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
    if (! $box) {
        return false;
    }
        
    $min_x = min([$box[0], $box[2], $box[4], $box[6]]);
    $max_x = max([$box[0], $box[2], $box[4], $box[6]]);
    $min_y = min([$box[1], $box[3], $box[5], $box[7]]);
    $max_y = max([$box[1], $box[3], $box[5], $box[7]]);
    $width = ($max_x - $min_x);
    $height = ($max_y - $min_y);
    $left   = abs( $min_x ) + $width;
    $top    = abs( $min_y ) + $height;
    // 为了计算精确的边界框，我将文本写在一个大图像中
    $img     = @imagecreatetruecolor( $width << 2, $height << 2 );
    $white   =  imagecolorallocate( $img, 255, 255, 255 );
    $black   =  imagecolorallocate( $img, 0, 0, 0 );
    imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
    // 确保文本完全在图像中
    imagettftext( $img, $font_size,
        $font_angle, $left, $top,
        $white, $font_file, $text);
    // 开始扫描 (0=> 黑色 => 空的)
    $rleft  = $w4 = $width<<2;
    $rright = 0;
    $rbottom   = 0;
    $rtop = $h4 = $height<<2;
    for ($x = 0; $x < $w4; $x ++) {
        for ($y = 0; $y < $h4; $y ++) {
            if (imagecolorat($img, $x, $y)) {
                $rleft = min($rleft, $x);
                $rright = max($rright, $x);
                $rtop = min($rtop, $y);
                $rbottom = max($rbottom, $y);
            }
        }
    }
        
    // 销毁资源
    imagedestroy($img);
    return [
        'left'   => $left - $rleft,
        'top'    => $top  - $rtop,
        'width'  => $rright - $rleft + 1,
        'height' => $rbottom - $rtop + 1,
    ];
}

//  函数计算
//$opt = calculateTextBox('16', 0, Font::get('zh'), '测试文本');
//echor(imagettfbbox('16', 0, Font::get('zh'), '测试文本'));
//echor($opt);


// 创建图片
$img = new ImageBase(100, 100);
$img->setBgColor('#B2DEFF');


// 内置计算文本范围信息
$box = $img->getTtfBoxInfo(16, 0, Font::get('zh'), '测试文本');

// 居中写入文本
$img->tATtf('测试文本', 16, $box['cx'], $box['cy'], '#000000', 0, Font::get('zh'));
$img->makePng();

