<?php
namespace qpf\htmc\css\attr;

/**
 * CSS3 新特性
 * 
 * @author qiun
 *        
 */
class Css3 extends CssAttr
{

    /**
     * 安装服务端字体
     *
     * Font-face 可以用来加载字体样式，而且它还能够加载服务器端的字体文件，
     * 让客户端显示客户端所没有安装的字体。
     *
     * ~~~客户端
     * <p><font face="arial">arial courier verdana</font></p>
     * <p><font style="font-family: arial">arial courier verdana</font></p>
     * ~~~服务端
     * @font-face {
     * font-family: BorderWeb;
     * src:url(BORDERW0.eot);
     * }
     * ~~~使用
     * .border { FONT-SIZE: 35px; COLOR: black; FONT-FAMILY: "BorderWeb" }
     * ~~~
     *
     * @param string $font_name
     *            字体名称
     * @param string $font_url
     *            字体.eot位置
     * @return string
     */
    public function font_face($font_name, $font_url)
    {
        $value = 'font-family' . self::space() . $font_name . self::end;
        $value .= 'src' . self::space() . $this->parseUrl($font_url) . self::end;
        $css = '@font-face{' . $value . '}';
        return $css;
    }

    /**
     * 生成线性渐变颜色 - 颜色属性值
     *
     * 实例:
     * ~~~
     * background: linear-gradient(#fff, #333);
     * linear-gradient(#000, #f00 50%, #090);
     * linear-gradient(0deg, #000 20%, #f00 50%, #090 80%);
     * linear-gradient(45deg, #000, #f00 50%, #090);
     * linear-gradient(to top right, #000, #f00 50%, #090);
     * ~~~
     * 
     * @param string $value
     *            可能的值:
     *            参数1 > 角度(可选)
     *            - `to left` : 设置渐变为从右到左。相当于: 270deg
     *            - `to right` : 设置渐变从左到右。相当于: 90deg
     *            - `to top` : 设置渐变从下到上。相当于: 0deg
     *            - `to bottom` : 设置渐变从上到下。相当于: 180deg。这是默认值，等同于留空不写。
     *            注意: 可自由搭配角度, 例`to top right`
     *            参数2 > 渐变的起止颜色
     *            - 可设置多个颜色:
     *            - 参数1 > color : 必选, 指定颜色, 例`#fff`
     *            - 参数2 > length : 可选, 用长度值指定起止色位置。不允许负值,
     *            - 参数3 > 百分比 : 可选, 用百分比指定起止色位置, 例`0% ~ 100%`
     * @return string
     */
    public function linear_gradient($value)
    {
        return 'linear-gradient(' . $value . ')';
    }

    /**
     * 生成放射状渐变 - 颜色属性值
     *
     * 实例
     * ~~~
     * # 只给出100px，所以被当成是正圆的半径，于是就能确定一个直径为100px的圆；
     * radial-gradient(100px, #f00, #ff0, #080);
     * # 给出了2个值，按理应该是要画一个椭圆的，但2个值相等，所以这个椭圆其实此时是个正圆形态。
     * radial-gradient(100px 100px, #f00, #ff0, #080);
     * # 表示了一个水平半径为50px，垂直半径为100px的椭圆
     * radial-gradient(50px 100px, #f00, #ff0, #080);
     *
     * 格式:
     * radial-gradient(水平半径 垂直半径, 颜色1, 颜色2, 颜色3);
     * 参考:
     * radial-gradient(circle at center, #f00, #ff0, #080);
     * radial-gradient(circle closest-side, #f00, #ff0, #080);
     * radial-gradient(farthest-side, #f00 20%, #ff0 50%, #080 80%);
     * radial-gradient(at top right, #f00, #ff0, #080);
     * radial-gradient(farthest-side at top right, #f00, #ff0, #080);
     * 可用逗号来实现多个放射颜色:
     * background: radial-gradient(),radial-gradient();
     * ~~~
     *
     * @param string $value
     *            可能的值:
     *            参数1 > 确定圆心位置:
     *            如果提供2个参数，第一个表示横坐标，第二个表示纵坐标；如果只提供一个，第二值默认为50%，即center
     *            圆心参数1
     *            - 百分比 : 径向渐变圆心的横坐标值。可以为负值
     *            - lenght : 100px
     *            - left : 设置左边为径向渐变圆心的横坐标值
     *            - center : 设置中间为径向渐变圆心的横坐标值
     *            - right : 设置右边为径向渐变圆心的横坐标值
     *            圆心参数2:
     *            - lenght : 100px
     *            - 百分比 : 径向渐变圆心的横坐标值。可以为负值
     *            - center : 设置中间为径向渐变圆心的横坐标值
     *            - bottom : 设置底部为径向渐变圆心的纵坐标值
     *            参数2 > 圆的类型:
     *            - circle : 指定圆形的径向渐变
     *            - ellipse : 指定椭圆形的径向渐变
     *            参数3 >
     *            - closest-side : 指定径向渐变的半径长度为从圆心到离圆心最近的边
     *            - closest-corner : 指定径向渐变的半径长度为从圆心到离圆心最近的角
     *            - farthest-side : 指定径向渐变的半径长度为从圆心到离圆心最远的边
     *            - farthest-corner : 指定径向渐变的半径长度为从圆心到离圆心最远的角
     * @return string
     */
    public function radial_gradient($value)
    {
        return 'radial-gradient(' . $value . ')';
    }

    /**
     * 定位对象剪切 - css
     *
     * - 示例：clip:rect(auto 50px 20px auto)
     * - 必须将position的值设为absolute或者fixed，此属性方可使用
     * - 这个属性将被废弃，推荐使用 clip-path, 目前还有效果
     * - 上-右-下-左的顺序提供自对象左上角为(0,0)坐标计算的四个偏移数值，
     * 其中任一数值都可用auto替换，即此边不剪切。
     * 
     * @param string $value            
     * @return string
     */
    public function clip($value)
    {
        return 'clip' . self::space() . $value . self::end;
    }

    /**
     * 文字是否横向拉伸变形 - css3 - 支持性差
     *
     * - 文字的拉伸是相对于浏览器显示的字体的正常宽度
     * 
     * @param string $value
     *            - normal：正常文字宽度
     *            - ultra-condensed：比正常文字宽度窄4个基数。
     *            - extra-condensed：比正常文字宽度窄3个基数。
     *            - condensed：比正常文字宽度窄2个基数。
     *            - semi-condensed：比正常文字宽度窄1个基数。
     *            - semi-expanded：比正常文字宽度宽1个基数。
     *            - expanded：比正常文字宽度宽2个基数。
     *            - extra-expanded：比正常文字宽度宽3个基数。
     *            - ultra-expanded：比正常文字宽度宽4个基数。
     */
    public function font_stretch($value)
    {
        return 'font-stretch' . self::space() . $value . self::end;
    }

    /**
     * 设置或检索对象的 aspect 值，用以保持首选字体的 x-height
     * 
     * @param string $value
     *            可能的值：
     *            - none ： 不保留首选字体的 x-height
     *            - number ：定义字体的 aspect 值
     * @return string
     */
    public function font_size_adjust($value)
    {
        return 'font-size-adjust' . self::space() . $value . self::end;
    }

    /**
     * 检索或设置对象中的制表符的长度
     * 
     * @param string $value            
     * @return string
     */
    public function tab_size($value)
    {
        return 'tab—size' . self::space() . $value . self::end;
    }

    /**
     * 检索或设置移动端页面中对象文本的大小调整
     * 
     * @param string $value            
     * @return string
     */
    public function text_size_adjust($value)
    {
        return 'text-size-adjust' . self::space() . $value . self::end;
    }

    /**
     * 文本装饰线条的位置
     * 
     * @param string $value
     *            可能的值：
     *            - none：指定文字无装饰
     *            - underline：指定文字的装饰是下划线
     *            - overline：指定文字的装饰是上划线
     *            - line-through：指定文字的装饰是贯穿线
     *            - blink：指定文字的装饰是闪烁。
     * @return string
     */
    public function text_decoration_line($value)
    {
        return 'text-decoration-line' . self::space() . $value . self::end;
    }

    /**
     * 文本装饰线条的颜色
     * 
     * @param string $value
     *            指定颜色
     * @return string
     */
    public function text_decoration_color($value)
    {
        return 'text-decoration-color' . self::space() . $value . self::end;
    }

    /**
     * 文本装饰线条的形状
     * 
     * @param string $value
     *            可能的值：
     *            - solid：实线
     *            - double：双线
     *            - dotted：点状线条
     *            - dashed：虚线
     *            - wavy：波浪线
     * @return string
     */
    public function text_decoration_style($value)
    {
        return 'text-decoration-style' . self::space() . $value . self::end;
    }

    /**
     * 文本装饰线条必须略过内容中的哪些部分
     * 
     * @param string $value
     *            可能的值：
     *            - none：不略过：文本装饰将绘制在所有文本内容及行内盒上。
     *            - objects：略过原子内联元素（例如图片或内联块）
     *            - spaces：略过空白：包括常规空白（U+0020）、制表符（U+0009）以及不间断空格（U+00A0）、表意空格（U+3000）、所有固定宽度空格（U+2000至U+200A、U+202F和U+205F）、以及相邻的字母间隔或单词间隔。
     *            - ink：略过字符绘制处：中断装饰线，以显示文本装饰件将穿过该字形的文本。用户代理可能还会在该字形轮廓的两侧额外的略过一段距离。
     *            - edges：用户代理应当将装饰线的起始、结束位置放置于较所装饰元素的内容边缘更靠内的位置，使得诸如两个紧密相邻的元素的下划线不会显示为一条下划线。（这在中文里很重要，对于中文，下划线是一种标点符号。）
     *            - box-decoration：略过盒子的margin,border,padding区域。需要注意的是，这只针对祖先的装饰效果，装饰盒不会绘制自身的装饰。
     * @return string
     */
    public function text_decoration_skip($value)
    {
        return 'text-decoration-skip' . self::space() . $value . self::end;
    }

    /**
     * 下划线的位置
     * 
     * @param string $value
     *            可能的值：
     *            - auto：用户代理可能会使用任意算法确定下划线的位置。
     *            - under：下划线的定位与元素内容盒子的下边缘相关
     *            - left：下划线的定位与元素内容盒子的左边缘相关
     *            - right：下划线的定位与元素内容盒子的右边缘相关
     * @return string
     */
    public function text_underline_position($value)
    {
        return 'text-underline-position' . self::space() . $value . self::end;
    }

    /**
     * 内容块固有的书写方向
     * 
     * @param string $value
     *            可能的值：
     *            - horizontal-tb：水平方向自上而下的书写方式。即 left-right-top-bottom（类似IE私有值lr-tb）
     *            - vertical-rl：垂直方向自右而左的书写方式。即 top-bottom-right-left（类似IE私有值tb-rl）
     *            - vertical-lr：垂直方向自左而右的书写方式。即 top-bottom-left-right
     *            - lr-tb：左-右，上-下。对象中的内容在水平方向上从左向右流入，后一行在前一行的下面。 所有的字形都是竖直向上的。这种布局是罗马语系使用的（IE）
     *            - tb-rl：上-下，右-左。对象中的内容在垂直方向上从上向下流入，自右向左。后一竖行在前一竖行的左面。全角字符是竖直向上的，半角字符如拉丁字母或片假名顺时针旋转90度。这种布局是东亚语系通常使用的（IE）
     * @return string
     */
    public function writing_mode($value)
    {
        return 'writing-mode' . self::space() . $value . self::end;
    }

    /**
     * 区域是否允许用户缩放，调节元素尺寸大小。
     *
     * - IE 、iOS Safari 、Android Browser 不支持
     * - Firefox老版本需要`-moz-`, 5开始不需要前缀
     * - Chrome Safari Opera 支持
     * 
     * @param string $value
     *            可能的值：
     *            - none：不允许用户调整元素大小。
     *            - both：用户可以调节元素的宽度和高度。
     *            - horizontal：用户可以调节元素的宽度
     *            - vertical：用户可以调节元素的高度。
     * @return string
     */
    public function resize($value)
    {
        return 'resize' . self::space() . $value . self::end;
    }

    /**
     * 是否允许用户选中文本
     *
     * ~~~兼容版本
     * .test{
     * -webkit-user-select:none; // 谷歌，Safari，Android ， ios
     * -moz-user-select:none; // 火狐
     * -o-user-select:none; // Opera
     * -ms-user-select:none; // IE10~11
     * user-select:none;
     * }
     * <div class="test" onselectstart="return false;" unselectable="on"> 无法选中的文本 </div>
     * ~~~
     *
     * - IE6-9不支持该属性，但支持使用标签属性 onselectstart="return false;"
     * 来达到 user-select:none 的效果；Safari和Chrome也支持该标签属性；
     *
     * - 直到Opera12.5仍然不支持该属性，但支持使用私有的标签属性 unselectable="on"
     * 来达到 user-select:none 的效果；unselectable 的另一个值是 off；
     *
     * 除Chrome和Safari外，在其它浏览器中，如果将文本设置为 -ms-user-select:none;
     * 则用户将无法在该文本块中开始选择文本。不过，如果用户在页面的其他区域开始选择文本，
     * 则用户仍然可以继续选择将文本设置为 -ms-user-select:none; 的区域文本；
     *
     * @param string $value
     *            可能的值：
     *            - none：文本不能被选择
     *            - text：可以选择文本
     *            - all：当所有内容作为一个整体时可以被选择。如果双击或者在上下文上点击子元素，那么被选择的部分将是以该子元素向上回溯的最高祖先元素。
     *            - element：可以选择文本，但选择范围受元素边界的约束
     * @return string
     */
    public function user_select($value)
    {
        return 'user-select' . self::space() . $value . self::end;
    }

    /**
     * 让元素无法点击响应
     *
     * - 作用：可以让a标签的连接无法点击
     * - 设置或检索在何时成为属性事件的target
     *
     * @param string $value
     *            可能的值：
     *            - auto：与pointer-events属性未指定时的表现效果相同
     *            - none：元素永远不会成为鼠标事件的target。
     * @return string
     */
    public function pointer_events($value)
    {
        return 'pointer-events' . self::space() . $value . self::end;
    }
    
    //=======================多列（multi-column）===========================

    /**
     * 设置对象的列数和每列的宽度
     * 
     * - IE10以及以上支持
     * 
     * ~~~
     * <p>很多文字的，段落</p>
     * # 将文字 以200像素的宽度排列，多出的自动换列
     * columns:200px; 
     * # 将文字以200像素的宽度限制每行字数，并固定3列。
     * columns:200px 3;
     * ~~~
     * 
     * @param string $width 对象每列的宽度，即column-width属性
     * @param string $count 对象的列数，即column-count属性
     * @return string
     */
    public function columns($width, $count)
    {
        $value = $this->mergeAttr($width, $count);
        return $this->cssPrefix(
            'columns' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 每列的宽度
     * @param string $value 可能的值：
     * - <length>：用长度值来定义列宽。不允许负值
     * - auto：根据 <' column-count '> 自定分配宽度
     * @return string
     */
    public function column_width($value)
    {
        return $this->cssPrefix(
            'column-width' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 对象的列数
     * @param string $value 可能的值：
     * - <integer>：用整数值来定义列数。不允许负值
     * - auto：根据 <' column-count '> 自定分配宽度
     * @return string
     */
    public function column_count($value)
    {
        return $this->cssPrefix(
            'column-count' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的间隙
     * 
     * - 如果不兼容，添加`-moz-` 或`-webkit-`
     * @param string $value 可能的值：
     * - <length>：用长度值来定义列与列之间的间隙。不允许负值
     * - normal：与 <' font-size '> 大小相同
     * @return string
     */
    public function column_gap($value)
    {
        return $this->cssPrefix(
            'column-gap' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的边框
     * @param string $width 列与列之间的边框厚度， 即column-rule-width
     * @param string $style 列与列之间的边框样式
     * @param string $color 列与列之间的边框颜色
     * @return string
     */
    public function column_rule($width, $style, $color)
    {
        $value = $this->mergeAttr($width, $style, $color);
        return $this->cssPrefix(
            'column-rule' . self::space() . $value . self::end, 
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的边框厚度
     * @param string $value 可能的值：
     * - <length>：用长度值来定义边框的厚度。不允许负值
     * - medium：定义默认厚度的边框。
     * - thin：定义比默认厚度细的边框。
     * - thick：定义比默认厚度粗的边框。
     * @return string
     */
    public function column_rule_width($value)
    {
        return $this->cssPrefix(
            'column-rule-width' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的边框样式
     * @param string $value 可能的值：
     * - none：无轮廓。<' column-rule-color '> 与<' column-rule-width '> 将被忽略
     * - hidden：隐藏边框。
     * - dotted：点状轮廓。
     * - dashed：虚线轮廓。
     * - solid：实线轮廓
     * - double：双线轮廓。两条单线与其间隔的和等于指定的 <' column-rule-width '> 值
     * - groove：3D凹槽轮廓。
     * - ridge：3D凸槽轮廓。
     * - inset：3D凹边轮廓。
     * - outset：3D凸边轮廓
     * @return string
     */
    public function column_rule_style($value)
    {
        return $this->cssPrefix(
            'column-rule-style' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的边框颜色
     * @param string $value 指定颜色
     * @return string
     */
    public function column_rule_color($value)
    {
        return $this->cssPrefix(
            'column-rule-color' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 列与列之间的边框颜色
     * 
     * - IE10以下不支持，火狐不支持
     * @param string $value 可能的值：
     * - none：不跨列
     * - all：横跨所有列
     * @return string
     */
    public function column_span($value)
    {
        return $this->cssPrefix(
            'column-span' . self::space() . $value . self::end,
            ['-webkit-']);
    }
    
    /**
     * 所有列的高度是否统一
     *
     * - IE10以下不支持
     * @param string $value 可能的值：
     * - auto：列高度自适应内容
     * - balance：所有列的高度以其中最高的一列统一
     * @return string
     */
    public function column_fill($value)
    {
        return $this->cssPrefix(
            'column-fill' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 对象之前是否断行
     * @param string $value 可能的值：
     * - auto：既不强迫也不禁止在元素之前断行并产生新列
     * - always：总是在元素之前断行并产生新列
     * - avoid：避免在元素之前断行并产生新列
     * @return string
     */
    public function column_break_before($value)
    {
        return $this->cssPrefix(
            'column-break-before' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 对象之后是否断行
     * @param string $value 可能的值：
     * - auto：既不强迫也不禁止在元素之后断行并产生新列
     * - always：总是在元素之后断行并产生新列
     * - avoid：避免在元素之后断行并产生新列
     * @return string
     */
    public function column_break_after($value)
    {
        return $this->cssPrefix(
            'column-break-after' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }
    
    /**
     * 对象内部是否断行
     * @param string $value 可能的值：
     * - auto：既不强迫也不禁止在元素内部断行并产生新列
     * - avoid：避免在元素内部断行并产生新列
     * @return string
     */
    public function column_break_inside($value)
    {
        return $this->cssPrefix(
            'column-break-inside' . self::space() . $value . self::end,
            ['-moz-', '-webkit-']);
    }

}