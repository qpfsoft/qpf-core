<?php
namespace qpf\image\lib;

/**
 * 真彩色图像
 * 
 * 需要GD版本2.0以上
 */
class ImageTrueColor extends ImageBase
{
    /**
     * 构造函数 - 真彩色图像, 没有调色板256限制
     * @param int $width 图像宽度
     * @param int $height 图像高度
     */
    public function __construct($width, $height)
    {
        $this->image = @imagecreatetruecolor($width, $height);
        
        if (!$this->image) {
            throw new \Exception('真彩图像创建失败!');
        }
    }
    
    /**
     * 设置背景色为透明
     * @return bool
     */
    public function setBgColorAlpha()
    {
        $color = $this->buildColor([0, 0, 0], 127);
        return $this->fill(0, 0, $color);
    }
    
    /**
     * 设置背景的颜色
     * @param string|array $color 颜色
     * @param int $alpha 透明度, 范围0~127, 0代表正常, 127代表最大透明度
     * @return void
     */
    public function setBgColor($color, $alpha = 0)
    {
        $color = $this->parseColor($color);
        
        list($red, $green, $blue) = $color;

        $this->fill(0, 0, $this->buildColor($color, $alpha));
    }
}