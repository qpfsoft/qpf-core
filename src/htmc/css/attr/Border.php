<?php
namespace qpf\htmc\css\attr;

/**
 * CSS边框
 *
 * 用于设置盒子的边框 和 表格的边框样式
 * 
 * @author qiun
 *        
 */
class Border extends CssAttr
{

    /**
     * 设置边框 - 四边相同
     *
     * @param string $width
     *            边框的宽度, 边框宽度可能的值:
     *            - thin : 定义细的边框
     *            - medium : 默认。定义中等的边框
     *            - thick : 定义粗的边框
     *            - length : 例`2px`
     * @param string $style
     *            边框的样式, 边框样式可能的值:
     *            - none : 无边框
     *            - hidden : 与 "none" 相同, 对于表，hidden 用于解决边框冲突
     *            - dotted : 点状边框, 在大多数浏览器中呈现为实线
     *            - dashed : 虚线, 在大多数浏览器中呈现为实线
     *            - solid : 实线
     *            - double : 双线。双线的宽度等于 border-width 的值。
     *            - groove : 3D 凹槽边框。其效果取决于 border-color 的值。
     *            - ridge : 3D 垄状边框。
     *            - inset : 3D inset 边框
     *            - outset : 3D outset 边框
     * @param string $color
     *            边框的颜色, 例`#ff0000`
     * @return string
     */
    public function border($width, $style, $color)
    {
        $value = "{$width} {$style} {$color}";
        return 'border' . self::space() . $value . self::end;
    }

    /**
     * 设置边框宽度 - 单独设置
     *
     * 边框宽度可能的值:
     * - thin : 定义细的边框
     * - medium : 默认。定义中等的边框
     * - thick : 定义粗的边框
     * - length : 例`2px`
     * 
     * @param string $top 上边框
     * @param string $right 右边框
     * @param string $bottom 下边框
     * @param string $left 左边框
     * @return string
     */
    public function border_width($top, $right, $bottom, $left)
    {
        $value = $this->parseTRBL($top, $right, $bottom, $left);
        return 'border-width' . self::space() . $value . self::end;
    }

    /**
     * 设置边框样式 - 单独设置
     *
     * 边框样式可能的值:
     * - none : 无边框
     * - hidden : 与 "none" 相同, 对于表，hidden 用于解决边框冲突
     * - dotted : 点状边框, 在大多数浏览器中呈现为实线
     * - dashed : 虚线, 在大多数浏览器中呈现为实线
     * - solid : 实线
     * - double : 双线。双线的宽度等于 border-width 的值。
     * - groove : 3D 凹槽边框。其效果取决于 border-color 的值。
     * - ridge : 3D 垄状边框。
     * - inset : 3D inset 边框
     * - outset : 3D outset 边框
     * 
     * @param string $top 上边框
     * @param string $right 右边框
     * @param string $bottom 下边框
     * @param string $left 左边框
     * @return string
     */
    public function border_style($top, $right, $bottom, $left)
    {
        $value = $this->parseTRBL($top, $right, $bottom, $left);
        return 'border-width' . self::space() . $value . self::end;
    }

    /**
     * 设置边框的颜色 - 单独设置
     *
     * 颜色可能的值:
     * - color_name : 颜色名称的边框颜色, 比如 `red`
     * - hex_number : 十六进制值的边框颜色, 比如 `#ff0000`
     * - rgb_number : rgb 代码的边框颜色, 比如 `rgb(255,0,0)`
     * - transparent : 默认值, 边框颜色为透明
     * 
     * @param string $top 上边框
     * @param string $right 右边框
     * @param string $bottom 下边框
     * @param string $left 左边框
     * @return string
     */
    public function border_color($top, $right, $bottom, $left)
    {
        $value = $this->parseTRBL($top, $right, $bottom, $left);
        return 'border-color' . self::space() . $value . self::end;
    }
    

    /**
     * 设置圆角 - 四角相同
     * 
     * @param string $value 可能的值:
     * - length : `10px`
     * - % : `10%
     * - 水平方向半径 / 垂直方向半径
     * @return string
     */
    public function border_radius($value)
    {
        return 'border-radius' . self::space() . $value . self::end;
    }
    
    /**
     * 圆角 - 3个值
     * @param string $top_left  上左
     * @param string $top_right_bottom_left 上右下左
     * @param string $bottom_right 下右
     */
    public function border_radius3($top_left, $top_right_bottom_left, $bottom_right)
    {
        $value = "{$top_left} {$top_right_bottom_left} {$bottom_right}";
        return 'border-radius' . self::space() . $value . self::end;
    }
    
    /**
     * 圆角 - 4个值
     * @param string $top_left  上左
     * @param string $top_right 上右
     * @param string $bottom_right 下右
     * @param string $bottom_left 下左
     */
    public function border_radius4($top_left, $top_right, $bottom_right, $bottom_left)
    {
        $value = "{$top_left} {$top_right} {$bottom_right} {$bottom_left}";
        return 'border-radius' . self::space() . $value . self::end;
    }
    
    /**
     * 圆角 - 8个值
     * @param string $x_top_left 水平方向, 上左半径
     * @param string $x_top_right 水平方向, 上右半径
     * @param string $x_bottom_right 水平方向, 下右半径
     * @param string $x_bottom_left 水平方向, 下左半径
     * @param string $y_top_left 垂直方向, 上左半径
     * @param string $y_top_right 垂直方向, 上右半径
     * @param string $y_bottom_right 垂直方向, 下右半径
     * @param string $y_bottom_left 垂直方向, 下左半径
     * @return string
     */
    public function border_radius8($x_top_left, $x_top_right, $x_bottom_right, $x_bottom_left, $y_top_left, $y_top_right, $y_bottom_right, $y_bottom_left)
    {
        $value = "$x_top_left $x_top_right $x_bottom_right {$x_bottom_left}/{$y_top_left} $y_top_right $y_bottom_right $y_bottom_left";
        return 'border-radius' . self::space() . $value . self::end;
    }

    /**
     * 设置圆角 - 左上角
     * 
     * @param string $value            
     * @return string
     */
    public function border_radius_top_left($value)
    {
        return 'border-top-left-radius' . self::space() . $value . self::end;
    }

    /**
     * 设置圆角 - 右上角
     * 
     * @param string $value 水平方向和垂直方向的半径
     * - 1个值时： 表示水平和垂直方向相同
     * - 2个值时： 第二个值代表垂直方向的半径
     * @return string
     */
    public function border_radius_top_right($value)
    {
        return 'border-top-right-radius' . self::space() . $value . self::end;
    }

    /**
     * 设置圆角 - 左下角
     * 
     * @param string $value            
     * @return string
     */
    public function border_radius_bottom_left($value)
    {
        return 'border-bottom-left-radius' . self::space() . $value . self::end;
    }

    /**
     * 设置圆角 - 右下角
     * 
     * @param string $value            
     * @return string
     */
    public function border_radius_bottom_right($value)
    {
        return 'border-bottom-right-radius' . self::space() . $value . self::end;
    }

    /**
     * 表格单元格 - 边框合并
     * 
     * @param string $value
     *            可能的值:
     *            - separate : 默认值。边框会被分开。不会忽略 border-spacing 和 empty-cells 属性。
     *            - collapse : 合并为一个单一的边框。会忽略 border-spacing 和 empty-cells 属性。
     * @return string
     */
    public function border_collapse($value)
    {
        return 'border-collapse' . self::space() . $value . self::end;
    }

    /**
     * 表格单元格 - 间距大小
     * 
     * @param string $length1
     *            水平间距, 例`5px`
     * @param string $length2
     *            垂直间距, 可选, 未设置时与水平间距相同.
     * @return string
     */
    public function border_spacing($length1, $length2 = null)
    {
        if (is_null($length2)) {
            $value = $length1;
        } elseif ($length1 == $length2) {
            $value = $length1;
        } else {
            $value = $length1 . ' ' . $length2;
        }
        
        return 'border-spacing' . self::space() . $value . self::end;
    }

    /**
     * 对象外的线条轮廓
     *
     * - 不占据布局空间，不会影响元素的尺寸；
     * - 可能是非矩形
     * - 在边框的外面
     * - 只有ie6和ie7不支持
     * - 当鼠标悬停在链接上，或者点击过的链接，颜色会被设置为 #2a6496
     * - 获得焦点 {outline: thin dotted #333} 默认样式;
     * - webkit游览器 {outline: 5px auto -webkit-focus-ring-color;outline-offset: -2px;}
     * 
     * @param string $width 轮廓边框的宽度, 可能的值：
     * - <length>：用长度值来定义轮廓的厚度。不允许负值
     * - medium ：定义默认宽度的轮廓。
     * - thin ：定义比默认宽度细的轮廓。
     * - thick ：定义比默认宽度粗的轮廓。
     * @param string $style 轮廓边框的样式, 可能的值：
     * - none：无轮廓。与任何指定的 <' outline-width '> 值无关
     * - auto : 自动
     * - dotted：点状轮廓。
     * - dashed：虚线轮廓。
     * - solid：实线轮廓
     * - double：双线轮廓。两条单线与其间隔的和等于指定的 <' outline-width '> 值
     * - groove：3D凹槽轮廓。
     * - ridge：3D凸槽轮廓。
     * - inset：3D凹边轮廓。
     * - outset：3D凸边轮廓。
     * @param string $color 轮廓边框的颜色, 可能的值：
     * - <color>：指定颜色
     * - invert：使用背景色的反色。
     * - `-webkit-focus-ring-color` : webkit游览器的颜色
     * @return string
     */
    public function outline($width, $style, $color)
    {
        $value = $width . ' ' . $style . ' ' . $color;
        return 'outline' . self::space() . $value . self::end;
    }
    
    /**
     * 去除游览器a链接的外框
     * 
     * - 链接虚框, < a >点击链接文字或图片
     * @return string
     */
    public function outline_none()
    {
        return 'outline' . self::space() . '0' . self::end;
    }

    /**
     * 轮廓边框的宽度
     * 
     * @param string $value 可能的值：
     * - <length>：用长度值来定义轮廓的厚度。不允许负值
     * - medium ：定义默认宽度的轮廓。
     * - thin ：定义比默认宽度细的轮廓。
     * - thick ：定义比默认宽度粗的轮廓。
     * @return string
     */
    public function outline_width($value)
    {
        return 'outline-width' . self::space() . $value . self::end;
    }

    /**
     * 轮廓边框的样式
     * 
     * @param string $value 可能的值：
     * - none：无轮廓。与任何指定的 <' outline-width '> 值无关
     * - dotted：点状轮廓。
     * - dashed：虚线轮廓。
     * - solid：实线轮廓
     * - double：双线轮廓。两条单线与其间隔的和等于指定的 <' outline-width '> 值
     * - groove：3D凹槽轮廓。
     * - ridge：3D凸槽轮廓。
     * - inset：3D凹边轮廓。
     * - outset：3D凸边轮廓。
     * @return string
     */
    public function outline_style($value)
    {
        return 'outline-style' . self::space() . $value . self::end;
    }

    /**
     * 轮廓边框的样式
     * 
     * @param string $value 可能的值：
     * - <color>：指定颜色
     * - invert：使用背景色的反色。
     * @return string
     */
    public function outline_color($value)
    {
        return 'outline-color' . self::space() . $value . self::end;
    }
    
    /**
     * 线条轮廓偏移容器的值
     * @param string $value 用长度值来定义轮廓偏移。允许负值。
     * @return string
     */
    public function outline_offset($value)
    {
        return 'outline-offset' . self::space() . $value . self::end;
    }
}