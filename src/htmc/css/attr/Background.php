<?php
namespace qpf\htmc\css\attr;

/**
 * CSS背景样式
 * 
 * @author qiun
 *        
 */
class Background extends CssAttr
{

    /**
     * 背景颜色
     * 
     * @param string $value
     *            可能的值:
     *            - color_name : 颜色名称的边框颜色, 比如 `red`
     *            - hex_number : 十六进制值的边框颜色, 比如 `#ff0000`
     *            - rgb_number : rgb 代码的边框颜色, 比如 `rgb(255,0,0)`
     *            - transparent : 默认值, 边框颜色为透明
     * @return string;
     */
    public function background_color($value)
    {
        return 'background-color' . self::space() . $value . self::end;
    }

    /**
     * 图片背景
     * 
     * @param string $value 图像的路径, 前缀`url:`将自动包裹值
     * @return string;
     */
    public function background_image($value)
    {
        // `url:http://img.png` => 'url(http://img.png)'
        if (stripos($value, 'url:') !== false) {
            $value = $this->parseUrl(substr($value, 4));
        }
        
        return 'background-image' . self::space() . $value . self::end;
    }
    
    /**
     * 渐变背景
     *
     * - 使用过时的语法：-webkit-gradient(linear,…)
     * - IE6.0-9.0使用私有滤镜来实现该效果: progid:DXImageTransform.Microsoft.Gradient()
     * @param string $angle 渐变角度, 可能的值:
     * - `to bottom` : 从上到下, 相当于: 180deg。这是默认值，等同于留空不写。
     * - `to top` : 从下到上, 相当于: 0deg
     * - `to right` : 从左到右, 相当于: 90deg
     * - `to left` : 从右到左, 相当于: 270deg
     * @param string $colorStart 开始颜色
     * @param string $colorEnd 结束颜色
     * @return string
     */
    public function background_image_linear_gradient($colorStart, $colorEnd, $angle = '180deg')
    {
        $value = "linear-gradient({$angle}, {$colorStart}, {$colorEnd})";
        $css = 'background-image' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-', '-ms-', '-o-'], true);
    }

    /**
     * 图片背景定位
     *
     * 可能的值:
     * - top bottom center left right: 只规定了一个关键词，那么第二个值将是"center"。
     * - x% y% : 左上角是 0% 0%。右下角是 100% 100%。
     * - xpos ypos : 左上角是 0 0。单位是像素 (0px 0px),仅规定了一个值，另一个值将是50%。
     * 
     * @param string $x
     *            水平位置, 可正可负
     * @param string $y
     *            垂直位置
     * @return string
     */
    public function background_position($x, $y)
    {
        $value = $x . ' ' . $y;
        return 'background-position' . self::space() . $value . self::end;
    }

    /**
     * 背景图片的大小
     *
     * 可能的值:
     * - length : `100px 100px` 宽度和高度,只设置一个值.则第二个值会被设置为 "auto"。即`100px`
     * - % : 以百分比的方式 , 即 `50%` , `100% 100%`
     * - cover : 把背景图像扩展至足够大，以使背景图像完全覆盖背景区域。 - 完全覆盖区域,但图片会超出隐藏
     * - contain : 把图像图像扩展至最大尺寸，以使其宽度和高度完全适应内容区域。 - 在范围内最多的显示.
     * 
     * @param string $width
     *            宽度
     * @param string $height
     *            高度
     * @return string
     */
    public function background_size($width, $height = null)
    {
        if (is_null($height)) {
            $value = $width;
        } else {
            $value = $width . ' ' . $height;
        }
        
        return 'background-size' . self::space() . $value . self::end;
    }

    /**
     * 背景图片的绘制区域 - 盒子的填充,边框,内容区域
     * 
     * @param string $value
     *            可能的值:
     *            - border-box : 背景被裁剪到边框盒, 图片从边框的位置开始显示, 即边框会覆盖背景图片.
     *            - padding-box : 背景被裁剪到内边距框, 在边框内开始显示图片.
     *            - content-box : 背景被裁剪到内容框, 有内填充的以外的内容部分显示背景.
     * @return string
     */
    public function background_clip($value)
    {
        return 'background-clip' . self::space() . $value . self::end;
    }

    /**
     * 背景图片 - 重复平铺显示
     * 
     * @param string $value
     *            可能的值:
     *            - repeat : 默认,背景图像将在垂直方向和水平方向重复
     *            - repeat-x : 在水平方向重复
     *            - repeat-y : 在垂直方向重复
     *            - no-repeat : 仅显示一次
     * @return string
     */
    public function background_repeat($value)
    {
        return 'background-repeat' . self::space() . $value . self::end;
    }

    /**
     * 背景图片定位 - 相对盒子的什么区域开始
     * 
     * @param string $value
     *            可能的值:
     *            - padding-box : 背景图像相对于内边距框来定位, 边框内
     *            - border-box : 背景图像相对于边框盒来定位, 边框的位置开始, 即边框会覆盖图片
     *            - content-box : 背景图像相对于内容框来定位, 盒子内容区域显示, 不包含内填充.
     * @return string
     */
    public function background_origin($value)
    {
        return 'background-origin' . self::space() . $value . self::end;
    }

    /**
     * 背景图像 - 固定或滚动
     * 
     * @param string $value
     *            可能的值:
     *            - scroll : 默认值。背景图像会随着页面其余部分的滚动而移动
     *            - fixed : 当页面的其余部分滚动时，背景图像不会移动
     * @return string
     */
    public function background_attachment($value)
    {
        return 'background-attachment' . self::space() . $value . self::end;
    }

    /**
     * 背景阴影
     * 
     * @param string $x 必需, 水平阴影的位置, 允许负值。
     * @param string $y 必需, 垂直阴影的位置, 允许负值。
     * @param string $blur 可选, 模糊距离。 `1px`
     * @param string $spread 可选, 阴影的尺寸。`1px`
     * @param string $color 可选, 阴影的颜色。`rgba(0, 0, 0, 0.2)`
     * @param string $inset 可选, 将外部阴影 (outset/inset) 改为内部阴影。默认`outset`
     * @return string
     */
    public function box_shadow($x, $y, $blur = null, $spread = null, $color = null, $inset = null)
    {
        $value =  $this->mergeAttr($x, $y, $blur, $spread, $color, $inset);
        return 'box-shadow' . self::space() . $value . self::end;
    }
    
    /**
     * 生产rgba()颜色属性值
     * 
     * @param integer $red 红, 0-255
     * @param integer $green 黄, 0-255
     * @param integer $blue  蓝, 0-255
     * @param float $alpha 透明度, 范围从0-1, 
     * @return string
     */
    public function rgba($red, $green, $blue, $alpha)
    {
        return "rgba($red, $green, $blue, $blue)";
    }
}