<?php
namespace qpf\image\lib;

use qpf\exception\ParamException;

/**
 * Image 该类代表了一个图片资源
 * 
 * 可打开现有图片进行缩放裁剪[[thumbnail]], 加水印[[watermark]].
 */
class Image extends ImageBase
{
    /**
     * 当前打开图片的信息
     * [
     *    'ext'  => '.png', // 后缀名
     *    'type' => 'png', // 图片类型
     *    'width' => 200, // 宽度
     *    'height' => 50, // 高度
     *    'mime'   => 'image/png', // 媒体类型
     * ]
     * @var array
     */
    public $info = [];
    
    /**
     * 构造函数
     * @param string $filename 图片文件或图片URL
     */
    public function __construct($filename)
    {
        $this->image = $this->open($filename);
    }
    
    /**
     * 打开图片
     * @param string $file 图片文件或图片URL
     * @return resource 返回该图片资源句柄
     */
    protected function open($file)
    {
        if (!$this->isImage($file)) {
            throw new ParamException('参数期望: 图片文件');
        }
        // 图片扩展名
        $array = getimagesize($file);
        $this->info['width'] = $array[0];
        $this->info['height'] = $array[1];
        $this->info['int_type'] = $array[2];
        // 后缀名`.png`
        $this->info['ext'] = image_type_to_extension($array[2]);
        // 类型
        $this->info['type'] = substr($this->info['ext'], 1);
        // jpeg采用简写后缀名
        if ($this->info['ext'] == '.jpeg') {
            $this->info['ext'] = '.jpg';
            $this->info['type'] = 'jpg';
        }
        // 媒体类型 `image/png`
        $this->info['mime'] = $array['mime'];
        $image = null;
        
        switch ($this->info['type']) {
            case 'png':
                $image = @imagecreatefrompng($file);
                break;
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($file);
                break;
            case 'gif':
                $image = @imagecreatefromgif($file);
                break;
            default:
                throw new \Exception('不支持的图片类型');
        }
        
        // 打开失败
        if (!$image) {
            $img = new ImageTrueColor(150, 30);
            $img->setBgColor([255, 255, 255]);
            $img->string(1, 5, 'Error loading', [ 0, 0, 0], 5);
            return $img->image;
        }
        
        return $image;
    }
    
    /**
     * 裁剪图片的大小并保存 - 缩放,速度快,质量低
     * 
     * - 原始图片没有超过限定宽高, 尺寸不变
     * @param string $file 新的文件名, 默认null直接输出
     * @param int $max_width 最大宽度
     * @param int $max_height 最大高度
     * @return void
     */
    public function setSize($file = null, $max_width = 200, $max_height = 200)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $x_ratio = $max_width / $width;
        $y_ratio = $max_height / $height;
        
        // 宽高都小于最大值
        if (($width <= $max_width) && ($height <= $max_height)) {
            $tn_width = $width;
            $tn_height = $height;
        } elseif (($x_ratio + $height) < $max_height) {
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_width;
        } else {
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_height;
        }
        // 创建新图像
        $tmp = new ImageTrueColor($tn_width, $tn_height);
        // 检查此图像是PNG还是GIF，然后设置是否为透明
        if ($this->info['type'] == 'gif' || $this->info['type'] == 'png') {
            $tmp->savealpha();
            $tmp->setBgColor([255, 255, 255], 127);
        }
        // 调整图像大小 - 低质量- 快速
        $tmp->copy($this->image, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
        
        if ($file) {
            switch ($this->info['type']) {
                case 'png':
                    $tmp->savePng($file, 0);
                    break;
                case 'jpg':
                case 'jpeg':
                    $tmp->saveJpg($file, 100);
                    break;
                case 'gif':
                    $tmp->saveGif($file);
                    break;
                default:
                    throw new \Exception('不支持的图片类型');
            }
        } else {
            switch ($this->info['type']) {
                case 'png':
                    $tmp->makePng();
                    break;
                case 'jpg':
                case 'jpeg':
                    $tmp->makeJpg();
                    break;
                case 'gif':
                    $tmp->makeGif();
                    break;
                default:
                    throw new \Exception('不支持的图片类型');
            }
        }
    }

    /**
     * 裁剪图片 - 略缩图
     * @param string $file 另存文件名
     * @param int $toWidth 裁剪后宽度
     * @param int $toHeight 裁剪后高度
     * @param int $toType 裁剪类型, 可能的值:
     * - 1 固定宽度, 高度自增
     * - 2 固定高度, 宽度自增
     * - 3 固定宽度, 高度裁剪
     * - 4 固定高度, 宽度裁剪
     * - 5 缩放最大边, 原图不裁剪
     * - 6 略缩图尺寸不变, 自动裁切最大边
     */
    public function thumbnail($file, $toWidth = 200, $toHeight = 200, $toType = 6)
    {
        $crop_size = $this->thumbnailSize($this->info['width'], $this->info['height'], $toWidth, $toHeight, $toType);
        
        // 创建略缩图
        if ($this->info['type'] == 'gif') {
            $temp = new ImageBase($crop_size[0], $crop_size[1]);
            $bgColor = $temp->buildColor([255, 0, 0]);
        } else {
            $temp = new ImageTrueColor($crop_size[0], $crop_size[1]);
            $temp->savealpha();
        }
        
        // 采集像素
        if (function_exists('imagecopyresampled')) {
            $temp->copy2($this->image, 0, 0, 0, 0, $crop_size[0], $crop_size[1], $crop_size[2], $crop_size[3]);
        } else {
            $temp->copy($this->image, 0, 0, 0, 0, $crop_size[0], $crop_size[1], $crop_size[2], $crop_size[3]);
        }
        
        if ($this->info['type'] == 'gif') {
            // 透明背景
            $temp->setColorTransparent($bgColor);
        }
        
        // 创建目录
        is_dir(dirname($file)) || mkdir(dirname($file), 0755, true);
        
        // 保存类型
        $temp->saveType($this->info['type'], $file);
        
        // 释放原始图片资源
        $this->destroy();
        
        return true;
    }
    
    /**
     * 计算指定裁剪类型的尺寸信息
     * @param int $width 原始宽度
     * @param int $height 原始高度
     * @param int $toWidth 裁剪后宽度
     * @param int $toHeight 裁剪后高度
     * @param int $toType 裁剪类型
     */
    private function thumbnailSize($width, $height, $toWidth, $toHeight, $toType)
    {
        // 略缩图尺寸
        $w = $toWidth;
        $h = $toHeight;
        // 原始尺寸
        $cuthumbWidth = $width;
        $cuthumbHeight = $height;
        switch ($toType) {
            case 1 :
                //固定宽度  高度自增
                $h = $toWidth / $width * $height;
                break;
            case 2 :
                //固定高度  宽度自增
                $w = $height / $height * $width;
                break;
            case 3 :
                //固定宽度  高度裁切
                $cuthumbHeight = $width / $toWidth * $toHeight;
                break;
            case 4 :
                //固定高度  宽度裁切
                $cuthumbWidth = $height / $toHeight * $toWidth;
                break;
            case 5 :
                //缩放最大边 原图不裁切
                if (($width / $toWidth) > ($height / $toHeight)) {
                    $h = $toWidth / $width * $height;
                } elseif (($width / $toWidth) < ($height / $toHeight)) {
                    $w = $toWidth / $height * $width;
                } else {
                    $w = $toWidth;
                    $h = $toHeight;
                }
                break;
            default:
                //缩略图尺寸不变，自动裁切图片
                if (($height / $toHeight) < ($width / $toWidth)) {
                    $cuthumbWidth = $height / $toHeight * $toWidth;
                } elseif (($height / $toHeight) > ($width / $toWidth)) {
                    $cuthumbHeight = $width / $toWidth * $toHeight;
                }
        }

        return [$w, $h, $cuthumbWidth, $cuthumbHeight];
    }
    
    /**
     * 水印图片
     * @param string $file 处理结果保存文件路径
     * @param string $waterImage 水印图片, 未设置时会采用文本水印
     * @param string $pos 水印9宫格, 1~9个位置, 0为随机位置, 默认`9`右下角
     * @param array $text 文本水印内容, 水印文本,
     * - size : 字体大小
     * - angle : 角度
     * - ttf : 字体名称, 依赖于Font组件
     * - text : 文本内容
     * @param string $alpha 合并程度, 不透明0~100完全透明, 默认`60`, 调色板图片不会透明
     * @return bool
     */
    public function watermark($file, $waterImage, $pos = 9, $text = ['text' => 'QPF-EC', 'size' => 12, 'angle' => 0, 'ttf' => 'zh', 'color' => '#f00f00'], $alpha = 60)
    {
        if (!empty($waterImage)) {
            $water = new Image($waterImage);
            
            $waterWidth = $water->info['width'];
            $waterHeight = $water->info['height'];
        } else {
            // 水印图片不存在, 采用水印文本
            $box = imagettfbbox($text['size'], $text['angle'], $this->getTTF($text['ttf']), $text['text']);
            
            $waterWidth = $box[2] - $box[6];
            $waterHeight = $box[3] - $box[7];
        }
        
        $imgWidth = $this->info['width'];
        $imgHeight = $this->info['height'];
        
        // 水印尺寸大于原始图片
        if ($imgHeight < $waterHeight || $imgWidth < $waterWidth) {
            return false;
        }
        
        // 水印位置
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
        
        if (isset($water)) {
            // 使用水印图片
            if ($water->info['int_type'] == IMAGETYPE_PNG) {
                $this->imageCopy($water->image, $x, $y, 0, 0, $waterWidth, $waterHeight);
            } else {
                $this->imageCopyMerge($water->image, $x, $y, 0, 0, $waterWidth, $waterHeight, $alpha);
            }
        } else {
            // 使用水印文字
            $this->tATtf($text['text'], $text['size'], $x, $y, $text['color'], $text['angle'], $this->getTTF($text['ttf']));
        }
        
        $this->saveType($this->info['type'], $file);
        
        return true;
    }
}