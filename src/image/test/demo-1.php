<?php
/**
 * 此代码生成RGB立方体（带或不带边框）。 
 * 因为它只能使可见像素清晰快速地显示（大约1到2秒）。 
 * 通过更改$order-variable，您可以从不同的角度看到多维数据集。 
 * 输入double或tribble值（如rrg或ggg）将为您提供其他单通道规格。
 */


$borders = true;
$order   = 'rgb';

set_time_limit(0);
$img = imageCreateTrueColor(510, 510);

$bg = imageColorAllocate($img, 255, 255, 255);
$black = imageColorAllocate($img, 255, 255, 255);

for ($r=0; $r<256; $r++) {
    for ($g=0; $g<256; $g++) {
        for ($b=0; $b<256; $b++) {
            $rN = ${$order{0}};
            $gN = ${$order{1}};
            $bN = ${$order{2}};
            
            $col = imageColorAllocate($img, $rN, $gN, $bN);
            imagesetpixel($img, $b+($r*0.5)+(255/4), $g+($r*0.5)+(255/4), $col);
            if ($r < 255 && $g > 0) break;
        }
    }
    
    if ($borders) {
        imagesetpixel($img, ($r*0.5+(255/4)), ($r*0.5)+(255/4),     $black);
        imagesetpixel($img, ($r*0.5)+255+(255/4), ($r*0.5)+(255/4), $black);
        imagesetpixel($img, ($r*0.5)+(255/4), ($r*0.5)+255+(255/4), $black);
    }
}

if ($borders) {
    imageline($img, 255/4, 255/4, 255+(255/4), 255/4, $black);
    imageline($img, 255/4, 255/4, 255/4, 255+(255/4), $black);
    imageline($img, 255*0.5+(255/4), 255*0.5+(255/4), 255*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), $black);
    imageline($img, 255*0.5+(255/4), 255*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), 255*0.5+(255/4), $black);
    imageline($img, 255*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), $black);
    imageline($img, 255*0.5 + 509*0.5+(255/4), 255*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), 255*0.5 + 509*0.5+(255/4), $black);
}

header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);