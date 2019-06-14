<?php
namespace qpf\image\lib;

use qpf\image\font\Font;

/**
 * 图像操作基础
 */
class ImageBase
{
    /**
     * 当前图片资源
     * @var resource
     */
    public $image;
    
    /**
     * 构造函数 - 基于调色板, 限制(255+1)种颜色
     * @param int $width 图像宽度
     * @param int $height 图像高度
     */
    public function __construct($width, $height)
    {
        $this->image = imagecreate($width, $height);
        
        if (!$this->image) {
            throw new \Exception('创建图像失败!');
        }
    }
    
    /**
     * 检查文件是否是图片
     * @param string $image 被检查的图片文件
     * @return bool
     */
    public function isImage($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $extType = ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'];
        $imgType = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF, IMAGETYPE_BMP, IMAGETYPE_SWC];
        
        return is_file($filename) && in_array($ext, $extType) && in_array($this->getType($filename), $imgType);
    }
    
    /**
     * 获取图片文件的类型
     * @param string $filename 被检查的图片文件
     * @return int|false 返回图片类型的常量值`IMAGETYPE_*`
     */
    public function getType($filename)
    {
        if (function_exists('exif_imagetype')) {
            if (filesize($filename) > 11) {
                return exif_imagetype($filename);
            } else {
                return false;
            }
        } else {
            $exif_imagetype = function ($filename) {
                if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
                    return $type;
                }
                return false;
            };
            
            return $exif_imagetype($filename);
        }
    }
    
    /**
     * 确保正确解析颜色设置
     * @param string|array $color 颜色, 支持格式:
     * - `#FFFFFF` : 十六进制颜色值
     * - `255,255,255` : 逗号分隔的RGB值
     * - [r, g, b] : 数组分隔的RGB值
     * @return array 返回包含RGB值的数组[0 => r, 1 => g, 2 => b]
     */
    public function parseColor($color)
    {
        // 跳过颜色索引
        if (is_int($color)) {
            return $color;
        }
        // RGB数组直接返回
        if (is_array($color)) {
            return $color;
        }
        
        // RGB字符串
        if (strpos($color, ',') !== false) {
            return explode(',', str_replace(' ', '', $color));
        }
        
        // 颜色哈希
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }
        
        if (strlen($color) == 6) {
            list ($r, $g, $b) = [$color[0] . $color[1],$color[2] . $color[3],$color[4] . $color[5]];
        } elseif (strlen($color) == 3) {
            list ($r, $g, $b) = [$color[0] . $color[0],$color[1] . $color[1],$color[2] . $color[2]];
        } else {
            throw new \Exception(__METHOD__ . ' 颜色设置错误' . get_varstr($color));
            return false;
        }
        
        return [hexdec($r), hexdec($g), hexdec($b)];
    }
    
    /**
     * 设置基于调色板的图像的背景颜色
     * @param string|array $color 颜色
     * @return void
     */
    public function setBgColor($color)
    {
        $color = $this->parseColor($color);

        list($red, $green, $blue) = $color;
        
        imagecolorallocate($this->image, $red, $green, $blue);
    }
    
    /**
     * 生成一个随机RGB颜色数组
     * @return array
     */
    public function getRandColor()
    {
        return[mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200)];
    }
    
    /**
     * 生成颜色到调色板
     * ```
     * 第一次创建非透明颜色, 会给[[imagecreate()]] 建立的图像
     * ```
     * 
     * 注意只能在调色板中指定255种颜色
     * @param string|array $color 颜色
     * @param int $alpha 透明度, 范围0~127, 0代表正常, 127代表最大透明度
     * @return int 成功返回颜色索引值, 失败返回false. 判断时需要`===`全等
     */
    public function buildColor($color, $alpha = 0)
    {
        $color = $this->parseColor($color);

        list($red, $green, $blue) = $color;
        
        // 防止重复创建相同的颜色, 已存在返回颜色索引值
        $has = $this->hasColor([$red, $green, $blue], $alpha);
        if ($has !== -1) {
            return $has;
        }
        
        // 调色板已满, 使用已存在相似的颜色
        if ($this->getColorCount() >= 255) {
            $this->getColorCloseset($color, $alpha);
        }
        
        // 分配颜色
        if ($alpha > 0) {
            imagecolorallocatealpha($this->image, $red, $green, $blue, $alpha);
        }
        
        return imagecolorallocate($this->image, $red, $green, $blue);
    }
    
    /**
     * 获取RBG颜色索引
     * @param mixed $color 颜色
     * @return int
     */
    public function getRgbIndex($color)
    {
        if ($color === null || empty($color)) {
            return $color;
        }
        
        // 颜色索引, 可直接使用
        if (is_int($color)) {
            return $color;
        }
        
        // 创建颜色, 并返回颜色索引
        return $this->buildColor($color);
    }
    
    /**
     * 将某个颜色索引定义为透明色 - 抠绿
     * 
     * 透明色, 不同于透明度, 透明度是针对某个颜色的属性
     * 一旦设定了某个颜色为透明色, 图像中该色的仍和区域都成为透明的
     * @param int $color 颜色索引
     */
    public function setColorTransparent($color)
    {
        imagecolortransparent($this->image, $color);
    }
    
    /**
     * 检查颜色是否已存在
     * @param string|array $color 颜色
     * @param int $alpha 透明度, 范围0~127, 0代表正常, 127代表最大透明度
     * @return bool 未创建的颜色返回 `-1`, 否则返回颜色的索引值
     */
    public function hasColor($color, $alpha = 0)
    {
        $color = $this->parseColor($color);
 
        list($red, $green, $blue) = $color;
        
        if ($alpha > 0) {
            // 从调色板中获取颜色
            return imagecolorexactalpha($this->image, $red, $green, $blue, $alpha);
        }
        
        return imagecolorexact($this->image, $red, $green, $blue);
    }
    
    /**
     * 获取调色板中颜色的数量
     * @return int 最大返回256
     */
    public function getColorCount()
    {
        return imagecolorstotal($this->image);
    }
    
    /**
     * 获取调色板中颜色最接近的颜色索引值
     * @param string|array $color 颜色
     * @param int $alpha 透明度, 范围0~127, 0代表正常, 127代表最大透明度
     * @return int 返回颜色索引值
     */
    public function getColorCloseset($color, $alpha = 0)
    {
        $color = $this->parseColor($color);
        
        list($red, $green, $blue) = $color;
        
        if ($alpha > 0) {
            // 取得与指定颜色最接近的颜色的索引值
            imagecolorclosestalpha($this->image, $red, $green, $blue, $alpha);
        }
        
        return imagecolorclosest($this->image, $red, $green, $blue);
    }
    
    /**
     * 获取颜色的更亮版本
     * ```微调参数推荐:
     * 0, 0.1
     * 0, 0.2
     * 0, 0.28
     * 0, 0.38
     * 0, 0.48
     * ```
     * @param string|array $rgb 红黄绿RGB颜色
     * @param float $add 饱和度, 微调范围`0.000001~1`
     * @param float $lightness 明度, 微调范围`0.000001~1`
     * @return string
     */
    public function getColorLight($rgb, $saturation = 0.1, $lightness = 0.1)
    {
        list($red, $green, $blue) = $this->parseColor($rgb);
        
        $col_old = new \qpf\image\lib\RGB($red, $green, $blue);
        $opt = $col_old->toHSL()->toArray();
        $col_new = new \qpf\image\lib\HSL($opt['hue'], min(1, $opt['saturation'] + $saturation), min(1, $opt['lightness'] + $lightness));
        return $col_new->toRGB()->toHex();
    }
    
    /**
     * 获取颜色的更暗版本
     * ```微调参数推荐:
     * 0, 0.1
     * 0.1, 0.2
     * 0.2, 0.2
     * 0.2, 0.3
     * 0.3, 0.4
     * ```
     * @param string|array $rgb
     * @param float $add 饱和度, 微调范围`0.000001~1`
     * @param float $lightness 明度, 微调范围`0.000001~1`
     * @return string
     */
    public function getColorDark($rgb, $saturation = 0.1, $lightness = 0.1)
    {
        list($red, $green, $blue) = $this->parseColor($rgb);
        
        $col_old = new \qpf\image\lib\RGB($red, $green, $blue);
        $opt = $col_old->toHSL()->toArray();
        $col_new = new \qpf\image\lib\HSL($opt['hue'], max(0, $opt['saturation'] - $saturation), max(0, $opt['lightness'] - $lightness));
        return $col_new->toRGB()->toHex();
    }
    
    /**
     * 取得图像宽度
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }
    
    /**
     * 获取图像的高度
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }
    
    /**
     * 获取指定像素的颜色索引值
     * @param int $x 水平坐标
     * @param int $y 垂直坐标
     * @return int 返回颜色索引
     */
    public function getColor($x, $y)
    {
        return imagecolorat($this->image, $x, $y);
    }
    
    /**
     * 设定图像的混色模式 (透明混合)
     * 
     * # 什么是混合模式?
     * 类似PS的图层叠加, 即`正片叠低, 变亮, 插值`等
     * 
     * # 什么时候用?
     * 给图片打水印(叠加透明的图形)是, 需要开启混色模式
     * 
     * - 混色模式在画调色板图像时不可用
     * @param bool $blendmode 是否启用混色模式
     * @return bool
     */
    public function alphablending($blendmode)
    {
        return imagealphablending($this->image, $blendmode);
    }
    
    /**
     * 关闭混色存储透明通道
     */
    public function savealpha()
    {
        // 关闭混色模式
        imagealphablending($this->image, false);
        // 存储透明通道
        imagesavealpha($this->image, true);
    }
    
    /**
     * 水平写一行字
     * @param int $x 水平位置, 字符串左上角
     * @param int $y 垂直位置, 字符串左上角
     * @param string $string 字符串
     * @param unknown $color
     * @param int $font 
     * 若值为 1，2，3，4 或 5，则使用内置字体
     */
    public function string($x, $y, $string, $color, $font)
    {
        // 非颜色索引, 进行创建颜色
        if (!is_int($color)) {
            $color = $this->buildColor($color);
        }
        
        imagestring($this->image, $font, $x, $y, $string, $color);
    }
    
    /**
     * 拷贝图像的一部分
     * @param resource $src_im 水印图片资源
     * @param int $dst_x 基础图片的x坐标
     * @param int $dst_y 基础图片的y坐标
     * @param int $src_x 水印图片的x坐标
     * @param int $src_y 水印图片的y坐标
     * @param int $src_w 水印图片的宽度
     * @param int $src_h 水印图片的高度
     */
    public function imageCopy($src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        imagecopy($this->image, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    /**
     * 拷贝并合并图像的一部分
     * @param resource $src_im 水印图片资源
     * @param int $dst_x 基础图片的x坐标
     * @param int $dst_y 基础图片的y坐标
     * @param int $src_x 水印图片的x坐标
     * @param int $src_y 水印图片的y坐标
     * @param int $src_w 水印图片的宽度
     * @param int $src_h 水印图片的高度
     * @param int $pct 合并成功, 范围0~100, 0什么也没做, 
     * 100对于调色板图像与imagecopy一样, 
     * 对于真彩图像实现alpha透明.
     */
    public function imageCopyMerge($src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        imagecopymerge($this->image, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
    }
    
    /**
     * 拷贝部分图像并调整大小 - GD1
     * 
     * - 使用相对原始的图片缩放算法, 相对不清晰或像素化, 但速度快
     * @param resource $src_image 复制图片的资源句柄
     * @param int $dst_x 画布的x坐标, 起始左上角, 粘贴位置
     * @param int $dst_y 画布的y坐标, 起始左上角, 粘贴位置
     * @param int $src_x 图片的x坐标, 保留范围的左上角, 裁剪起始位置
     * @param int $src_y 图片的y坐标, 保留范围的左上角, 裁剪起始位置
     * @param int $dst_w 粘贴到画布上的宽度, 可拉伸图片
     * @param int $dst_h 粘贴到画布上的高度, 可拉伸图片
     * @param int $src_w 图片的宽度, 可用于缩放
     * @param int $src_h 图片的高度, 可用于缩放
     */
    public function copy($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        imagecopyresized($this->image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }
    
    /**
     * 重采样拷贝部分图像并调整大小 - GD2
     * 
     * - 使用平滑插入算法, 图片智力更高, 处理速度慢
     * @param resource $src_image 复制图片的资源句柄
     * @param int $dst_x 画布的x坐标, 起始左上角, 粘贴位置
     * @param int $dst_y 画布的y坐标, 起始左上角, 粘贴位置
     * @param int $src_x 图片的x坐标, 保留范围的左上角, 裁剪起始位置
     * @param int $src_y 图片的y坐标, 保留范围的左上角, 裁剪起始位置
     * @param int $dst_w 粘贴到画布上的宽度, 可拉伸图片
     * @param int $dst_h 粘贴到画布上的高度, 可拉伸图片
     * @param int $src_w 图片的宽度, 可用于缩放
     * @param int $src_h 图片的高度, 可用于缩放
     */
    public function copy2($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        imagecopyresampled($this->image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }
    
    /**
     * 是否设置了填充贴图
     * @var bool
     */
    private $useTile = false;
    /**
     * 使用贴图颜色标识
     * @var string
     */
    const COL_TILE = 'tile';
    
    /**
     * 设置填充贴图
     * 设置填充颜色传入IMG_COLOR_TILED常量即可使用
     * @param resource $tile 图片资源句柄
     * @return void
     */
    public function useImageColor($tile)
    {
        imagesettile($this->image, $tile);
        $this->useTile = true;
    }
    
    /**
     * 获取贴图颜色索引
     * @return int -5
     */
    public function getTile()
    {
        return IMG_COLOR_TILED;
    }
    
    /**
     * 设置画线的粗度 - 画矩形，多边形，椭圆等
     * @param int $width
     * @return void
     */
    public function sXianWidth($width)
    {
        imagesetthickness($this->image, $width);
    }
    
    /**
     * 根据样式画线
     * @param int $x1 起点x坐标
	 * @param int $y1 起点y坐标
	 * @param int $x2 结点x坐标
	 * @param int $y2 结点y坐标
     * @param array $style
     */
    public function tXianStyle($x1, $y1, $x2, $y2, $style)
    {
        if (is_array($style)) {
            $style = str_replace('\'', '', var_export($style, true));
        }
        imagesetstyle($this->image, $style);
        imageline($this->image, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
    }
    
    /**
     * 矩形区域填充颜色
     * @param int $x 左上角水平位置
     * @param int $y 左上角垂直位置
     * @param array|int|string $color 颜色, 可能的值:
     * 颜色索引, 若是颜色哈希, RBG数组将自动创建颜色
     * @return bool 是否成功
     */
    public function fill($x, $y, $color)
    {
        // 非颜色索引, 进行创建颜色
        if (!is_int($color)) {
            $color = $this->buildColor($color);
        }
        
        return imagefill($this->image, $x, $y, $color);
    }
    
    /**
     * 画点 - 画图工具
     * @param int $x 水平坐标
     * @param int $y 垂直左边
     * @param RGB $color 颜色
     * @return bool
     */
    public function tDian($x, $y, $color)
    {
        $color = $this->getRgbIndex($color);
        
        return imagesetpixel($this->image, $x, $y, $color);
    }
    
    /**
     * 画圆  - 画图工具
     * @param int $cx 圆心x坐标
	 * @param int $cy 圆心Y坐标
	 * @param int $width 圆的宽度
	 * @param int $height 圆的高度
	 * @param RGB $bkColor 边框颜色
	 * @param RGB $bjColor 背景填充颜色
     */
    public function tYuan($cx, $cy, $width, $height, $bkColor, $bjColor = null)
    {
        if (!empty($bjColor)) {
            if (self::COL_TILE == $bjColor) {
                $bjColor = $this->getTile();
            } else {
                $bjColor = $this->getRgbIndex($bjColor);
            }
            
            // 画一椭圆并填充
            imagefilledellipse($this->image, $cx, $cy, $width, $height, $bjColor);
            
            if (!empty($bkColor)) {
                $bkColor = $this->getRgbIndex($bkColor);
                imageellipse($this->image, $cx, $cy, $width, $height, $bkColor);
            }
        } else {
            $bkColor = $this->getRgbIndex($bkColor);
            // 画一个椭圆
            imageellipse($this->image, $cx, $cy, $width, $height, $bkColor);
        }
    }

    /**
     * 画扇形 (圆弧)
     * $bk值：0:填充-无边框 1:填充-弧线 2：填充-边框
     * @param int $cx 圆心x坐标
     * @param int $cy 圆心y坐标
     * @param int $width 圆的宽度
     * @param int $height 圆的高度
     * @param int $start 开始时间, 0 在水平右侧
     * @param int $end 结束时间, 顺时针
     * @param RGB $color 颜色
     * @param RGB $bjColor 背景颜色
     * @param int $style 样式 0无边框 1填充+圆弧 2填充+边框 3画三角形：最大180°
     * @return void
     */
    public function tYuanHu($cx, $cy, $width, $height, $start, $end, $color, $bjColor = null, $style = 0)
    {
        $color = $this->getRgbIndex($color);
        if (!empty($bjColor)) {
            if (self::COL_TILE == $bjColor) {
                $bjColor = $this->getTile();
            } else {
                $bjColor = $this->getRgbIndex($bjColor);
            }
            
            if ($style == 2) {
                $width = $width - 2;
                $height = $height - 2;
                // 画一椭圆弧且填充
                imagefilledarc($this->image, $cx - 1, $cy + 1, $width, $height, $start, $end, $color, IMG_ARC_PIE);
                imagefilledarc($this->image, $cx + 1, $cy - 1, $width, $height, $start, $end, $color, IMG_ARC_PIE);
                imagefilledarc($this->image, $cx + 1, $cy + 1, $width, $height, $start, $end, $color, IMG_ARC_PIE);
                imagefilledarc($this->image, $cx - 1, $cy - 1, $width, $height, $start, $end, $color, IMG_ARC_PIE);
            } elseif ($style == 1) {
                imagefilledarc($this->image, $cx, $cy, $width, $height, $start, $end, $color, IMG_ARC_CHORD);
            } elseif ($style == 3) {
                imagefilledarc($this->image, $cx, $cy, $width, $height, $start, $end, $bjColor, IMG_ARC_NOFILL);
            } else {
                imagefilledarc($this->image, $cx, $cy, $width, $height, $start, $end, $bjColor, IMG_ARC_PIE);
            }
        } else {
            // 画椭圆弧
            imagearc($this->image, $cx, $cy, $width, $height, $start, $end, $color);
        }
    }
    
    /**
     * 画线
	 * @param int $x1 起点x坐标
	 * @param int $y1 起点y坐标
	 * @param int $x2 结点x坐标
	 * @param int $y2 结点y坐标
	 * @param RGB $color 颜色
     */
    public function tXian($x1, $y1, $x2, $y2, $color)
    {
        $color = $this->getRgbIndex($color);
        imageline($this->image, $x1, $y1, $x2, $y2, $color);
    }
    
    /**
     * 画横线
     * @param int $x 水平坐标, 起始点
     * @param int $y 垂直位置, 起始点
     * @param int $width 宽度
     * @param RGB $color 颜色
     */
    public function tHr($x, $y, $width, $color)
    {
        $this->tXian($x, $y, $x + $width, $y, $color);
    }

    /**
     * 画线段
     * @param int $x1 起点x坐标
     * @param int $y1 起点y坐标
     * @param int $x2 结点x坐标
     * @param int $y2 结点y坐标
     * @param RGB $color 线段的颜色
     * @param int $width 线段的长度, 单位px
     * @param RGB $color2 线段融入背景的颜色, 默认为透明, 可指定颜色变为彩线
     */
    public function tXianDuan($x1, $y1, $x2, $y2, $color, $width, $color2 = null)
    {
        $color = $this->getRgbIndex($color);
        if (empty($color2)) {
            // 默认透明消失的部分
            $color2 = IMG_COLOR_TRANSPARENT;
        } else {
            // 自定义线段消失部分的颜色
            $color2 = $this->getRgbIndex($color);
        }
        $style = [];
        for ($i = 0; $i < $width; $i++) {
            $style[] = $color;
        }
        for ($i = 0; $i < $width; $i++) {
            $style[] = $color2;
        } 
        // 设定画线的风格
        imagesetstyle($this->image, $style);
        // 画线
        imageline($this->image, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
    }
    
    /**
     * 画线 - 多边形 会自动直线连接闭合
     * @param array $points 坐标点数组, 成对的[x1, y1, x2, y2, ...]
     * @param int $num_points 显示点的数量, 最少3个坐标
     * @param RGB $color 边框颜色
     * @param RGB $bjColor 填充色, 使用填充必须大于等于3个点
     */
    public function tXians(array $points, $num_points, $color, $bjColor = null)
    {
        if (!isset($points[($num_points * 2 - 1)])) {
            throw new \Exception('要显示的点与提供的坐标点数量不匹配');
        }
        $color = $this->getRgbIndex($color);
        
        // 填充背景色
        if (!empty($bjColor) && $num_points >= 3) {
            if (self::COL_TILE == $bjColor) {
                $bjColor = $this->getTile();
            } else {
                $bjColor = $this->getRgbIndex($bjColor);
            }
            // 画一多边形并填充
            imagefilledpolygon($this->image, $points, $num_points, $bjColor);
            
            // 描边
            if (!empty($color)) {
                imagepolygon($this->image, $points, $num_points, $color);
            }
            
        } else {
            // 绘制一个多边形, 最少3个点
            imagepolygon($this->image, $points, $num_points, $color);
        }
    }

    /**
     * 画矩形
     * @param int $x1 左上角x坐标
     * @param int $y1 左上角y坐标
     * @param int $widch 宽度
     * @param int $height 高度
     * @param RGB $color 边框颜色
     * @param RGB $bjColor 背景颜色
     */
    public function tJuXing($x1, $y1, $widch, $height, $color, $bjColor = null)
    {
        $x2 = $widch + $x1;
        $y2 = $height + $y1;
        if (! empty($bjColor)) {
            if (self::COL_TILE == $bjColor) {
                $bjColor = $this->getTile();
            } else {
                $bjColor = $this->getRgbIndex($bjColor);
            }
            // 画一矩形并填充
            imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $bjColor);
            
            // 描边
            if (!empty($color)) {
                $color = $this->getRgbIndex($color);
                imagerectangle($this->image, $x1, $y1, $x2, $y2, $color);
            }
        } else {
            $color = $this->getRgbIndex($color);
            imagerectangle($this->image, $x1, $y1, $x2, $y2, $color);
        }
    }
    
    /**
     * 画圆柱
     * 
     * 圆柱的参数设置, 可以当作画矩形来理解
     * @param int $sx 水平坐标, 左上角
     * @param int $sy 垂直坐标, 左上角
     * @param int $w 圆柱宽度, px
     * @param int $h 圆柱高度, px
     * @param string|array $rgb 颜色, 不支持传入外部颜色索引, 例`r,g,b`
     * @param bool $_3d 是否启用3D效果
     * @return void
     */
    public function tYuanZhu($sx, $sy, $w, $h, $rgb, $_3d = false)
    {
        // 圆柱填充色
        $col_yz = $this->parseColor($rgb);
       

        $yz_yw = $w; // 圆柱-圆宽
        $yz_yh = $w / 3; // 圆柱-圆-高
        $yz_yx = $sx + $w / 2;// 圆柱-水平位置
        $yz_yy1 = $sy;
        $yz_yy2 = $sy + $h;
        $yz_jx_sx = $sx; // 圆柱-矩形-x坐标, 左上角
        $yz_jx_sy = $sy; // 圆柱-矩形-y坐标, 左上角
        
        $yz_fzx_ex = $sx + $w;
        $yz_fzx_y = $sy + $h;
        
        // 生成黑色 用于描边
        $col_hei = $this->buildColor([0, 0, 0]);
        // 生成透明黑色, 用于阴影
        $col_hei_yin = $this->buildColor([0, 0, 0], 100);
        
        // 阴影
        if ($_3d === true) {
            $this->tYuan($sx+$w - ($w*0.2), $yz_yy2, $w, $yz_yh, '', $col_hei_yin);
        }
        
        $this->tYuan($yz_yx, $yz_yy2, $yz_yw, $yz_yh, $col_hei, $col_yz); // 底部圆形
        $this->tJuXing($yz_jx_sx, $yz_jx_sy, $w, $h, $col_hei, $col_yz); // 中间矩形
        $this->tXian($sx+1, $yz_fzx_y, $yz_fzx_ex-1, $yz_fzx_y, $col_yz); // 底部遮盖线
        
        // 圆柱浅色
        $col_yz_qian = $this->getColorLight($col_yz);
        
        // 高光
        if ($_3d === true) {
            // 圆心水平位置+1 防止覆盖圆柱边框, 垂直为总高度的一半
            $this->tYuanHu($sx+1, $sy+$h/2, $w+$w/3, $h, -90, 90, '', $col_yz_qian);
        }
        
        // 顶部圆
        $this->tYuan($yz_yx, $yz_yy1, $yz_yw, $yz_yh, $col_hei, $col_yz_qian);
    }
    
    /**
     * 规律间隔递增
     * @param string $id 间隔标识
     * @param int $spacing 递增间距, 默认`1`
     * @param int $start 起始坐标, 默认`0`
     * @param int $type 方向类型, 默认`x`, 可自定义方向标识`y`
     */
    public function getSpace($id, $spacing = 1, $start = 0, $type = 'x')
    {
        static $_data = [];
        
        // 清空记录
        if ($id === null) {
            $_data = []; 
        }
        
        // 方向识别
        $sid = $id.'_'.$type;
        
        // 清除指定间隔标识
        if ($spacing === null) {
            foreach ($_data as $name => $v) {
                // `id_`开头全部删除
                if (strncmp($name, $id . '_', strlen($id . '_')) === 0) {
                    unset($_data[$name]);
                }
            }
        }
        
        // 存在递增
        if (key_exists($sid, $_data)) {
            return $_data[$sid] += $spacing;
        } else {
            // 首次记录
            return $_data[$sid] = $start + $spacing;
        }
    }
    
    /**
     * 获取当前图片指定九宫格位置
     * @param int $pos 九宫格位置, 1~9, 0 代表随机位置
     * @return array 返回指定格子的x,y坐标
     */
    public function jiugongge($pos)
    {
        $ws = $this->getWidth() / 9;
        $hs = $this->getHeight() / 9;
        switch ($pos) {
            case 1 :
                $x = $y = 25;
                break;
            case 2 :
                $x = ($imgWidth - $waterWidth) / 2;
                $y = 25;
                break;
            case 3 :
                $x = $imgWidth - $waterWidth;
                $y = 25;
                break;
            case 4 :
                $x = 25;
                $y = ($imgHeight - $waterHeight) / 2;
                break;
            case 5 :
                $x = ($imgWidth - $waterWidth) / 2;
                $y = ($imgHeight - $waterHeight) / 2;
                break;
            case 6 :
                $x = $imgWidth - $waterWidth;
                $y = ($imgHeight - $waterHeight) / 2;
                break;
            case 7 :
                $x = 25;
                $y = $imgHeight - $waterHeight;
                break;
            case 8 :
                $x = ($imgWidth - $waterWidth) / 2;
                $y = $imgHeight - $waterHeight;
                break;
            case 9 :
                $x = $imgWidth - $waterWidth - 10;
                $y = $imgHeight - $waterHeight;
                break;
            default :
                $x = mt_rand(25, $imgWidth - $waterWidth);
                $y = mt_rand(25, $imgHeight - $waterHeight);
        }
    }

    /**
     * 圆柱 - 图标
     *
     * ```
     * [高度1, 高度2, 高度3, ...]
     * ```
     *
     * @param array $group 参数组
     * @param int $gx 水平坐标, 圆柱组的左下角
     * @param int $gy 垂直坐标, 圆柱组的左下角
     * @param int $w 宽度, 公共属性, 可覆盖
     * @param int $h 高度, 公共属性, 可覆盖
     * @param RGB $rgb 圆柱颜色
     * @param bool $_3d 是否3D化
     */
    public function tYuanZhuGroup(array $group, $gx, $gy, $w, $h, $rgb, $_3d = false)
    {
        foreach ($group as $yz) {
            // 组y位置 - 圆柱高度 = 圆柱底部对齐的y位置
            $this->tYuanZhu($this->getSpace('__yzg__', $w, $gx, 'x'), $gy - $yz, $w, $yz, $rgb, $_3d);
        }
        $this->getSpace('__yzg__', null);
    }
    
    /**
     * 写字 - 仅限英文
     * @param string $string 内容
	 * @param int $size 如果是 1，2，3，4 或 5，则使用内置字体, 推荐`5`
	 * @param int $x 坐标x
	 * @param int $y 坐标y
	 * @param RGB $color 颜色
	 * @param bool $up 是否垂直排列
     */
    public function tA($string, $size, $x, $y, $color, $up = false)
    {
        $color = $this->getRgbIndex($color);
        if ($up) {
            imagestringup($this->image, $size, $x, $y, $string, $color);
        } else {
            imagestring($this->image, $size, $x, $y, $string, $color);
        }
    }
    
    /**
     * 测量TTF字体文本的范围
     * @param float $size 字体大小, 单位像素
     * @param float $angle 角度
     * @param string $fontfile 字体文件
     * @param string $text 要测量的字符串
     * @return array 返回含有8个元素的数组, 表示文本外框的四个角, 
     * 逆时针从左下角开始, 跟文本角度无关
     * [左下x, 左下y, 右下x, 右下y, 右上x, 右上y, 左上x, 左上y]
     */
    public function getTtfBox($size, $angle, $fontfile, $text)
    {
        return imagettfbbox($size, $angle, $fontfile, $text);
    }
    
    /**
     * 获取TTF字体文本的范围信息
     * @param float $size 字体大小, 单位像素
     * @param float $angle 角度
     * @param string $fontfile 字体文件
     * @param string $text 要测量的字符串
     * @return array 返回数组包含:
     * - x : 水平坐标, 文本左下角
     * - y : 垂直坐标, 文本左下角
     * - width : 文本占用宽度, 像素
     * - height : 文本占用高度, 像素
     * - box : 原始文本范围数组
     * - cx : 文本水平居中, 左下角位置
     * - cy : 文本垂直居中, 左下角位置
     */
    public function getTtfBoxInfo($size, $angle, $fontfile, $text)
    {
        $box = imagettfbbox($size, $angle, $fontfile, $text);
        $x = abs($box[6]);
        $y = abs($box[7]);
        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[1]);
        $cx = $x + ($this->getWidth() / 2) - ($width / 2);
        $cy = $y + ($this->getHeight() / 2) - ($height / 2);
        return [
            'x'         => $x,
            'y'         => $y,
            'width'     => $width,
            'height'    => $height,
            'box'       => $box,
            'cx'        => $cx,
            'cy'        => $cy,
        ];
    }

    /**
     * 写字 - 自定义TTF字体
     * @param string $string 内容
	 * @param int $size 字体大小
	 * @param int $x 左下角坐标x
	 * @param int $y 左下角坐标y
	 * @param RGB $color 颜色
	 * @param int $angle 角度
	 * @param string $fontfile 字体文件
	 * @return 8 个单元的数组表示了文本外框的四个角
     */
    public function tATtf($string, $size, $x, $y, $color, $angle, $fontfile)
    {
        $color = $this->getRgbIndex($color);
        imagettftext($this->image, $size, $angle, $x, $y, $color, $fontfile, $string);
    }

    /**
     * 获得字体文件名 - 字体库
     * @param string $font 字体, 未指定会返回默认字体
     * @return string 返回字体路径
     */
    public function getTTF($font = 'default')
    {
        return Font::get($font);
    }
    
    /**
     * 返回首选的中文字体
     * @return string
     */
    public function getZhTTF()
    {
        return Font::get('zh');
    }
    
    /**
     * 返回首选的英文字体
     * @return string
     */
    public function getUsTTF()
    {
        return Font::get('arial');
    }
    
    /**
	 * 绘制网格
	 * 
	 * @param $sx 起点x
	 * @param $sy 起点y
	 * @param $width 宽度
	 * @param $height 高度
	 * @param $gw 宽度间隔
	 * @param $gh 高度间隔
	 * @param $color 颜色
	 * */
    public function tWangGe($sx,$sy,$width,$height,$gw,$gh,$color)
    {
        $_i = $width / $gw;
        $_g = $height / $gh;
        $width += $sx;
        $height += $sy;
        $x = $sx;
        $y = $sy;
        // 画横线
        for ($i = 0; $i <= $_i; $i ++) {
            $this->tXian($sx, $sy, $sx, $height, $color);
            $sx += $gw;
        }
        // 画竖线
        for ($g = 0; $g <= $_g; $g ++) {
            $this->tXian($x, $sy, $width, $sy, $color);
            $sy += $gh;
        }
    }
    
    /**
     * 绘制网格文字
     *
     * @param $word 传值为array
     * @param $size 文字大小
     * @param $color 文字颜色
     * @param $sx 开始位置x
     * @param $sy 开始位置y
     * @param $gw 间隔宽度
     * @param $gh 间隔高度
     * @param $angle 倾斜角度
     * */
    function tWangGeWord($word,$size,$color,$sx,$sy,$gw,$gh,$angle=0){
        $ww=$sx;
        $wh=$sy;
        if (is_array($word)) {
            foreach ($word as $key=>$val){
                $this->tATtf($val, $size, $ww, $wh, $color, $angle, $this->ecTtf('宋体'));
                $ww+=$gw; $wh+=$gh;
            }
        }
        
    }
    
    
    
    /**
     * 检查图片类型是否支持
     * @return bool
     */
    public function checkImageType($type)
    {
        if (imagetypes() && constant('IMG_' . strtoupper($type))) {
            return true;
        }
        
        throw new \Exception('系统不支持该图片类型: ' . $type);
    }
    
    /**
     * 输出指定类型的图片到游览器
     * @param string $type 图片类型
     */
    public function makeType($type)
    {
        switch ($type) {
            case 'png':
                $this->makePng();
                break;
            case 'jpg':
            case 'jpeg':
                $this->makeJpg();
                break;
            case 'gif':
                $this->makeGif();
                break;
            default:
                throw new \Exception('不支持的图片类型' . $type);
        }
    }
    
    /**
     * 输出PNG图像到游览器
     * @param int $quality 压缩级别：从0(无压缩)到9。
     * @return void
     */
    public function makePng($quality = 0)
    {
        $this->checkImageType('png');
        $this->sendHeader('png');
        imagepng($this->image, null, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 输出PNG图像到游览器 - 保持透明度
     * @param int $quality 压缩级别：从0(无压缩)到9。
     * @return void
     */
    public function makePngAlpha($quality = 0)
    {
        $this->checkImageType('png');
        $this->savealpha();
        $this->sendHeader('png');
        imagepng($this->image, null, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 输出GIF图像到游览器
     */
    public function makeGif()
    {
        $this->checkImageType('gif');
        $this->sendHeader('gif');
        imagegif($this->image);
        imagedestroy($this->image);
    }
    
    /**
     * 输出GIF图像到游览器  - 保持透明度
     */
    public function makeGifAlpha()
    {
        $this->checkImageType('gif');
        $this->savealpha();
        $this->sendHeader('gif');
        imagegif($this->image);
        imagedestroy($this->image);
    }
    
    /**
     * 输出JPG图像到游览器
     * @param int $quality 可选, 范围0(最差,最小)~100(最好,最大), 图片质量默认75%
     */
    public function makeJpg($quality = 75)
    {
        $this->checkImageType('jpg');
        $this->sendHeader('jpg');
        imagejpeg($this->image, null, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 发送内容类型报头
     * @param string $type 资源类型
     * @return void
     */
    protected function sendHeader($type)
    {
        $mime = [
            'jpeg'  => 'image/jpeg',
            'jpg'   => 'image/jpeg',
            'png'   => 'image/png',
            'gif'   => 'image/gif',
            'wbmp'  => 'image/vnd.wap.wbmp',
        ];
        
        header('Content-Type: ' . $mime[$type]);
    }
    
    /**
     * 保存为PNG图片
     * @param string $file 文件路径
     * @param int $quality 压缩级别：从0(无压缩)到9
     */
    public function savePng($file, $quality = null)
    {
        imagepng($this->image, $file, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 保存为PNG图片
     * @param string $file 文件路径
     * @param int $quality 压缩级别：从0(无压缩)到9
     */
    public function savePngAlpha($file, $quality = null)
    {
        $this->savealpha();
        imagepng($this->image, $file, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 保存为PNG图片
     * @param string $file 文件路径
     * @param int $quality 可选, 范围0(最差,最小)~100(最好,最大), 图片质量默认75%
     */
    public function saveJpg($file, $quality = 75)
    {
        imagejpeg($this->image, $file, $quality);
        imagedestroy($this->image);
    }
    
    /**
     * 保存为PNG图片
     * @param string $file 文件路径
     */
    public function saveGif($file)
    {
        imagegif($this->image, $file);
        imagedestroy($this->image);
    }
    
    /**
     * 保存为PNG图片
     * @param string $file 文件路径
     */
    public function saveGifAlpha($file)
    {
        $this->savealpha();
        imagegif($this->image, $file);
        imagedestroy($this->image);
    }
    
    /**
     * 保存为指定类型
     * @param string $type 图片类型
     * @param string $file 文件路径
     */
    public function saveType($type, $file)
    {
        switch ($type) {
            case 'png':
                $this->savePng($file, 0);
                break;
            case 'jpg':
            case 'jpeg':
                $this->saveJpg($file, 100);
                break;
            case 'gif':
                $this->saveGif($file);
                break;
            default:
                throw new \Exception('不支持的图片类型');
        }
    }
    
    /**
     * 销毁图像释放内存
     */
    public function destroy()
    {
        imagedestroy($this->image);
    }
}