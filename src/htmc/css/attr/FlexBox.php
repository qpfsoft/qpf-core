<?php
namespace qpf\htmc\css\attr;

/**
 * 伸缩盒子（新）
 * 
 * - css3 伸缩盒 Flexible Box Layout
 * 
 * 使用伸缩盒子需要将父元素设置为`display:flex;`.
 * 
 * ~~~实例
 * <ul class="flex">
 *  <li>a</li>
 *  <li>b</li>
 *  <li>c</li>
 *</ul>
 *.flex{display:flex;width:800px;margin:0;padding:0;list-style:none;}
 *.flex :nth-child(1){flex:1 1 300px;}
 *.flex :nth-child(2){flex:2 2 200px;}
 *.flex :nth-child(3){flex:3 3 400px;}
 * ~~~
 * 
 * flex	CSS3	无	复合属性。设置或检索伸缩盒对象的子元素如何分配空间。
 * flex-grow	CSS3	无	设置或检索弹性盒的扩展比率。
 * flex-shrink	CSS3	无	设置或检索弹性盒的收缩比率
 * flex-basis	CSS3	无	设置或检索弹性盒伸缩基准值。
 * flex-flow	CSS3	无	复合属性。设置或检索伸缩盒对象的子元素排列方式。
 * flex-direction	CSS3	无	设置或检索伸缩盒对象的子元素在父容器中的位置。
 * flex-wrap	CSS3	无	设置或检索伸缩盒对象的子元素超出父容器时是否换行。
 * align-content	CSS3	无	设置或检索弹性盒堆叠伸缩行的对齐方式。
 * align-items	CSS3	无	设置或检索弹性盒子元素在侧轴（纵轴）方向上的对齐方式。
 * align-self	CSS3	无	设置或检索弹性盒子元素自身在侧轴（纵轴）方向上的对齐方式。
 * justify-content	CSS3	无	设置或检索弹性盒子元素在主轴（横轴）方向上的对齐方式。
 * order	CSS3	无	设置或检索伸缩盒对象的子元素出現的順序。
 * 
 * @author qiun
 *
 */
class FlexBox extends CssAttr
{
    /**
     * 元素显示为伸缩盒子
     * 
     * - 该属性需设置给父元素
     * @return string
     */
    public function display_flex()
    {
        return $this->cssPrefix('display'. self::space() . 'flex' . self::end, ['-webkit-'], true);
    }
    
    /**
     * 设置或检索弹性盒模型对象的子元素如何分配空间
     * 
     * - 如果缩写「flex: 1」, 则其计算值为「1 1 0%」
     * - 如果缩写「flex: auto」, 则其计算值为「1 1 auto」
     * - 如果「flex: none」, 则其计算值为「0 0 auto」
     * - 如果「flex: 0 auto」或者「flex: initial」, 则其计算值为「0 1 auto」，即「flex」初始值
     * 
     * @param string $grow 用来指定扩展比率，即剩余空间是正值时此「flex子项」相对于「flex容器」里其他「flex子项」能分配到空间比例。
     * 在「flex」属性中该值如果被省略则默认为「1」
     * @param string $shrink 用来指定收缩比率，即剩余空间是负值时此「flex子项」相对于「flex容器」里其他「flex子项」能收缩的空间比例。
     * 在收缩的时候收缩比率会以伸缩基准值加权,在「flex」属性中该值如果被省略则默认为「1」
     * @param string $basis 用来指定伸缩基准值，即在根据伸缩比率计算出剩余空间的分布之前，「flex子项」长度的起始数值。
     * 在「flex」属性中该值如果被省略则默认为「0%」
     * 在「flex」属性中该值如果被指定为「auto」，则伸缩基准值的计算值是自身的 <' width '> 设置，如果自身的宽度没有定义，则长度取决于内容。
     * @return string
     */
    public function flex($grow, $shrink, $basis)
    {
        $value = $this->mergeAttr($grow, $shrink, $basis);
        return 'flex'. self::space() . $value . self::end;
    }
    
    /**
     * 弹性盒的扩展比率
     * 
     * ~~~示例b,c将按照1:3的比率分配剩余空间
     * <ul class="flex">
     * <li>a</li>
     * <li>b</li>
     * <li>c</li>
     * </ul>
     * .flex{display:flex;width:600px;margin:0;padding:0;list-style:none;}
     * .flex li:nth-child(1){width:200px;}
     * .flex li:nth-child(2){flex-grow:1;width:50px;}
     * .flex li:nth-child(3){flex-grow:3;width:50px;}
     * 
     * - 站比例，是指剩余的宽度部分。
     * 
     * 本例中b,c两项都显式的定义了flex-grow，flex容器的剩余空间分成了4份，其中b占1份，c占3分，即1:3
     * flex容器的剩余空间长度为：600-200-50-50=300px，所以最终a,b,c的长度分别为：
     * a: 50+(300/4)=200px
     * b: 50+(300/4*1)=125px
     * a: 50+(300/4*3)=275px
     * ~~~
     * 
     * @param string $value 用数值来定义扩展比率。不允许负值
     * - 默认值为0，如果没有显示定义该属性，是不会拥有分配剩余空间权利的。
     * @return string
     */
    public function flex_grow($value)
    {
        return $this->cssPrefix('flex-grow'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 弹性盒的收缩比率
     * 
     * ~~~示例 a,b,c将按照1:1:3的比率来收缩空间
     * <ul class="flex">
     * <li>a</li>
     * <li>b</li>
     * <li>c</li>
     * </ul>
     * .flex{display:flex;width:400px;margin:0;padding:0;list-style:none;}
     * .flex li{width:200px;}
     * .flex li:nth-child(3){flex-shrink:3;}
     * lex-shrink的默认值为1，如果没有显示定义该属性，将会自动按照默认值1在所有因子相加之后计算比率来进行空间收缩。
     * 本例中c显式的定义了flex-shrink，a,b没有显式定义，但将根据默认值1来计算，可以看到总共将剩余空间分成了5份，其中a占1份，b占1份，c占3分，即1:1:3
     * 我们可以看到父容器定义为400px，子项被定义为200px，相加之后即为600px，超出父容器200px。那么这么超出的200px需要被a,b,c消化
     * 通过收缩因子，所以加权综合可得200*1+200*1+200*3=1000px；
     * 于是我们可以计算a,b,c将被移除的溢出量是多少：
     * a被移除溢出量：(200*1/1000)*200，即约等于40px
     * b被移除溢出量：(200*1/1000)*200，即约等于40px
     * c被移除溢出量：(200*3/1000)*200，即约等于120px
     * 最后a,b,c的实际宽度分别为：200-40=160px, 200-40=160px, 200-120=80px
     * ~~~
     * 
     * @param string $value 用数值来定义收缩比率。不允许负值
     * @return string
     */
    public function flex_shrink($value)
    {
        return $this->cssPrefix('flex-shrink'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 弹性盒伸缩基准值
     * 
     * 如果所有子元素的基准值之和大于剩余空间，
     * 则会根据每项设置的基准值，按比率伸缩剩余空间
     * 
     * @param string $value 可能的值：
     * - <length>：用长度值来定义宽度。不允许负值
     * - <percentage>：用百分比来定义宽度。不允许负值
     * - auto：无特定宽度值，取决于其它属性值
     * - content：基于内容自动计算宽度
     * @return string
     */
    public function flex_basis($value)
    {
        return $this->cssPrefix('flex-basis'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 弹性盒模型对象的子元素排列方式
     * @param string $direction 定义弹性盒子元素的排列方向
     * @param string $wrap 控制flex容器是单行或者多行
     * @return string
     */
    public function flex_flow($direction, $wrap)
    {
        $value = $this->mergeAttr($direction, $wrap);
        return $this->cssPrefix('flex-flow'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * flex容器的主轴方向
     * @param string $value 可能的值：
     * - row：主轴与行内轴方向作为默认的书写模式。即横向从左到右排列（左对齐）。
     * - row-reverse：对齐方式与row相反。
     * - column：主轴与块轴方向作为默认的书写模式。即纵向从上往下排列（顶对齐）。
     * - column-reverse：对齐方式与column相反。
     * @return string
     */
    public function flex_direction($value)
    {
        return $this->cssPrefix('flex-direction'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * flex容器是单行或者多行，同时横轴的方向决定了新行堆叠的方向。
     * @param string $value 可能的值：
     * - nowrap：flex容器为单行。该情况下flex子项可能会溢出容器
     * - wrap：flex容器为多行。该情况下flex子项溢出的部分会被放置到新行，子项内部会发生断行
     * - wrap-reverse：反转 wrap 排列。
     * @return string
     */
    public function flex_wrap($value)
    {
        return $this->cssPrefix('flex-wrap'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 容器的侧轴还有多余空间时，
     * 本属性可以用来调准「伸缩行」在伸缩容器里的对齐方式，
     * 这与调准伸缩项目在主轴上对齐方式的 <' justify-content '> 属性类似。
     * 请注意本属性在只有一行的伸缩容器上没有效果。
     * 
     * @param string $value 可能的值：
     * - flex-start：各行向弹性盒容器的起始位置堆叠。弹性盒容器中第一行的侧轴起始边界紧靠住该弹性盒容器的侧轴起始边界，之后的每一行都紧靠住前面一行。
     * - flex-end：各行向弹性盒容器的结束位置堆叠。弹性盒容器中最后一行的侧轴起结束界紧靠住该弹性盒容器的侧轴结束边界，之后的每一行都紧靠住前面一行。
     * - center：各行向弹性盒容器的中间位置堆叠。各行两两紧靠住同时在弹性盒容器中居中对齐，保持弹性盒容器的侧轴起始内容边界和第一行之间的距离与该容器的侧轴结束内容边界与第最后一行之间的距离相等。（如果剩下的空间是负数，则各行会向两个方向溢出的相等距离。）
     * - space-between：各行在弹性盒容器中平均分布。如果剩余的空间是负数或弹性盒容器中只有一行，该值等效于'flex-start'。在其它情况下，第一行的侧轴起始边界紧靠住弹性盒容器的侧轴起始内容边界，最后一行的侧轴结束边界紧靠住弹性盒容器的侧轴结束内容边界，剩余的行则按一定方式在弹性盒窗口中排列，以保持两两之间的空间相等。
     * - space-around：各行在弹性盒容器中平均分布，两端保留子元素与子元素之间间距大小的一半。如果剩余的空间是负数或弹性盒容器中只有一行，该值等效于'center'。在其它情况下，各行会按一定方式在弹性盒容器中排列，以保持两两之间的空间相等，同时第一行前面及最后一行后面的空间是其他空间的一半。
     * - stretch：各行将会伸展以占用剩余的空间。如果剩余的空间是负数，该值等效于'flex-start'。在其它情况下，剩余空间被所有行平分，以扩大它们的侧轴尺寸。
     * @return string
     */
    public function align_content($value)
    {
        return $this->cssPrefix('align-content'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 定义flex子项在flex容器的当前行的侧轴（纵轴）方向上的对齐方式。
     * @param string $value 可能的值：
     * - flex-start：弹性盒子元素的侧轴（纵轴）起始位置的边界紧靠住该行的侧轴起始边界。
     * - flex-end：弹性盒子元素的侧轴（纵轴）起始位置的边界紧靠住该行的侧轴结束边界。
     * - center：弹性盒子元素在该行的侧轴（纵轴）上居中放置。（如果该行的尺寸小于弹性盒子元素的尺寸，则会向两个方向溢出相同的长度）。
     * - baseline：如弹性盒子元素的行内轴与侧轴为同一条，则该值与'flex-start'等效。其它情况下，该值将参与基线对齐。
     * - stretch：如果指定侧轴大小的属性值为'auto'，则其值会使项目的边距盒的尺寸尽可能接近所在行的尺寸，但同时会遵照'min/max-width/height'属性的限制。
     * @return string
     */
    public function align_items($value)
    {
        return $this->cssPrefix('align-items'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 定义flex子项单独在侧轴（纵轴）方向上的对齐方式
     * @param string $value 可能的值：
     * - auto：如果'align-self'的值为'auto'，则其计算值为元素的父元素的'align-items'值，如果其没有父元素，则计算值为'stretch'。
     * - flex-start：弹性盒子元素的侧轴（纵轴）起始位置的边界紧靠住该行的侧轴起始边界。
     * - flex-end：弹性盒子元素的侧轴（纵轴）起始位置的边界紧靠住该行的侧轴结束边界。
     * - center：弹性盒子元素在该行的侧轴（纵轴）上居中放置。（如果该行的尺寸小于弹性盒子元素的尺寸，则会向两个方向溢出相同的长度）。
     * - baseline：如弹性盒子元素的行内轴与侧轴为同一条，则该值与'flex-start'等效。其它情况下，该值将参与基线对齐。
     * - stretch：如果指定侧轴大小的属性值为'auto'，则其值会使项目的边距盒的尺寸尽可能接近所在行的尺寸，但同时会遵照'min/max-width/height'属性的限制。
     * @return string
     */
    public function align_self($value)
    {
        return $this->cssPrefix('align-self'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 弹性盒子元素在主轴（横轴）方向上的对齐方式
     * @param string $value 可能的值：
     * - flex-start：弹性盒子元素将向行起始位置对齐。该行的第一个子元素的主起始位置的边界将与该行的主起始位置的边界对齐，同时所有后续的伸缩盒项目与其前一个项目对齐。
     * - flex-end：弹性盒子元素将向行结束位置对齐。该行的第一个子元素的主结束位置的边界将与该行的主结束位置的边界对齐，同时所有后续的伸缩盒项目与其前一个项目对齐。
     * - center：弹性盒子元素将向行中间位置对齐。该行的子元素将相互对齐并在行中居中对齐，同时第一个元素与行的主起始位置的边距等同与最后一个元素与行的主结束位置的边距（如果剩余空间是负数，则保持两端相等长度的溢出）。
     * - space-between：弹性盒子元素会平均地分布在行里。如果最左边的剩余空间是负数，或该行只有一个子元素，则该值等效于'flex-start'。在其它情况下，第一个元素的边界与行的主起始位置的边界对齐，同时最后一个元素的边界与行的主结束位置的边距对齐，而剩余的伸缩盒项目则平均分布，并确保两两之间的空白空间相等。
     * - space-around：弹性盒子元素会平均地分布在行里，两端保留子元素与子元素之间间距大小的一半。如果最左边的剩余空间是负数，或该行只有一个伸缩盒项目，则该值等效于'center'。在其它情况下，伸缩盒项目则平均分布，并确保两两之间的空白空间相等，同时第一个元素前的空间以及最后一个元素后的空间为其他空白空间的一半。
     * @return string
     */
    public function justify_content($value)
    {
        return $this->cssPrefix('justify-content'. self::space() . $value . self::end, ['-webkit-']);
    }
    
    /**
     * 弹性盒模型对象的子元素出現的順序
     * @param string $value 用整数值来定义排列顺序，数值小的排在前面。可以为负值。
     * @return string
     */
    public function order($value)
    {
        return $this->cssPrefix('order'. self::space() . $value . self::end, ['-webkit-']);
    }
}