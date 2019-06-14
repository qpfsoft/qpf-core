<?php
/**
 * $_GET["src"] 指定一张jpg图片
 * 
 * 将jpg图片中的白色转换为透明. 输出为png图片
 * 
 * 使白色（或接近白色）背景透明的图像
 */

function FloodFill($im, $x, $y)
{
    $rgb = imagecolorat($im, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    
    $counter=0;
    $counter2=0;
    // 颜色接近255(白色)的部分全部转换为白色
    if($r >= 245){ $counter++;}
    if($g >= 245){ $counter++;}
    if($b >= 245){ $counter++;}
    if($r >= 240){ $counter2++;}
    if($g >= 240){ $counter2++;}
    if($b >= 240){ $counter2++;}
    
    if($counter >= 1 && $counter2 == 3){
        $background = imagecolorallocate($im, 180, 0, 255);
        imagesetpixel($im, $x, $y, $background);
        
        FloodFill ($im, $x, $y+1);
        FloodFill ($im, $x+1, $y);
        FloodFill ($im, $x, $y-1);
    }
}

$src = $_GET["src"];
$im = imagecreatefromjpeg($src);

// 画边框
//$border = imagecolorallocate($im, 180, 0, 255);
//drawBorder($im, $border, 1);

// 画一个边框
function drawBorder($im, $color, $thickness = 1)
{
    $x1 = 0;
    $y1 = 0;
    $x2 = ImageSX($im) - 1;
    $y2 = ImageSY($im) - 1;
    
    for($i = 0; $i < $thickness; $i++)
    {
        ImageRectangle($im, $x1++, $y1++, $x2--, $y2--, $color);
    }
}

$rgb = imagecolorat($im, 0, 0);
FloodFill($im, 0, 0);
$color = imagecolorallocate($im, 180, 0, 255);
imagecolortransparent($im, $color);
header("Content-type: image/png");
imagepng($im);
imagedestroy($im);