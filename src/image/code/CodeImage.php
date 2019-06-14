<?php
namespace qpf\image\code;

use qpf\image\lib\ImageTrueColor;
use qpf\func\ArrFunc;
use qpf\func\StrFunc;

/**
 * 图片验证码
 */
class CodeImage
{
    private $img;
    private $width;
    private $height;
    /**
     * 随机颜色索引列表
     * @var array
     */
    private $randColor;
    /**
     * 背景色
     * @var mixed
     */
    private $bgColor;
    
    /**
     * 构造函数
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->img = new ImageTrueColor($width, $height);
    }
    
    /**
     * 设置背景颜色
     * @param mixed $color
     */
    public function setBgColor($color)
    {
        $this->bgColor = $color;
        $this->img->setBgColor($color);
    }
    
    /**
     * 获得画布操作对象
     * @return \qpf\image\lib\ImageTrueColor
     */
    public function getImage()
    {
        return $this->img;
    }
 
    /**
     * 返回当前随机颜色索引列表
     * @return array
     */
    public function getRandColor()
    {
        if ($this->randColor === null) {
            $this->randColor = [
                $this->img->getRandColor(),
                $this->img->getRandColor(),
                $this->img->getRandColor(),
            ];
        }
        
        return $this->randColor;
    }
    
    /**
     * 设置干扰点
     * @param int $num 数量
     */
    public function setNoisePoint($num)
    {
        $colorList = $this->getRandColor();
        for ($i = 0; $i < $num; $i++) {
            $this->img->tDian(mt_rand(0, $this->width), mt_rand(0, $this->height), ArrFunc::randArrayValue($colorList));  
        }
    }
    
    /**
     * 设置干扰线
     * @param int $num 数量
     */
    public function setNoiseLine($num)
    {
        $colorList = $this->getRandColor();
        for ($i = 0; $i < $num; $i++) {
            $this->img->tXian(0, mt_rand(0, $this->width), $this->width, mt_rand(0, $this->height), ArrFunc::randArrayValue($colorList));
        }
    }
    
    /**
     * 扭曲
     * 
     * 必须预先设置背景颜色
     */
    public function setDeform()
    {
        $txt = new ImageTrueColor($this->width, $this->height);
        $txt->setBgColor($this->bgColor);
  
        
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $rgb = $this->img->getColor($x, $y);
                $txt->tDian((int) ($x + sin($y / $this->height * 2 * M_PI - M_PI * 0.5) * 3), $y, $rgb);
            }
        }
        
        $this->img = $txt;
    }
    
    /**
     * 设置文本
     * @param string $string
     */
    public function setText($string)
    {
        $textArray = StrFunc::split($string, 1, false);
        $count = count($textArray);
        $fontSize = 20;
        $colorList = $this->getRandColor();
        for ($i = 0; $i < $count; $i++) {
            $color = ArrFunc::randArrayValue($colorList);
            $angle = mt_rand(-1, 1) * mt_rand(1, 20);
            $this->img->tATtf($textArray[$i], $fontSize, 5 + $i * floor($fontSize * 1.3), floor($this->height * 0.75), $color, $angle, $this->img->getUsTTF());
        }
    }
    
    /**
     * 生成图片
     */
    public function build()
    {
        $this->img->makePng();
    }
}