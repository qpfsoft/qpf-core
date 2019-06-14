<?php
namespace qpf\image;

use qpf\image\lib\ImageTrueColor;
use qpf\image\lib\ImageBase;
use qpf\image\lib\Image;
use qpf\exceptions\NotFoundException;

/**
 * 易图
 * 
 * 基于GD模块的绘图工具类
 * 
 * @version 1.2
 */
class EasyChart
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        if (!extension_loaded('gd') && !function_exists('imagecreatetruecolor')) {
            throw new NotFoundException('程序无法运行！系统缺少gd组件');
        }
    }
    
    /**
     * 获取GD库的安装支持信息
     * ```
     * JIS-mapped : JIS映射的日语字体支持
     * FreeType : 安装了Freetype支持
     * ```
     * @return array
     */
    public function getInf()
    {
        static $info;
        
        if ($info === null) {
            $info = gd_info();
            $temp = [];
            foreach ($info as $name => $val) {
                if (is_bool($val)) {
                    $arr = explode(' ', $name, 2);
                    $temp[array_shift($arr)] = $val;
                }
            }
            
            $info = $temp;
        }
        
        return $info;
    }
        
    /**
     * 获取支持的图片类型
     * @return array
     */
    public function getImageTypes()
    {
        static $types;
        
        if ($types === null) {
            $info = $this->getInf();
            $img_type = ['GIF', 'JPEG', 'PNG', 'WBMP', 'XPM', 'XBM'];
            $types = [];
            foreach ($img_type as $i => $ext) {
                if (isset($info[$ext]) && $info[$ext]) {
                    $types[] = $ext;
                }
            }
        }
        
        return $types;
    }
    
    /**
     * 是否支持PNG图像类型
     * @return bool
     */
    public function hasPng()
    {
        return (imagetypes() & IMG_PNG) ? true : false;
    }
    
    /**
     * 是否支持JPG/JPEG图像类型
     * @return bool
     */
    public function hasJpg()
    {
        return (imagetypes() & IMG_JPG) ? true : false;
    }

    /**
     * 是否支持GIF图像类型
     * @return bool
     */
    public function hasGif()
    {
        return (imagetypes() & IMG_GIF) ? true : false;
    }
    
    /**
     * 是否支持WBMP图像类型
     * @return bool
     */
    public function hasWbmp()
    {
        return (imagetypes() & IMG_WBMP) ? true : false;
    }
    
    /**
     * 获取图像大小 - 不依赖GD库
     * 
     * @param string $filename 文件路径或图片URL
     * @return array [$width, $height, $type, $attr]
     */
    public function getImageSize($filename)
    {
        return getimagesize($filename);
    }
    
    /**
     * 获取图片类型或转换类型为描述  - 不依赖GD库
     * @param string $filename 文件路径或图片URL, 或图片类型的int值
     * @return string
     */
    public function getImageType($filename)
    {
        $imageTypeArray = [
            0 => 'UNKNOWN',1 => 'GIF',2 => 'JPEG',3 => 'PNG',4 => 'SWF',5 => 'PSD',6 => 'BMP',7 => 'TIFF_II',
            8 => 'TIFF_MM',9 => 'JPC',10 => 'JP2',11 => 'JPX',12 => 'JB2',13 => 'SWC',14 => 'IFF',15 => 'WBMP',
            16 => 'XBM',17 => 'ICO',18 => 'COUNT'
        ];
        
        if (is_numeric($filename)) {
            return $imageTypeArray[$filename];
        }
        
        $size = getimagesize($filename);
        return $imageTypeArray[$size[2]];
    }
    
    /**
     * 获取图片类型的文件扩展名  - 不依赖GD库
     * @param string $imagetype 图片类型, IMAGETYPE_XXX 系列常量
     * @return string 返回图片文件的扩展名
     */
    public function getImageExt($imagetype)
    {
        return image_type_to_extension($imagetype);
    }
    
    /**
     * 创建一个基于调色板的图像
     * ```
     * imagecreatetruecolor需要用imagefill()来填充颜色(不填充时为"黑色")
     * imagecreate()需要用imagecolorAllocate()添加背景色(必须填充)
     * ```
     * 
     * 特别注意:
     * 基于调色板的图像, 第一次创建颜色(它可能来自其它方法设置的颜色), 将自动变为背景色!
     * @param int $width 宽度
     * @param int $height 高度
     * @return ImageBase
     */
    public function create($width, $height)
    {
        return new ImageBase($width, $height);
    }
    
    /**
     * 创建真彩图像 - GD2
     * @param int $width 宽度
     * @param int $height 高度
     * @return ImageTrueColor
     */
    public function createTrueColor($width, $height)
    {
        return new ImageTrueColor($width, $height);
    }
    
    /**
     * 打开图片资源
     * @param string $filename 图片路径或图片URL
     * @return \qpf\image\lib\Image
     */
    public function openImage($filename)
    {
        return new Image($filename);
    }
    
    /**
     * 无需下载/读取整个图像即可检索JPEG宽度和高度
     * @param string $filename 文件路径或图片URL
     * @return array|false 返回[宽度, 高度]
     */
    public function getJpegSize($filename)
    {
        $handle = fopen($filename, "rb") or die("Invalid file stream.");
        $new_block = null;
        if (! feof($handle)) {
            $new_block = fread($handle, 32);
            $i = 0;
            if ($new_block[$i] == "\xFF" && $new_block[$i + 1] == "\xD8" && $new_block[$i + 2] == "\xFF" &&
                 $new_block[$i + 3] == "\xE0") {
                $i += 4;
                if ($new_block[$i + 2] == "\x4A" && $new_block[$i + 3] == "\x46" && $new_block[$i + 4] == "\x49" &&
                     $new_block[$i + 5] == "\x46" && $new_block[$i + 6] == "\x00") {
                    // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                    $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                    $block_size = hexdec($block_size[1]);
                    while (! feof($handle)) {
                        $i += $block_size;
                        $new_block .= fread($handle, $block_size);
                        if ($new_block[$i] == "\xFF") {
                            // New block detected, check for SOF marker
                            $sof_marker = array("\xC0","\xC1","\xC2","\xC3","\xC5","\xC6","\xC7","\xC8","\xC9","\xCA","\xCB","\xCD","\xCE","\xCF");
                            if (in_array($new_block[$i + 1], $sof_marker)) {
                                // SOF marker detected. Width and height information is contained in bytes 4-7 after
                                // this byte.
                                $size_data = $new_block[$i + 2] . $new_block[$i + 3] . $new_block[$i + 4] .
                                     $new_block[$i + 5] . $new_block[$i + 6] . $new_block[$i + 7] . $new_block[$i + 8];
                                $unpacked = unpack("H*", $size_data);
                                $unpacked = $unpacked[1];
                                $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                                $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                                return array($width,$height);
                            } else {
                                // Skip block marker and read block size
                                $i += 2;
                                $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                                $block_size = hexdec($block_size[1]);
                            }
                        } else {
                            return FALSE;
                        }
                    }
                }
            }
        }
        return FALSE;
    }
}