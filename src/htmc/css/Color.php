<?php
namespace qpf\htmc\css;

/**
 * 颜色管理器
 *
 * 作用：
 * - 生成颜色
 * - 生成不同类型的颜色代码
 *
 *
 * # RGB转换为十六进制的方法, 返回(string)"#FFB400"
 * Color::RGBToHex('rgb(255, 180, 0)')
 * Color::colorToHex(255, 180, 0)
 * # Hex转换为RGB颜色,返回(array)['r','g','b']
 * Color::hex2rgb('#FFB400')
 * Color::colorToRgb('#FFB400')
 *
 * ~~~算法原理
 * RGB(红, 绿, 蓝), 每一个取值范围 0~ 255
 *
 * 将RGB颜色转换成 十六进制her #xxx 格式:
 *
 * # 思路, 分别将 红, 绿, 蓝的, 十进制值 转换成十六进制, 添加前缀#, 后面值连接起来几个.
 * 实例: 红 255
 * 如果十进制值大于16, 就除以16并取整(不四舍五入)
 * $r = 255 % 16 (取余)
 * $c = 255 / 16 (取商)
 * $arr[$c] . $arr[$r]
 * ~~~
 *
 * @author qiun
 */
class Color
{

    /**
     * Hex格式转换为RGB数组
     * 
     * @param string $hex_color
     *            十六进制颜色
     * @return array 数组键名采用r,g,b区分3位
     */
    static function colorToRgb($hex_color)
    {
        if (stripos($hex_color, '#') !== false) {
            $hex_color = substr($hex_color, 1);
        }
        
        if (strlen($hex_color) > 3) {
            $hex_color = str_split($hex_color, 2);
        } else {
            $hex_color = str_split($hex_color, 1);
        }
        $rgbArr = [];
        $rgbArr['r'] = hexdec($hex_color[0]);
        $rgbArr['g'] = hexdec($hex_color[1]);
        $rgbArr['b'] = hexdec($hex_color[2]);
        return $rgbArr;
    }

    /**
     * RGB颜色转换为Hex格式
     * 
     * @param integer $r
     *            红
     * @param integer $g
     *            黄
     * @param integer $b
     *            蓝
     * @return string 返回十六进制颜色#xxxxxx
     */
    static public function colorToHex($r, $g, $b)
    {
        $rgb = [
            $r,
            $g,
            $b
        ];
        $hexStr = '#';
        $hex = [
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F'
        ];
        foreach ($rgb as $ia) {
            $ib = null;
            $hexArr = [];
            while ($ia > 16) {
                $ib = $ia % 16;
                $ia = ($ia / 16) >> 0;
                $hexArr[] = $hex[$ib];
            }
            $hexArr[] = $hex[$ia];
            $hexStr .= str_pad(implode('', array_reverse($hexArr)), 2, 0, STR_PAD_LEFT);
        }
        return $hexStr;
    }

    /**
     * Css颜色RGB字符串转为十六进制
     * 
     * @param $rgb RGB颜色的字符串
     *            如：rgb(255,255,255);
     * @return string 十六进制颜色值 如：#FFFFFF
     */
    static public function RGBToHex($rgb)
    {
        $regexp = '/^rgb\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/';
        $result = preg_match($regexp, $rgb, $match);
        if (! $result)
            return false;
        return self::colorToHex($match[1], $match[2], $match[3]);
    }

    /**
     * Hex格式转换为RGB数组
     * 
     * @param string $hex_color
     *            十六进制颜色
     * @return array 数组键名采用r,g,b区分3位
     */
    static public function hex2rgb($hexColor)
    {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }
}