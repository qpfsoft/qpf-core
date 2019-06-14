<?php
namespace qpf\image\lib;

/**
 * RGB色彩空间描述
 */
class RGB
{

    /**
     * 红色 0-1
     * @var float
     */
    protected $_red;

    /**
     * 绿色 0-1
     * @var float
     */
    protected $_green;

    /**
     * 蓝色 0-1
     * @var float
     */
    protected $_blue;

    /**
     * 创建RGB颜色
     * 
     * - 支持0~255取值, 内部自动转换为0~1取值范围
     * @param float $red 红色0-1, 例`0.9xxxxx` 最大五位小数点
     * @param float $green 绿色0-1
     * @param float $blue 蓝色0-1
     */
    public function __construct($red = 0, $green = 0, $blue = 0)
    {
        if ($red > 1 || $green > 1 || $blue > 1) {
            // 采用0~255取值范围, 转换为 0~1范围
            $this->_red = round($red/255, 6);
            $this->_green = round($green/255, 6);
            $this->_blue = round($blue/255, 6);
        } else {
            // 采用 0~1取值范围
            $this->_red = $red;
            $this->_green = $green;
            $this->_blue = $blue;
        }
    }

    /**
     * 获取红色分量
     * @return float
     */
    public function getRed()
    {
        return $this->_red;
    }

    /**
     * 获取绿色分量
     * @return float
     */
    public function getGreen()
    {
        return $this->_green;
    }

    /**
     * 获取蓝色分量
     * @return float
     */
    public function getBlue()
    {
        return $this->_blue;
    }

    /**
     * 返回该色彩的HSL空间描述
     * @return HSL
     */
    public function toHSL()
    {
        $r = $this->getRed();
        $g = $this->getGreen();
        $b = $this->getBlue();
        $rgb = array(
            $r,$g,$b
        );
        $max = max($rgb);
        $min = min($rgb);
        $diff = $max - $min;
        if ($max == $min) {
            $h = 0;
        } else if ($max == $r && $g >= $b) {
            $h = 60 * (($g - $b) / $diff);
        } else if ($max == $r && $g < $b) {
            $h = 60 * (($g - $b) / $diff) + 360;
        } else if ($max == $g) {
            $h = 60 * (($b - $r) / $diff) + 120;
        } else if ($max == $b) {
            $h = 60 * (($r - $g) / $diff) + 240;
        } else {
            throw new \ErrorException("RGB conversion HSL failure!");
        }
        $l = ($max + $min) / 2;
        if ($l == 0 || $max == $min) {
            $s = 0;
        } else if (0 < $l && $l <= 0.5) {
            $s = $diff / (2 * $l);
        } else if ($l > 0.5) {
            $s = $diff / (2 - 2 * $l);
        } else {
            throw new \ErrorException("RGB conversion HSL failure!");
        }

        return new HSL($h, $s, $l);
    }

    /**
     * 返回此色彩的HSV空间描述
     * @return HSV
     */
    public function toHSV()
    {
        $red = $this->getRed();
        $green = $this->getGreen();
        $blue = $this->getBlue();
        
        $rgb = array(
            $red,$green,$blue
        );
        $max = max($rgb);
        $min = min($rgb);
        $diff = $max - $min;
        
        /* 计算色相 */
        if ($max == $min) {
            $hue = 0;
        } else if ($max == $red && $green >= $blue) {
            $hue = 60 * (($green - $blue) / $diff);
        } else if ($max == $red && $green < $blue) {
            $hue = 60 * (($green - $blue) / $diff) + 360;
        } else if ($max == $green) {
            $hue = 60 * (($blue - $red) / $diff) + 120;
        } else if ($max == $blue) {
            $hue = 60 * (($red - $green) / $diff) + 240;
        } else {
            throw new \ErrorException("compute hue failure!");
        }
        
        /* 计算饱和度 */
        if ($max == 0) {
            $saturation = 0;
        } else {
            $saturation = 1 - $min / $max;
        }
        
        /* 计算色调 */
        $value = $max;

        return new HSV($hue, $saturation, $value);
    }
    
    /**
     * 将取值范围`0~255`转换为`0~1`
     * @param string $color 颜色
     * @return array
     */
    public static function rgbColor($color)
    {
        if (strncmp('#', $color, 1) !== false) {
            $color = self::hexToRgb($color);
        } elseif (!is_array($color)) {
            $color = explode(',', strtr($color, ' ', ''));
        }
        return [round($color[0] / 255, 6), round($color[1] / 255, 6), round($color[2] / 255, 6)];
    }
    
    /**
     * 十六进制颜色转换RGB
     * @param string $color 格式`#FFFFFF`
     * @param bool $returnArray 是否返回数组格式, 默认`true`
     * @return mixed 返回false代表传入参数错误
     */
    public static function hexToRgb($color, $returnArray = true)
    {
        if (strncmp('#', $color, 1) !== false) {
            
            if (strlen($color) == 7) {
                list ($r, $g, $b) = [$color[0] . $color[1],$color[2] . $color[3],$color[4] . $color[5]];
            } elseif (strlen($color) == 4) {
                list ($r, $g, $b) = [$color[0] . $color[0],$color[1] . $color[1],$color[2] . $color[2]];
            } else {
                return false;
            }
            
            $color = $returnArray ? [hexdec($r), hexdec($g), hexdec($b)] : hexdec($r) .','. hexdec($g) .','. hexdec($b);
        }
        
        return false;
    }
    
    /**
     * RGB转十六进制颜色
     * @param string $hexColor RGB字符串`255,255,255`
     * @return string 返回格式`#FFFFFF`
     */
    public static function rgbToHex($rgb)
    {
        $regexp = "/([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})/";
        $re = preg_match($regexp, $rgb, $match);
        $re = array_shift($match);

        $hexColor = '#';
        $hex = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
        for ($i = 0; $i < 3; $i++) {
            $r = null;
            $c = $match[$i];
            $hexAr = [];
            while ($c >= 16) {
                $r = $c % 16;
                $c = ($c / 16) >> 0;
                array_push($hexAr, $hex[$r]);
            }
            array_push($hexAr, $hex[$c]);
            $ret = array_reverse($hexAr);
            $item = implode('', $ret);
            $item = str_pad($item, 2, '0', STR_PAD_LEFT);
            $hexColor .= $item;
        }
        return $hexColor;
    }
    
    /**
     * 返回此色彩的RGB `0~255`取值
     * @return array [R, G, B]
     */
    public function toRgb()
    {
        return [
            intval($this->getRed() * 255), 
            intval($this->getGreen() * 255),
            intval($this->getBlue() * 255)
        ];
    }
    
    /**
     * 返回此颜色的RGB描述 - `255,255,255`
     * @return string
     */
    public function toRgbString()
    {
        return intval($this->getRed() * 255) . ',' .
             intval($this->getGreen() * 255) . ',' .
             intval($this->getBlue() * 255);
    }
    
    /**
     * 返回此色彩的16色 `#000000`格式
     */
    public function toHex()
    {
        return self::rgbToHex($this->toRgbString());
    }

    /**
     * 返回该色彩的数组表现形式
     */
    public function toArray()
    {
        return ['red' => $this->getRed(),'green' => $this->getGreen(),'blue' => $this->getBlue()];
    }
}