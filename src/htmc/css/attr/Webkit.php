<?php
namespace qpf\htmc\css\attr;

/**
 * Webkit私有属性
 * -----------------------
 * 可用于: safari、chrome、Opera、iOS Safari、Android Browser、Android Chrome
 * 不兼容: IE, Firefox
 * 
 * @author qiun
 *        
 */
class Webkit extends CssAttr
{

    /**
     * 文字内部填充颜色
     * 
     * @param string $color
     *            指定文字的填充颜色.
     *            - transparent : 默认值
     * @return string
     */
    public function text_fill_color($color)
    {
        return '-webkit-text-fill-color' . self::space() . $color . self::end;
    }

    /**
     * 文字描边
     *
     * @param string $width
     *            描边厚度, 例`1px`
     * @param string $color
     *            描边颜色, 例`#f00`
     * @return string
     */
    public function text_stroke($width, $color)
    {
        $value = $width . ' ' . $color;
        return '-webkit-text-stroke' . self::space() . $value . self::end;
    }

    /**
     * 文字的描边厚度
     * 
     * @param string $value
     *            描边厚度, 例`1px`,不允许负值
     * @return string
     */
    public function text_stroke_width($value)
    {
        return '-webkit-text-stroke-width' . self::space() . $value . self::end;
    }

    /**
     * 文字的描边颜色
     * 
     * @param string $value
     *            描边颜色, 例`#f00`
     * @return string
     */
    public function text_stroke_color($value)
    {
        return '-webkit-text-stroke-color' . self::space() . $value . self::end;
    }

    /**
     * 倒影特效
     * 
     * @param string $value
     *            可能的值:
     *            - none：无倒影
     *            参数1 > 方向:
     *            - above : 指定倒影在对象的上边
     *            - below : 指定倒影在对象的下边
     *            - left : 指定倒影在对象的左边
     *            - right : 指定倒影在对象的右边
     *            参数2 > 图片与倒影之间的间隔:
     *            - 百分比 或 xp , 可以为负值
     *            参数3 > 倒影遮罩效果:
     *            - none : 无遮罩图像
     *            - url() : 遮罩图片
     *            - 渐变 : 线性, 放射,重复线性,重复放射
     * @return string
     */
    public function box_reflect($value)
    {
        return '-webkit-box-reflect' . self::space() . $value . self::end;
    }

    /**
     * 轻按时高亮
     *
     * 支持: ios 和 Android, 只有移动端有效
     * 当用户轻按一个链接或者JavaScript可点击元素时给元素覆盖一个高亮色
     * 
     * @param string $value
     *            颜色
     * @return string
     */
    public function tap_highlight_color($value)
    {
        return '-webkit-tap-highlight-color' . self::space() . $value . self::end;
    }

    /**
     * 元素可以被拖拽，而不它的内容
     *
     * 支持: Chrome, Safari, Opera , 移动端不支持
     * 
     * @param string $value
     *            可能的值:
     *            - auto : 默认值, 使用默认的拖拽行为，这种情况只有图片和链接可以被拖拽。
     *            - element : 整个元素而非它的内容可拖拽。
     *            - none : 元素不能被拖动。在通过选中后可拖拽
     * @return string
     */
    public function user_drag($value)
    {
        return '-webkit-user-drag' . self::space() . $value . self::end;
    }
}