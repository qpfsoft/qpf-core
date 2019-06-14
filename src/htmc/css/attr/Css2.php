<?php
namespace qpf\htmc\css\attr;

/**
 * CSS2 其他属性
 * 
 * @author qiun
 *        
 */
class Css2 extends CssAttr
{

    /**
     * 在对象前或后显示内容 - 用来和:after及:before伪元素一起使用
     * 
     * @param string $value
     *            可能的值：
     *            - normal：默认值。表现与none值相同
     *            - none：不生成任何值。
     *            - attr()：插入标签属性值, 例如`content:attr(title);`
     *            - url(): 使用指定的绝对或相对地址插入一个外部资源（图像，声频，视频或浏览器支持的其他任何资源）
     *            - string : 插入字符串
     *            - counter(name) ：使用已命名的计数器
     *            - counter(name,list-style-type):使用已命名的计数器并遵从指定的list-style-type属性
     *            - counters(name,string)：使用所有已命名的计数器
     *            - counters(name,string,list-style-type)：使用所有已命名的计数器并遵从指定的list-style-type属性
     *            - no-close-quote：并不插入quotes属性的后标记。但增加其嵌套级别
     *            - no-open-quote：并不插入quotes属性的前标记。但减少其嵌套级别
     *            - close-quote：插入quotes属性的后标记
     *            - open-quote：插入quotes属性的前标记
     * @return string
     */
    public function content($value)
    {
        $value = '"' . $value . '"';
        return 'content' . self::space() . $value . self::end;
    }

    /**
     * 设定当一个selector发生时计数器增加的值
     * 
     * @param string $value
     *            可能的值：
     *            - none：阻止计数器增加
     *            - <identifier>：identifier定义一个或多个将被增加的selector，id，或者class
     *            - <integer>：定义计算器每次增加的数值，可以为负值，默认值是1
     * @return string
     */
    public function counter_increment($value)
    {
        return 'counter-increment' . self::space() . $value . self::end;
    }

    /**
     * 将指定selector的计数器复位
     * 
     * @param string $value
     *            可能的值：
     *            - none：阻止计数器增加
     *            - <identifier>：identifier定义一个或多个将被增加的selector，id，或者class
     *            - <integer>：定义计算器每次增加的数值，可以为负值，默认值是0
     * @return string
     */
    public function counter_reset($value)
    {
        return 'counter-reset' . self::space() . $value . self::end;
    }

    /**
     * 设置或检索对象内使用的嵌套标记
     *
     * ~~~实例
     * q:lang(en){quotes:'[' ']' "<" ">";}
     * q:lang(zh-cmn-Hans){quotes:"«" "»" '"' '"';}
     * # html
     * <p lang="en"><q>Quote me <q>Quote me</q> Quote me!</q></p>
     * <p lang="zh-cmn-Hans"><q>Quote me <q>Quote me</q> Quote me!</q></p>
     * # 结果
     *
     * ~~~
     * 
     * @param string $value
     *            可能的值：
     *            - none：content属性的open-quote和close-quote值将不会生成任何标记
     *            - <string>：定义content属性的open-quote和close-quote值的标记，2个为一组
     * @return string
     */
    public function quotes($value)
    {
        return 'quotes' . self::space() . $value . self::end;
    }

    /**
     * 移动到对象上的的光标形状
     *
     * IE, Opera只支持*.cur等特定的图片格式；Firefox, Chrome, Safari既支持
     * 特定图片类型也支持常见的*.png, *.gif, *.jpg等图片格式。
     *
     * ~~~
     * :link,:visited{
     * cursor:url(example.svg#linkcursor),
     * url(hyper.cur),
     * url(hyper.png) 2 3,
     * pointer;
     * }
     * 客户端如果不支持SVG类型的光标，则使用下个"hyper.cur"；
     * 如果cur类型也不支持，则使用下个"hyper.png"；依次类推
     * ~~~
     *
     * @param string $value 可能的值：
     * - url() : 使用绝对或者相对地址引入外部图像作为光标形状
     *            <url> <x> <y>：通过<x> <y>两个值指定具体需要显示的图像位置。
     * - auto：用户代理基于当前上下文决定光标形状
     * - default：相关平台的默认光标形状，通常为箭头。
     * - none：没有光标形状
     * - help : 鼠标加问号
     * - pointer : 点击手 - 常用
     * - progress ： 鼠标加圆圈， 代表运行中
     * - wait ： 圆圈，系统忙
     * - cell ： 单元格十字架
     * - crosshair ： 瞄准十字架，细
     * - text ： 文本 `I`
     * - copy ： 鼠标加号
     * - move : 移动
     * - no-drop : 不可选
     * - zoom-in ： 放大镜
     * - zoom-out ： 缩小镜
     * - 还有一组边框拉伸鼠标。
     * @return string
     */
    public function cursor($value)
    {
        return 'cursor' . self::space() . $value . self::end;
    }
}