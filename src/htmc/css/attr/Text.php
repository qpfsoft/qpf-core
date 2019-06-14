<?php
namespace qpf\htmc\css\attr;

/**
 * CSS文本样式
 * 
 * @author qiun
 *        
 */
class Text extends CssAttr
{

    /**
     * 设置文本颜色
     * 
     * @param string $value
     *            可能的值:
     *            - red : 颜色名称
     *            - #ff0000 : 十六进制值的颜色
     *            - rgb(255,0,0) : rgb 代码的颜色
     * @return string
     */
    public function color($value)
    {
        return 'color' . self::space() . $value . self::end;
    }
    
    /**
     * 设置文本属性 - 复合
     * 
     * - 简写时，font-size和line-height只能通过斜杠/组成一个值，不能分开写。
     * - 顺序不能改变.这种简写方法只有在同时指定font-size和font-family属性时才起作用。
     * 而且，如果你没有设定font-weight, font-style, 以及 font-varient ，他们会使用缺省值
     * 
     * @param string $style 字体样式, 可选值:
     * - normal : 默认值。浏览器显示一个标准的字体样式。
     * - italic : 斜体的字体样式
     * - oblique: 倾斜的字体样式
     * @param string $variant 是否为小型的大写字母, 可选值:
     * - normal : 默认值
     * - small-caps : 显示小型大写字母的字体
     * @param string $weight 文本粗细, 可选值:
     * - normal : 默认值, 即 `400`
     * - bold : 粗体字符, 即 `700`
     * - bolder : 更粗的字符
     * - lighter: 更细的字符
     * - <int> : 取值范围：100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900
     * @param string $size 字体大小, 可选值:
     * - smaller : 比父元素更小的尺寸
     * - larger : 比父元素更大的尺寸
     * - length : 14px
     * - % : 100%为正常 > 200%变大
     * @param string $height 字体的行高
     * @param string $family 字体类型字符串描述, 用逗号分割
     */
    public function font($style, $variant, $weight, $size, $height, $family)
    {
        if (!empty($size) && !empty($height)) {
            $size = $size.'/'.$height;
            $height = null;
        }
        $value = $this->mergeAttr($style, $variant, $weight, $size, $height, $family);
        return 'font' . self::space() . $value . self::end;
    }
    
    /**
     * 设置文本字体系列
     * - 建议先英文后中文
     * 
     * @param string $value 字体
     * @return string
     */
    public function font_family($value)
    {
        return 'font-family' . self::space() . $value . self::end;
    }

    /**
     * 设置文本字体样式 - 倾斜
     * 
     * @param string $value 可选值:
     * - normal : 默认值。浏览器显示一个标准的字体样式。
     * - italic : 斜体的字体样式
     * - oblique: 倾斜的字体样式
     * @return string
     */
    public function font_style($value)
    {
        return 'font-style' . self::space() . $value . self::end;
    }

    /**
     * 设置文本英文为小型大小写
     * - 英文整体全部缩小
     * 
     * @param string $value 可选值:
     * - normal : 默认值
     * - small-caps : 显示小型大写字母的字体
     * @return string
     */
    public function font_variant($value)
    {
        return 'font-variant' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的粗细
     * 
     * @param string $value 可选值:
     * - normal : 默认值, 即 `400`
     * - bold : 粗体字符, 即 `700`
     * - bolder : 更粗的字符
     * - lighter: 更细的字符
     * - <int> : 取值范围：100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900
     * @return string
     */
    public function font_weight($value)
    {
        return 'font-weight' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的大小
     * 
     * @param string $value 可选值:
     * - smaller : 比父元素更小的尺寸
     * - larger : 比父元素更大的尺寸
     * - length : 14px
     * - % : 100%为正常 > 200%变大
     * @return string
     */
    public function font_size($value)
    {
        return 'font-size' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的行高 - 垂直居中
     * 
     * @param string $value
     *            可能的值:
     *            - 1 | 0.5 : 数字, 此数字会与当前的字体尺寸相乘来设置行间距
     *            - 20px : 像素设置行高
     *            - 120% : 百分比, 在大多数浏览器中默认行高大约是 110% 到 120%
     * @return string
     */
    public function line_height($value)
    {
        return 'line-height' . self::space() . $value . self::end;
    }

    /**
     * 设置文本 - 水平对齐
     * 
     * @param string $value
     *            可选值:
     *            - left : 左对齐
     *            - right : 右对齐
     *            - center : 居中
     *            - justify: 两端对齐, 即增加字间距让一段文本看起来是个方形
     * @return string
     */
    public function text_align($value)
    {
        return 'text-align' . self::space() . $value . self::end;
    }

    /**
     * 设置文本水平 - 左边对齐
     * 
     * @return string
     */
    public function text_align_left()
    {
        return 'text-align' . self::space() . 'left' . self::end;
    }

    /**
     * 设置文本水平 - 右边对齐
     * 
     * @return string
     */
    public function text_align_right()
    {
        return 'text-align' . self::space() . 'right' . self::end;
    }

    /**
     * 设置文本水平 - 中间对齐
     * 
     * @return string
     */
    public function text_align_center()
    {
        return 'text-align' . self::space() . 'center' . self::end;
    }

    /**
     * 设置文本水平 - 两端对齐
     * 
     * @return string
     */
    public function text_align_justify()
    {
        return 'text-align' . self::space() . 'justify' . self::end;
    }

    /**
     * 设置文本的装饰 - 下划线 - css3
     * 
     * @param string $value
     *            可选值:
     *            - none : 不需要装饰
     *            - underline : 下划线
     *            - overline : 上划线
     *            - line-through : 删除线
     *            - blink : 闪烁的文本 (游览器不支持)
     * @return string
     */
    public function text_decoration($value)
    {
        return 'text-decoration' . self::space() . $value . self::end;
    }

    /**
     * 设置文本无装饰效果
     * 
     * @return string
     */
    public function text_decoration_none()
    {
        return 'text-decoration' . self::space() . 'none' . self::end;
    }

    /**
     * 设置文本的下划线
     * 
     * @return string
     */
    public function text_decoration_underline()
    {
        return 'text-decoration' . self::space() . 'underline' . self::end;
    }

    /**
     * 设置文本的缩进 - 整段文本缩进
     * 
     * @param string $value
     *            例如`25px`
     * @return string
     */
    public function line_indent($value)
    {
        return 'text-indent' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的阴影效果 - css3
     *
     * -推荐的阴影: 5px 5px 10px black
     * 
     * @param string $x 必需, 水平阴影的位置, 允许负值, 例`10px`
     * @param string $y 必需, 垂直阴影的位置, 允许负值, 不设置时为`0px`
     * @param string $blur 可选, 模糊的距离。
     * @param string $color 可选, 阴影的颜色
     * @return string
     */
    public function text_shadow($x, $y, $blur = null, $color = null)
    {
        $value = $x . ' ' . $y;
        
        $value .= is_null($blur) ? '' : ' ' . $blur;
        
        $value .= is_null($color) ? '' : ' ' . $color;
        
        return 'text-shadow' . self::space() . $value . self::end;
    }

    /**
     * 设置文本 - 英文大小写 - css3
     * 
     * @param string $val
     *            可能的值:
     *            - none : 默认
     *            - capitalize : Ab 首字母大写
     *            - uppercase : AB 全部大写
     *            - lowercase : ab 全部小写
     */
    public function text_transform($val)
    {
        return 'text-transform' . self::space() . $val . self::end;
    }

    /**
     * 设置英文 - 首字母大写
     * 
     * @return string
     */
    public function text_transform_Aaa()
    {
        return 'text-transform' . self::space() . 'capitalize' . self::end;
    }

    /**
     * 设置英文 - 大写
     * 
     * @return string
     */
    public function text_transform_BBB()
    {
        return 'text-transform' . self::space() . 'uppercase' . self::end;
    }

    /**
     * 设置英文 - 小写
     * 
     * @return string
     */
    public function text_transform_ccc()
    {
        return 'text-transform' . self::space() . 'lowercase' . self::end;
    }

    /**
     * 设置文本 - 字间距 - css3
     *
     * 字间距: this is text.
     *
     * @param string $val
     *            可能的值:
     *            - 2px | -2px | -0.5em
     * @return string
     */
    public function letter_spacing($val)
    {
        return 'letter-spacing' . self::space() . $val . self::end;
    }

    /**
     * 设置英文单词 - 单词字母间距 - css3
     *
     * 单词间距: t h i s i s t e x t.
     *
     * @param string $val
     *            可能的值:
     *            - 25px | -0.5em |
     * @return string
     */
    public function word_spacing($val)
    {
        return 'word-spacing' . self::space() . $val . self::end;
    }

    /**
     * 设置段落 - 换行
     * 
     * @param string $val
     *            可能的值:
     *            - normal : 默认。空白会被浏览器忽略。
     *            - pre : 空白会被浏览器保留。其行为方式类似 HTML 中的 <pre> 标签。
     *            - nowrap : 文本不会换行，文本会在在同一行上继续，直到遇到 <br> 标签为止。
     *            - pre-wrap : 保留空白符序列，但是正常地进行换行。
     *            - pre-line : 合并空白符序列，但是保留换行符。
     * @return string
     */
    public function white_space($val)
    {
        return 'white-space' . self::space() . $val . self::end;
    }

    /**
     * 禁止文本换行
     * 
     * @return string
     */
    public function white_space_nowrap()
    {
        return 'white-space' . self::space() . 'nowrap' . self::end;
    }

    /**
     * 垂直对齐设置
     * 
     * @param string $value  可选值:
     * - baseline : 默认。在父元素的基线上
     * - middle : 把此元素放置在父元素的中部。
     * - sub : 下标, 垂直对齐文本的下标
     * - super : 上标, 垂直对齐文本的上标
     * - top : 最高元素对齐 ,把元素的顶端与行中最高元素的顶端对齐
     * - text-top : 最高文本对齐 ,把元素的顶端与父元素字体的顶端对齐
     * - bottom : 最低元素对齐, 把元素的顶端与行中最低的元素的顶端对齐。
     * - text-bottom : 最低文本对齐 ,把元素的底端与父元素字体的底端对齐。
     * - length : `10px` , `100%`
     */
    public function vertical_align($value)
    {
        return 'vertical-align' . self::space() . $value . self::end;
    }
    
    /**
     * 垂直居中 - 相对于父元素
     */
    public function vertical_align_middle()
    {
        return 'vertical-align' . self::space() . 'middle' . self::end;
    }
    
    /**
     * 设置列表样式
     * @param string $type 符号类型
     * @param string $position 符号绘制位置, inside(内0), outside(外1,默认)
     * @param string $image 图形符号,图片地址
     */
    public function list_style($type, $position = 1, $image = null)
    {
        $position = $position ? 'outside' : 'inside';
        $image = is_null($image) ? 'none' : $this->parseUrl($image);
        
        $value = "$type $position $image";
        return 'list-style' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的项目符号 - 绘制位置
     * 
     * @param string $value 可能的值:
     * - inside : 内,列表项目标记放置在文本以内，且环绕文本根据标记对齐。
     * - outside : 外,默认值。保持标记位于文本的左侧。列表项目标记放置在文本以外
     * @return string
     */
    public function list_style_position($value)
    {
        return 'list-style-position' . self::space() . $value . self::end;
    }

    /**
     * 设置文本的项目符号 - 图片符号
     * 
     * @param string $value 图片路径
     * @return string
     */
    public function list_style_image($value)
    {
        return 'list-style-image' . self::space() . $this->parseUrl($value) . self::end;
    }

    /**
     * 设置文本的项目符号 - 符号类型
     * 
     * @param string $value 可能的值:
     * - none : 无标记
     * - disc : 默认。标记是实心圆。
     * - circle : 空心圆
     * - square : 实心方块
     * - decimal : 数字
     * - decimal-leading-zero : 数字, 前面带0
     * - lower-roman : 小写罗马数字(i, ii, iii, iv, v, 等。)
     * - upper-roman : 大写罗马数字(I, II, III, IV, V, 等。)
     * - lower-alpha : 小写英文字母
     * - upper-alpha : 大写英文字母
     * @return string
     */
    public function list_style_type($value)
    {
        return 'list-style-type' . self::space() . $value . self::end;
    }

    // ============================其他======================================//
    
    /**
     * 设置文本块首行的缩进 - css3
     * 
     * @param string $val可能的值:
     * - 50px | 1cm : 固定的缩进
     * - 10% : 基于父元素宽度的百分比的缩进
     */
    public function text_indent($val)
    {
        return 'text-indent' . self::space() . $val . self::end;
    }

    /**
     * 设置文本的书写方向 - 书写方向
     *
     * 注意:
     * 从右开始书写, 标点符号位置会变到文本前面.
     *
     * @param string $val可选值:
     * - ltr : 默认。文本方向从左到右
     * - rtl : 文本方向从右到左
     * @return string
     */
    public function direction($val)
    {
        return 'direction' . self::space() . $val . self::end;
    }

    /**
     * 设置文本左对齐, 文本方向从左到右
     * 
     * @return string
     */
    public function direction_left()
    {
        return 'direction' . self::space() . 'ltr' . self::end;
    }

    /**
     * 设置文本右对齐, 文本方向从右到左
     * 
     * @return string
     */
    public function direction_right()
    {
        return 'direction' . self::space() . 'rtl' . self::end;
    }

    /**
     * 设置文本方向 - css3
     *
     * 注解:
     * 对于阿拉伯语只设置direction就能正确展示了，
     * 但是英文单词却只有右对齐效果，不会从右到左书写，
     * 只有设置了unicode-bidi:bidi-override;才好使呢
     *
     * @param string $val
     *            可能的值:
     *            - normal : 默认。原来是什么顺序就使用什么顺序
     *            - embed : 作用于inline元素，direction属性的值指定嵌入层，在对象内部进行隐式重排序
     *            - bidi-override : 严格按照direction属性的值重排序。忽略隐式双向运算规则
     * @return string
     */
    public function unicode_bidi($val)
    {
        return 'unicode-bidi' . self::space() . $val . self::end;
    }

    /**
     * 规定非中日韩文本的换行规则
     * 
     * @param string $val
     *            可能的值:
     *            - normal : 使用浏览器默认的换行规则
     *            - break-all : 允许在单词内换行
     *            - keep-all : 只能在半角空格或连字符处换行
     */
    public function word_break($val)
    {
        return 'word-break' . self::space() . $val . self::end;
    }

    /**
     * 允许长单词或 URL 地址换行到下一行 - css3
     * 
     * @param string $val
     *            可能的值:
     *            - normal : 只在允许的断字点换行
     *            - break-word : 在长单词或 URL 地址内部进行换行
     * @return string
     */
    public function word_wrap($val = 'break-word')
    {
        return 'word-wrap' . self::space() . $val . self::end;
    }

    /**
     * 设置文本溢出处理 - css3
     *
     * - 可通过:hover来制作鼠标悬停时显示出全部.
     *
     * @param string $val
     *            可能的值:
     *            - clip : 修剪文本, 即 溢出不显示
     *            - ellipsis : 显示省略符号来代表被修剪的文本, 即 `...`
     *            - string : 使用给定的字符串来代表被修剪的文本.
     */
    public function text_overflow($val)
    {
        return 'text-overflow' . self::space() . $val . self::end;
    }
}