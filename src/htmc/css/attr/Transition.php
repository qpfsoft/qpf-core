<?php
namespace qpf\htmc\css\attr;

/**
 * CSS3 过度动画效果
 * 
 * css的transition允许css的属性值在一定的时间区间内平滑地过渡。
 * 这种效果可以在鼠标单击、获得焦点、被点击或对元素任何改变中触发，并圆滑地以动画效果改变CSS的属性值。
 * 
 * 类似flash的创建`补间动画`;
 * 
 * 兼容性:
 * ie : 6-9 不支持
 * 
 * # 创建css3的过度效果, 实现步骤:
 * 1. 在默认样式中声明元素的初始状态样式;
 * 2. 声明过度元素最终状态样式, 比如`:hover`
 * 3. 在默认样式中添加过度函数, 添加一些不同的样式
 * 
 * 
 * 
 * 具体什么属性值可以有过渡效果, 可访问W3C:
 * https://www.w3.org/TR/css3-transitions/#properties-from-css-
 * 
 * 属性类型：
 * - color:通过红、绿、蓝和透明度组件变换（每个数值处理）
 * -length:真实的数字
 * -percentage:真实的数字
 * -integer离散步骤（整个数字），在真实的数字空间，以及使用floor()转换为整数时发生
 * -number真实的（浮点型）数值
 * -transform list:详情请参阅：[CSS3-2D-TRANSFORMS]
 * -rectangle:通过x, y, width 和 height（转为数值）变换
 * -visibility:离散步骤，在0到1数字范围之内，0表示“隐藏”，1表示完全“显示”
 * -shadow:作用于color, x, y 和 blur（模糊）属性
 * -gradient:通过每次停止时的位置和颜色进行变化。它们必须有相同的类型（放射状的或是线性的）和相同的停止数值以便执行动画
 * -paint server (SVG):只支持下面的情况：从gradient到gradient以及color到color，然后工作与上面类似
 * -space-separated list of above:如果列表有相同的项目数值，则列表每一项按照上面的规则进行变化，否则无变化
 * -a shorthand property:如果缩写的所有部分都可以实现动画，则会像所有单个属性变化一样变化
 * 
 * 属性名                          类型
 * background-color	color
 * background-image	only gradients
 * background-position	percentage, length
 * border-bottom-color	color
 * border-bottom-width	length
 * border-color	color
 * border-left-color	color
 * border-left-width	length
 * border-right-color	color
 * border-right-width	length
 * border-spacing	length
 * border-top-color	color
 * border-top-width	length
 * border-width	length
 * bottom	length, percentage
 * color	color
 * crop	rectangle
 * font-size	length, percentage
 * font-weight	number
 * grid-*	various
 * height	length, percentage
 * left	length, percentage
 * letter-spacing	length
 * line-height	number, length, percentage
 * margin-bottom	length
 * margin-left	length
 * margin-right	length
 * margin-top	length
 * max-height	length, percentage
 * max-width	length, percentage
 * min-height	length, percentage
 * min-width	length, percentage
 * opacity	number
 * outline-color	color
 * outline-offset	integer
 * outline-width	length
 * padding-bottom	length
 * padding-left	length
 * padding-right	length
 * padding-top	length
 * right	length, percentage
 * text-indent	length, percentage
 * text-shadow	shadow
 * top	length, percentage
 * vertical-align	keywords, length, percentage
 * visibility	visibility
 * width	length, percentage
 * word-spacing	length, percentage
 * z-index	integer
 * zoom	number
 * 
 * @author qiun
 *
 */
class Transition extends CssAttr
{
    /**
     * css3过度动画效果 - 复合
     * @param string $property 指定过渡或动态模拟的CSS属性
     * @param string $duration 指定完成过渡所需的时间,  例 `.5s` 0.5秒 
     * @param string $timing 指定过渡函数, 可能的值:
     * * - linear：线性过渡。等同于贝塞尔曲线(0.0, 0.0, 1.0, 1.0)
     * - ease：平滑过渡。等同于贝塞尔曲线(0.25, 0.1, 0.25, 1.0)
     * - ease-in：由慢到快。等同于贝塞尔曲线(0.42, 0, 1.0, 1.0)
     * - ease-out：由快到慢。等同于贝塞尔曲线(0, 0, 0.58, 1.0)
     * - ease-in-out：由慢到快再到慢。等同于贝塞尔曲线(0.42, 0, 0.58, 1.0)
     * - step-start：等同于 steps(1, start)
     * - step-end：等同于 steps(1, end)
     * - steps(<integer>[, [ start | end ] ]?)：接受两个参数的步进函数。第一个参数必须为正整数，指定函数的步数。第二个参数取值可以是start或end，指定每一步的值发生变化的时间点。第二个参数是可选的，默认值为end。
     * - cubic-bezier(<number>, <number>, <number>, <number>)：特定的贝塞尔曲线类型，4个数值需在[0, 1]区间内
     * @param string $delay 指定开始时出现的延迟时间,  例 `.5s` 0.5秒 , 默认值`0`, 立即执行
     * @return string
     */
    public function transitions($property, $duration, $timing, $delay = 0)
    {
        $value = $this->mergeAttr($property, $duration, $timing, $delay);
        $css = 'transition' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-']);
    }
    
    /**
     * 指定过渡或动态模拟的CSS属性
     * 
     * 
     * 语法: transition-property ： none | all | [ <IDENT> ] [ ',' <IDENT> ]*
     * 
     * ~~~示例
     * div{
     *      width: 300px;    // 初始状态
     *      transition-property: width; // 给宽度添加过度效果
     * }
     * div:hover{
     *      width: 100px;   // 最终样式
     * }
     * ~~~
     * @param string $value 可能的值:
     * - none : 没有属性改变, 当none时过度马上停止
     * - all  : 所有属性改变, 默认值, 元素任何属性值变化都将执行过度效果
     * - <indent> : 元素属性名, 
     * - <ident> : 可以指定元素的某一个属性值, 例如`width`
     * @return string
     */
    public function transition_property($value)
    {
        $css = 'transition-property' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-']);
    }
    
    /**
     * 指定完成过渡所需的时间
     * 
     * 时间单位:  ms, s
     * 单位换算:  1s = 1000ms (不允许负值, 小数点前的0可忽略)
     * @param string $value 动画时长, 例 `.5s` 0.5秒 
     * @return string
     */
    public function transition_duration($value)
    {
        $css = 'transition-duration' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-']);
    }
    
    /**
     * 指定过渡函数 - 动画效果
     * @param string $value 可能的值: 如果提供多个属性值，以逗号进行分隔。
     * - linear：(均速)线性过渡。等同于贝塞尔曲线(0.0, 0.0, 1.0, 1.0)
     * - ease：(逐渐变慢, 默认值)平滑过渡。等同于贝塞尔曲线(0.25, 0.1, 0.25, 1.0)
     * - ease-in：(加速)由慢到快。等同于贝塞尔曲线(0.42, 0, 1.0, 1.0)
     * - ease-out：(减速)由快到慢。等同于贝塞尔曲线(0, 0, 0.58, 1.0)
     * - ease-in-out：(加速然后减速)由慢到快再到慢。等同于贝塞尔曲线(0.42, 0, 0.58, 1.0)
     * - step-start：等同于 steps(1, start)
     * - step-end：等同于 steps(1, end)
     * - steps(<integer>[, [ start | end ] ]?)：接受两个参数的步进函数。第一个参数必须为正整数，指定函数的步数。第二个参数取值可以是start或end，指定每一步的值发生变化的时间点。第二个参数是可选的，默认值为end。
     * - cubic-bezier(<number>, <number>, <number>, <number>)：特定的贝塞尔曲线类型，4个数值需在[0, 1]区间内
     * @return string
     */
    public function transition_timing_function($value)
    {
        $css = 'transition-timing-function' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-']);
    }
    
    /**
     * 指定开始时出现的延迟时间 - 设定激活时间,防止立即触发
     * 
     * 时间单位:  ms, s
     * 单位换算:  1s = 1000ms (不允许负值, 小数点前的0可忽略)
     * 
     * @param string $value 延时时间, 例 `.5s` 0.5秒 , 默认值`0` 立即执行
     * @return string
     */
    public function transition_delay($value)
    {
        $css = 'transition-delay' . self::space() . $value . self::end;
        return $this->cssPrefix($css, ['-moz-', '-webkit-']);
    }
}