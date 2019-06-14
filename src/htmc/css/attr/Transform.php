<?php
namespace qpf\htmc\css\attr;

/**
 * 元素变形，变换
 * 
 * 
 * - 圆心点 : 用于指定旋转,倾斜的中心点.
 * - 位移: 始终参照元素的中心点. 不会被自定义圆心点影响.
 * 
 * 
 * 
 * @author qiun
 *
 */
class Transform extends CssAttr
{
    
    /**
     * 矩阵变换
     * 
     * - 矩阵可以理解为方阵，只不过，平时方阵里面站的是人，矩阵中是数值.
     * - CSS3中的矩阵指的是一个方法，书写为matrix()和matrix3d()，
     *   前者是元素2D平面的移动变换(transform)，后者则是3D变换。
     *   2D变换矩阵为3*3,  3D变换则是4*4的矩阵。
     * - 斜拉(skew)，缩放(scale)，旋转(rotate)以及位移(translate), 本质上都是应用的matrix()方法实现的.
     * - 
     */
    public function transform_matrix()
    {
        
    }
    
    /**
     * 矩阵变换 - 2D偏移
     * @param string $x 水平便宜位置, 整数,不要带px单位, 默认值`0`
     * @param string $y 垂直偏移位置, 整数,不要带px单位, 默认值`0`
     * @return string
     */
    public function transform_matrix_translate($x, $y)
    {
        $value = "matrix(1, 0, 0, 1, {$x}, {$y})";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D平移
     * @param string $x 水平偏移位置, 整数, 自带px单位, 默认值`0`
     * @param string $y 垂直偏移位置, 整数, 自带px单位, 默认值`0`
     * 如果第二个参数未提供，则默认值为0
     * @return string
     */
    public function transform_translate($x, $y)
    {
        $value = "translate(1, 0, 0, 1, {$x}px, {$y}px)";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D平移 - 水平位置
     * @param string $x 水平偏移位置, 整数, 自带px单位, 默认值`0`
     * @return string
     */
    public function transform_translateX($x)
    {
        $value = "translatex(1, 0, 0, 1, {$x}px, 0)";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D平移 - 垂直方向
     * @param string $y 垂直偏移位置, 整数, 自带px单位, 默认值`0`
     * @return string
     */
    public function transform_translateY($y)
    {
        $value = "translatey(1, 0, 0, 1, 0, {$y}px)";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 矩阵变换 - 2D缩放
     * @param string $x 水平拉伸比例, 默认值`0`
     * @param string $y 垂直拉伸比例, 默认值`0`
     * @return string
     */
    public function transform_matrix_scale($x, $y)
    {
        $value = "matrix({$x}, 0, 0, {$y}, 0, 0)";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D缩放
     * @param string $x 水平拉伸比例, 默认值`0`, 可以有小数点
     * @param string $y 垂直拉伸比例, 默认值`0`, 可以有小数点
     * @return string
     */
    public function transform_scale($x, $y)
    {
        $value = "scale({$x}, {$y})";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D缩放 - 水平方向
     * @param string $x 水平拉伸比例, 默认值`0`, 可以有小数点
     * @return string
     */
    public function transform_scaleX($x)
    {
        $value = "scalex({$x})";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 2D缩放 - 垂直方向
     * @param string $y 垂直拉伸比例, 默认值`0`, 可以有小数点
     * @return string
     */
    public function transform_scaleY( $y)
    {
        $value = "scaley({$y})";
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    
    /**
     * 设置对象的转换
     * @param string $value 可能的值：
     * - none：无转换
     * 2D Transform Functions：
     * - matrix()：以一个含六值的(a,b,c,d,e,f)变换矩阵的形式指定一个2D变换，相当于直接应用一个[a,b,c,d,e,f]变换矩阵
     * - translate()：指定对象的2D translation（2D平移）。第一个参数对应X轴，第二个参数对应Y轴。如果第二个参数未提供，则默认值为0
     * - translatex()：指定对象X轴（水平方向）的平移
     * - translatey()：指定对象Y轴（垂直方向）的平移
     * - rotate()：指定对象的2D rotation（2D旋转），需先有 <' transform-origin '> 属性的定义
     * - scale()：指定对象的2D scale（2D缩放）。第一个参数对应X轴，第二个参数对应Y轴。如果第二个参数未提供，则默认取第一个参数的值
     * - scalex()：指定对象X轴的（水平方向）缩放
     * - scaley()：指定对象Y轴的（垂直方向）缩放
     * - skew()：指定对象skew transformation（斜切扭曲）。第一个参数对应X轴，第二个参数对应Y轴。如果第二个参数未提供，则默认值为0
     * - skewx()：指定对象X轴的（水平方向）扭曲
     * - skewy()：指定对象Y轴的（垂直方向）扭曲
     * 3D Transform Functions：
     * - matrix3d()：以一个4x4矩阵的形式指定一个3D变换
     * - translate3d()：指定对象的3D位移。第1个参数对应X轴，第2个参数对应Y轴，第3个参数对应Z轴，参数不允许省略
     * - translatez()：指定对象Z轴的平移
     * - rotate3d()：指定对象的3D旋转角度，其中前3个参数分别表示旋转的方向x,y,z，第4个参数表示旋转的角度，参数不允许省略
     * - rotatex()：指定对象在x轴上的旋转角度
     * - rotatey()：指定对象在y轴上的旋转角度
     * - rotatez()：指定对象在z轴上的旋转角度
     * - scale3d()：指定对象的3D缩放。第1个参数对应X轴，第2个参数对应Y轴，第3个参数对应Z轴，参数不允许省略
     * - scalez()：指定对象的z轴缩放
     * - perspective()：指定透视距离
     * @return string
     */
    public function trans_form($value)
    {
        return $this->cssPrefix('transform' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 以某个原点进行转换
     *              ^ x 
     *              |
     *              |
     *      --------+-------> y
     *              |(0,0)
     *              |
     * 
     * 
     * - 设置中心点位置，即x和y的交接点，例如`图片收缩动画`：
     * ~~~
     * 以左下角那个点进行收缩动画：transform-origin: bottom left;
     * ~~~
     * 
     * @param string $value 可能的值：
     * - 百分比、em、px等具体的值可以为负值
     * - 也可以是top、right、bottom、left和center这样的关键词
     * 2D变形
     * - 可以是1个值，也可以是两个值， 第一个是水平x位置，第二是垂直y位置
     * 3D变形
     * - 还包括了z轴的第三个值
     * 
     * @return string
     */
    public function transform_origin($value)
    {
        return $this->cssPrefix('transform-origin' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 设置2D变形的中心点位置
     * 
     * xy位置可能的值：
     * - 百分比、em、px等具体的值，可以为负值。
     * - 可以是top、right、bottom、left和center这样的关键词(offset-keyword)
     * 
     * 示例：
     * ~~~
     * # transform-origin: 0 0; 中心点在左上角
     * # 坐标中心点就是左下角位置 ： transform-origin: bottom left;
     * ~~~
     * 
     * @param string $x 水平位置
     * @param string $y 垂直位置
     */
    public function origin_2D($x, $y)
    {
        
    }
    
    public function origin_3D($x, $y, $z)
    {
        
    }
    
    /**
     * 指定某元素的子元素是（看起来）位于三维空间内，还是在该元素所在的平面内被扁平化。
     * @param string $value 可能的值：
     * - flat：指定子元素位于此元素所在平面内
     * - preserve-3d：指定子元素定位在三维空间内
     * @return string
     */
    public function transform_style($value)
    {
        return $this->cssPrefix('transform-style' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 指定观察者与「z=0」平面的距离，使具有三维位置变换的元素产生透视效果。
     * 「z>0」的三维元素比正常大，而「z<0」时则比正常小，大小程度由该属性的值决定。
     * @param string $value 可能的值：
     * - none：不指定透视
     * - <length>：指定观察者距离「z=0」平面的距离，为元素及其内容应用透视变换。不允许负值
     * @return string
     */
    public function perspective($value)
    {
        return $this->cssPrefix('perspective' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 指定透视点的位置
     * @param string $value 可能的值：
     * - <percentage>：用百分比指定透视点坐标值，相对于元素宽度。可以为负值。
     * - <length>：用长度值指定透视点坐标值。可以为负值。
     * - left：指定透视点的横坐标为left
     * - center①：指定透视点的横坐标为center
     * - right：指定透视点的横坐标为right
     * - top：指定透视点的纵坐标为top
     * - center②：指定透视点的纵坐标为center
     * - bottom：指定透视点的纵坐标为bottom
     * @return string
     */
    public function perspective_origin($value)
    {
        return $this->cssPrefix('perspective-origin' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
    
    /**
     * 指定元素背面面向用户时是否可见
     * @param string $value 可能的值：
     * - visible：指定元素背面可见，允许显示正面的镜像。
     * - hidden：指定元素背面不可见
     * @return string
     */
    public function backface_visibility($value)
    {
        return $this->cssPrefix('backface-visibility' . self::space() . $value . self::end,
            ['-ms-', '-moz-', '-webkit-']);
    }
}