<?php
namespace qpf\htmc\css\attr;

/**
 * IE 私有属性
 * @author qiun
 *
 */
class IE extends CssAttr
{
    /**
     * 静态滤镜
     * - 设置元素的不透明度、渐变、模糊、对比度、明度等
     * @var string
     */
    const Visual_Filters = 'filter:progid:DXImageTransform.Microsoft.';
    /**
     * 过渡转场
     * - ie的动画效果
     * @var string
     */
    const Transitions_Reference = '';
    
    /**
     * PNG图片透明实现透明
     * 
     * - ie6的png图片透明的部分会显示灰色背景
     * @param string $enabled 可选值, 是否启用过滤器, 默认`true`
     * @param string $sizingMethod 可选值，设置或检索的方式来显示一个图像在对象边界显示方式。可能的值:
     * - crop : 裁剪图像以适应对象的尺寸；
     * - image : 默认值，扩大或减少对象的边界,以适应图像的尺寸；
     * - scale : 伸展或收缩图像填充对象的边界；
     * @param string $src 必须值，引入图片, 例 mini.jpg
     * @return string
     */
    public function filter_AlphaImageLoader($src, $sizingMethod = 'image', $enabled = 'true')
    {
        return self::Visual_Filters . "AlphaImageLoader('{$enabled}', '{$sizingMethod}', '{$src}')" . self::end;
    }
    
    /**
     * 渐变滤镜
     * @param string $StartColorStr 开始不透明度的梯度值
     * @param string $EndColorStr 结束不透明度的梯度值
     * @param string $GradientType 设置渐变的方向。有两个值，1代表水平方向，0代表垂直方向渐变。
     * @param string $enabled 可选值, 是否启用过滤器, 默认`true`
     * @return string
     */
    public function filter_Gradient($StartColorStr = '#00000000', $EndColorStr = '#FFFF3300', $GradientType = '1' , $enabled = 'ture')
    {
        return self::Visual_Filters . "Gradient('{$enabled}', '{$GradientType}', '{$StartColorStr}', '{$EndColorStr}')" . self::end;
    }
    
    // 未完成
    public function filter_Alpha($enabled = 'ture', $GradientType = '1', $StartColorStr = '#00000000', $EndColorStr = '#FFFF3300')
    {
        return self::Visual_Filters . "Alpha('{$enabled}', '{$GradientType}', '{$StartColorStr}', '{$EndColorStr}')" . self::end;
    }
    
    /**
     * 设置或者检索对象的缩放比例
     * 
     * - 它可以触发ie的haslayout属性，清除浮动，清除margin重叠等作用。 
     * @param string $value 默认1
     * @param string $prefix
     */
    public function zoom($value = 1, $prefix = '*')
    {
        return $prefix . 'zoom' . self::space() . $value . self::end;
    }
}