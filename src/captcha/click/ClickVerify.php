<?php
namespace qpf\captcha\click;

use qpf\func\ArrFunc;
use qpf\func\StrFunc;

/**
 * 点击验证码
 */
class ClickVerify
{

    /**
     * 创建验证码图
     * @param int $length 汉字数量
     */
    public static function create($length = 4)
    {
        // 准备背景图片与字体
        $srcPath = __DIR__ . '/src/';
        $imageBgMap = ['1.jpg', '2.jpg', '3.jpg'];
        $bgImagePath = $srcPath . ArrFunc::randArrayValue($imageBgMap);
        $fontPath = $srcPath . 'font.otf';
        
        $zhCode = StrFunc::strChinese('4');
        
        foreach ($zhCode as $zh) {
            // 随机字体大小
            $fontSize = rand(15, 30);
            
            //字符串文本框宽度和长度
            $fontarea  = imagettfbbox($fontSize, 0, $fontPath, $zh);
            $textWidth = $fontarea[2] - $fontarea[0];
            $textHeight = $fontarea[1] - $fontarea[7];
            echor($fontarea);
        }
    }
}