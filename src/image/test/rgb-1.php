<?php

use qpf\image\lib\RGB;
use qpf\image\lib\HSL;
use qpf\image\lib\HSV;

include __DIR__ . '/TestBase.php';

/**
 * 返回指定颜色较亮的相近似
 * @param array $rgb 包含RGB三个参数的数组
 * @param float $add 饱和度, 微调范围`0.000001~1`
 * @param float $lightness 明度, 微调范围`0.000001~1`
 * @return array 返回较亮的相似色
 */
function rgb_to_light($rgb, $saturation = 0.1, $lightness = 0.1)
{
    $col_old = new \qpf\image\lib\RGB($rgb[0], $rgb[1], $rgb[2]);
    $opt = $col_old->toHSL()->toArray();
    $col_new = new \qpf\image\lib\HSL($opt['hue'], min(1, $opt['saturation'] + $saturation), min(1, $opt['lightness'] + $lightness));
    return $col_new->toRGB()->toHex();
}

/**
 * 返回指定颜色较暗的相近似
 * @param array $rgb 包含RGB三个参数的数组
 * @param float $add 饱和度, 微调范围`0.000001~1`
 * @param float $lightness 明度, 微调范围`0.000001~1`
 * @return array 返回较暗的相似色
 */
function rgb_to_dark($rgb, $saturation = 0.1, $lightness = 0.1)
{
    $col_old = new \qpf\image\lib\RGB($rgb[0], $rgb[1], $rgb[2]);
    $opt = $col_old->toHSL()->toArray();
    $col_new = new \qpf\image\lib\HSL($opt['hue'], max(0, $opt['saturation'] - $saturation), max(0, $opt['lightness'] - $lightness));
    return $col_new->toRGB()->toHex();
}

/**
 * 显示颜色
 * @param string $color 例如`#as5dfs`
 */
function show_color($color)
{
    echo '<div style="height:50px;  background-color:' . $color . ';"></div>';
}

// 亮2
$col = rgb_to_light([0,120,215], 0, 0.48);
echor($col . ' - 5');
show_color($col);

// 亮2
$col = rgb_to_light([0,120,215], 0, 0.38);
echor($col . ' - 4');
show_color($col);

// 亮2
$col = rgb_to_light([0,120,215], 0, 0.28);
echor($col . ' - 3');
show_color($col);

// 亮2
$col = rgb_to_light([0,120,215], 0, 0.2);
echor($col . ' - 2');
show_color($col);

// 亮1
$col = rgb_to_light([0,120,215], 0, 0.1);
echor($col . ' - 1');
show_color($col);

echor(RGB::rgbToHex(implode(',', [0,120,215])) . ' - 正常');
// 正常色
show_color(RGB::rgbToHex(implode(',', [0,120,215])));


// 暗1
$col = rgb_to_dark([0,120,215], 0, 0.1);
echor($col . ' - 1');
show_color($col);

// 暗2
$col = rgb_to_dark([0,120,215], 0.1, 0.2);
echor($col . ' - 2');
show_color($col);

// 暗3
$col = rgb_to_dark([0,120,215], 0.2, 0.2);
echor($col . ' - 3');
show_color($col);


// 暗4
$col = rgb_to_dark([0,120,215], 0.2, 0.3);
echor($col . ' - 4');
show_color($col);

// 暗5
$col = rgb_to_dark([0,120,215], 0.3, 0.4);
echor($col . ' - 5');
show_color($col);


/* 
// 创建RGB对象
$col_1 = new RGB(28,183,70);

// 转换为返回此色彩的16色 `#000000`格式
echor($col_1->toHex());
show_color($col_1->toHex());

// 转换为toHSL格式
$col_2 = $col_1->toHSL()->toArray();
echor($col_2);

// 将RGB的HSL数据生成对象, 通过微调参数. 再转换为RGB值
$col_3 = new HSL($col_2['hue'], $col_2['saturation'], $col_2['lightness']);
echor($col_3->toRGB()->toHex());
show_color($col_3->toRGB()->toHex());

// 转换为toHSV格式
$col_4 = $col_1->toHSV()->toArray();
echor($col_4);

// 最大值是1, 通过选最小值, 值始终不超过1
$col_5 = new HSV($col_4['hue'], min(1, $col_4['saturation']+0.2),  min(1, $col_4['value']+0.2));
echor($col_5->toRGB()->toHex());
show_color($col_5->toRGB()->toHex());

echor($col_5->toArray()); */


//echor($col_1->toHSV());