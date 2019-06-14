<?php
namespace qpf\captcha;

use qpf;
use qpf\base\Core;
use qpf\exception\NotFoundException;
use qpf\func\StrFunc;
use qpf\func\ArrFunc;
use qpf\image\font\Font;
use qpf\image\lib\ImageTrueColor;
use qpf\exception\ConfigException;

/**
 * 验证码类
 */
class Captcha extends Core
{
    /**
     * 验证码变量名
     * - 作用于input名与session名
     * @var string
     */
    public $name;
    /**
     * 图片宽度
     * @var int
     */
    public $width;
    /**
     * 图片高度
     * @var int
     */
    public $height;
    /**
     * 验证码长度
     * @var int
     */
    public $length;
    /**
     * 验证码样式
     * ```
     * [
     * 'color'             => '', // 字色
     * 'font-size'         => '14', // 字号
     * 'font-family'       => '', // 字体
     * 'background-color'  => '#ffffff', // 背景颜色
     * 'text-decoration'   => '', // 文本装饰
     * ]
     * ```
     * @var array
     */
    public $style = [];
    /**
     * 真彩图像
     * @var ImageTrueColor
     */
    public $image;
    
    
    /**
     * 初始化
     */
    public function init()
    {
        if (!extension_loaded('gd') || !function_exists("imagepng")) {
            throw new NotFoundException('gd or imagepng()', 'extension');
        }
        $config = QPF::$app->config->group('captcha');
        $this->useConfig($config);
        // 创建画布
        $this->image = new ImageTrueColor($this->width, $this->height);
    }
    
    

    /**
     * 检查验证码是否正确
     * 
     * @param string $input 验证码在post中的参数名
     * @param string $session 验证码在session中的参数名
     * @return bool
     */
    public function check($input = null, $session = null)
    {
        $input = empty($input) ? $this->name : $input;
        return strtoupper(QPF::$app->request->post($input)) == $this->getCode($session);
    }
    
    /**
     * 获取验证码值
     * @param string $session 验证码在session中的参数名
     * @return string
     */
    public function getCode($session = null)
    {
        $session = empty($session) ? $this->name : $session;
        return QPF::$app->session->get($session);
    }
    
    /**
     * 创建验证码
     */
    public function create()
    {
        // 生成验证码
        $code = $this->buildCode();
        // 保存验证码 - 自动转换为小写
        QPF::$app->session->set($this->name, strtolower($code));
        
        // 设置背景颜色
        $this->image->setBgColor($this->getStyleBackgroundColor());
        
        
        // 文本装饰
        $fontColor = $this->getStyleColor();
        
        
        if ($this->getStyleTextDecoration()) {
            // 混淆网格
            $this->randLine($fontColor, false, true);
            
            // 扭曲变形
            $this->randDeform(2);
            // 混淆网格
            $this->randLine($fontColor, true, false);
            // 噪点
            $this->randPix($fontColor, false);
            
        }
        
        // 写入验证码
        $this->createFont($code);
        // 扭曲变形
        $this->randDeform(1);
        
        
        if (PHP_SAPI != 'cli') {
            $this->image->makePng(9);
            exit();
        }
        
        return true;
    }
    
    /**
     * 生成验证码值
     * @return string 返回的字母存在大小写
     */
    protected function buildCode()
    {
        // 随机数据
        $data = 'asfghkwetyupzxcvbnmZXCVBNMWERTYUPASFGHKL';
        
        return StrFunc::randString($data, $this->length, false);
    }
    

    /**
     * 返回背景颜色
     * @return mixed
     */
    protected function getStyleBackgroundColor()
    {
        if (!key_exists('background-color', $this->style)) {
            throw new ConfigException('style 属性缺少 `background-color` 背景颜色设置!');
        }
        
        if (is_bool($this->style['background-color'])) {
            $color = $this->image->getRandColor();
        } elseif(is_array($this->style['background-color'])) {
            $color = ArrFunc::randArrayValue($this->style['background-color']);
        } else {
            $color = $this->style['background-color'];
        }
        
        return $color;
    }
    
    /**
     * 返回文本颜色
     * @return string 十六进制颜色值
     */
    protected function getStyleColor()
    {
        if (!key_exists('color', $this->style)) {
            throw new ConfigException('style 属性缺少 `color` 字体颜色设置!');
        }
        
        if (is_bool($this->style['color'])) {
            $color = $this->image->getRandColor();
        } elseif(is_array($this->style['color'])) {
            $color = ArrFunc::randArrayValue($this->style['color']);
        } else {
            $color = $this->style['color'];
        }
        
        return $color;
    }
    
    /**
     * 返回字体大小
     * @return int
     */
    protected function getStyleFontSize()
    {
        if (!key_exists('font-size', $this->style)) {
            throw new ConfigException('style 属性缺少 `font-size` 字体大小设置!');
        }
        
        return (int) $this->style['font-size'];
    }
    
    /**
     * 返回字体文件路径
     * @return string
     */
    protected function getStyleFontFamily()
    {
        if (!key_exists('font-family', $this->style)) {
            throw new ConfigException('style 属性缺少 `font-family` 字体类型设置!');
        }
        
        if (is_array($this->style['font-family'])) {
            $type = ArrFunc::randArrayValue($this->style['font-family']);
        } else {
            $type = $this->style['font-family'];
        }
        
        return Font::get($type);
    }
    
    /**
     * 返回文本装饰
     */
    protected function getStyleTextDecoration()
    {
        if (!key_exists('text-decoration', $this->style)) {
            throw new ConfigException('style 属性缺少 `text-decoration` 文本装饰设置!');
        }
        
        return $this->style['text-decoration'];
    }

    
    /**
     * 随机角度
     * @return int
     */
    protected function randAngle()
    {
        $seed = [-1, 0, 1]; // 方向
        $angle = [9, 20]; // 角度
        
        return ArrFunc::randArrayValue($seed) * ArrFunc::randArrayValue($angle);
    }
    
    /**
     * 随机干扰线条
     * @param resource $image 画布资源
     * @param bool $xLine 水平横线
     * @param bool $yLine 垂直横线
     */
    protected function randLine($color, $xLine = true, $yLine = true)
    {
        /* @var $image \qpf\image\lib\ImageTrueColor  */

        // 水平线
        if ($xLine) {
            $l = $this->height / 5;
            
            for ($i = 1; $i < $l; $i++) {
                $step = $i * 5; // 间隔
                $this->image->tXian(0, $step, $this->width, $step, $color);
            }
        }

        // 垂直线
        if ($yLine) {
            $l = $this->width / 10;
            
            for ($i = 1; $i < $l; $i++) {
                $step = $i * 10; // 间隔
                $this->image->tXian($step, 0, $step, $this->height, $color);
            }
        }
        
    }
    
    /**
     * 扭曲变形
     * @param int $type 强度类型 1, 2
     */
    protected function randDeform($type = 1)
    {
        $temp = new ImageTrueColor($this->width, $this->height);
        $temp->setBgColor($this->getStyleBackgroundColor());
        
        if ($type === 1) {
            for ($x = 0; $x < $this->width; $x++) {
                for ($y = 0; $y < $this->height; $y++) {
                    $rgb = $this->image->getColor($x, $y);
                    $temp->tDian((int) ($x + sin($y / $this->height * 2 * M_PI - M_PI * 0.5) * 3), $y, $rgb);
                }
            }
        } else {
            $w = $this->width;
            $h = $this->height;
            $_n = ['3','4'];
            $n = $_n[mt_rand(0, count($_n) - 1)];
            for ($i = 1; $i < $w; $i++) {
                for ($j = 1; $j < $h; $j++) {
                    $rgb = $this->image->getColor($i, $j);
                    if((int)($i+20+sin($j/$h*2*M_PI)*10) <= $w && (int)($i+20+sin($j/$h*2*M_PI)*10) >= 0) {
                        imagesetpixel ($temp->image, (int)($i+10+sin($j/$h*$n*M_PI-M_PI*0.5)*3) , $j , $rgb);
                    }
                }
            }
        }
        
        
        $this->image = $temp;
    }
    
    /**
     * 写入验证码文本
     * @param resource $image 画布资源
     * @param string $code 验证码
     * @param mixed $color 颜色
     * @param string $fontfile 字体文件
     */
    protected function createFont($code)
    {
        /* @var $image \qpf\image\lib\ImageTrueColor  */
        
        // 水平位置均分
        $x = ($this->width - 10) / $this->length;
        
        for ($i = 0; $i < $this->length; $i++) {
            $this->image->tATtf($code[$i],
                $this->getStyleFontSize(), 
                $x * $i + mt_rand(6, 10), // x
                mt_rand($this->height / 1.3, $this->height - 5), // y
                $this->getStyleColor(), 
                mt_rand(-30, 30), // 角度
                $this->getStyleFontFamily());
        }
    }
    
    /**
     * 画点
     */
    protected function randPix($color, $level = false)
    {
        // 干扰点
        for ($i = 0; $i < 50; $i++) {
            $this->image->tDian(mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }

        // 干扰笔画
        if ($level) {
            $points = [];
            for ($i = 0; $i < 5; $i++) {
                $points[] = mt_rand(0, $this->width);
                $points[] = mt_rand(0, $this->height);
            }
            $this->image->tXians($points, 5, $color);
        }
        
    }



    
}