<?php
namespace qpf\htmc\css;

/**
 * CSS布局属性设置
 *
 * 已实现可用CSS属性:
 * - 宽高: width/height/min-/max-/
 * - 显示: display
 * - 盒子模型算法: box-sizing
 * - 边距: margin
 * - 填充: padding
 * - 定位: padding[top..]
 * - 浮动: float
 * - 清除浮动: clear
 * - 内容溢出: overflow
 * - 显示隐藏: visibility
 * - 堆叠顺序: z-index
 * - 透明度: opacity
 *
 * 衡量符号:
 * - % : 百分比
 * - px : 像素
 * - em : 相对长度
 *
 * div自适应居中:{margin:0 auto;}
 *
 * [min-width] 和 [max-width] 属性的应用:
 * ~~~
 * - 主要用于设置图片最小最大宽度限制比较多.
 * 比如一个图片为主列表,对象里图片大小不定时,为了不想让他太小不统一这个时候我们可以使用
 *
 * # 所有的图片都统一的进行缩放, 宽度不够的拉伸到200px,超过的缩小到200px
 * img{
 * min-width:200px;
 * max-width:200px;
 * }
 * 再如，一个盒子里有文章有图片混排的时候，有时图片宽度不能确定，这个时候如果html img图片宽度超出了div盒子宽度，可能图片就会撑破div盒子造成图片混乱。
 * ~~~
 *
 * @author qiun
 *        
 */
class Layout
{

    /**
     *
     * @var string 宽度, 例`100px`
     */
    public $width;

    /**
     * 设置宽度
     * 
     * @param string $value            
     * @return $this
     */
    public function width($value)
    {
        $this->width = $value;
        return $this;
    }

    /**
     *
     * @var string 高度, 例`100px`
     */
    public $height;

    /**
     * 设置高度
     * 
     * @param string $value            
     * @return $this
     */
    public function height($value)
    {
        $this->height = $value;
        return $this;
    }

    /**
     *
     * @var string 设置元素的最小宽度
     */
    public $min_width;

    /**
     * 设置最小宽度
     * 
     * @param string $value            
     * @return $this
     */
    public function minWidth($value)
    {
        $this->min_width = $value;
        return $this;
    }

    /**
     *
     * @var string 设置元素的最小宽度
     */
    public $min_height;

    /**
     * 设置最小高度
     * 
     * @param string $value            
     * @return $this
     */
    public function minHeight($value)
    {
        $this->min_height = $value;
        return $this;
    }

    /**
     *
     * @var string 设置元素的最小宽度
     */
    public $max_width;

    /**
     * 设置最大宽度
     * 
     * @param string $value            
     * @return $this
     */
    public function maxWidth($value)
    {
        $this->max_width = $value;
        return $this;
    }

    /**
     *
     * @var string 设置元素的最小宽度
     */
    public $max_height;

    /**
     * 设置最小高度
     * 
     * @param string $value            
     * @return $this
     */
    public function maxHeight($value)
    {
        $this->max_height = $value;
        return $this;
    }

    /**
     * 盒子模型计算
     *
     * 历史:
     * - (ie6传统)100 宽度 = 内容宽度+填充+边距, 正好占用100个像素.
     * - (w3c标准)100 宽度 = 内容宽度, 需要另外添加 填充和边距 宽度. 超过100个像素.
     * 参数:
     * - content-box:此值为其默认值，其让元素维持W3C的标准Box Model。(内容宽高)
     * - border-box:此值让元素维持IE传统的Box Model（IE6以下版本）.(边框宽高)
     * 兼容:
     * ie8以上,其他都支持.
     * Mozilla需要加上-moz-，Webkit内核需要加上-webkit-，Presto内核-o-,IE8-ms-
     * ie6&7兼容hack: { *width } 自己计算好实际宽度
     */
    public $box_sizing;

    /**
     * 设置盒子计算模式
     * 
     * @param string $content_border_box
     *            可选参数:
     *            - content-box:此值为其默认值，其让元素维持W3C的标准Box Model。(内容宽高)
     *            - border-box:此值让元素维持IE传统的Box Model（IE6以下版本）.(边框宽高)
     * @return $this;
     */
    public function boxSizing($content_border_box)
    {
        $this->box_sizing = $content_border_box;
        return $this;
    }

    /**
     *
     * @var string 属性规定元素应该生成的框的类型
     *      - none : 此元素不会被显示,与visibility属性的hidden值不同，其不为被隐藏的对象保留其物理空间
     *      - block : 此元素将显示为块级元素，此元素前后会带有换行符
     *      - inline : 默认。在一排显示。此元素会被显示为内联元素，元素前后没有换行符
     *      - inline-block : 行内块元素。（CSS2.1 新增的值）
     *      - list-item : 将块对象指定为列表项目。并可以添加可选项目标志
     *      - run-in : 此元素会根据上下文作为块级元素或内联元素显示
     *      - inherit : 规定应该从父元素继承 display 属性的值
     */
    public $display;

    /**
     * 元素显示类型
     * 
     * @param string $value
     *            参数:
     *            - none : 不显示
     *            - block : 块
     * @return $this
     */
    public function display($value)
    {
        $this->display = $value;
        return $this;
    }

    /**
     * 外边框
     * 
     * @var string|array - string : 简写 顺序为上右下左(顺时针)
     *      - array : [
     *      0 => top上
     *      1 => right右
     *      2 => bottom下
     *      3 => left左
     *      ]
     *      没有设置的位置用`0`或auto代替
     *      四边相同值缩写: 10px;
     *      四边其中上下值和左右值各相同缩写: 5px 7px; 上下5,左右7
     *      居中marign:0 auto;(兼容)该对象上级一定要设置text-align:center内容居中属性样式。
     *      兼容:
     *      IE6 浮动：Float 的时候容易造成双倍距离,
     *      使用 float: left; 后，在IE显示margin-left:1px;就变成2px的距离,加一个 display: inline; 就OK了
     */
    public $margin;

    /**
     * 设置边距
     * 
     * @param string|integer $top            
     * @param string|integer $right            
     * @param string|integer $bottom            
     * @param string|integer $left            
     * @return $this
     */
    public function margin($top, $right, $bottom, $left)
    {
        $this->margin[0] = $top;
        $this->margin[1] = $right;
        $this->margin[2] = $bottom;
        $this->margin[3] = $left;
        return $this;
    }

    /**
     * 设置上边距
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function margin_top($value)
    {
        $this->margin[0] = $value;
        return $this;
    }

    /**
     * 设置右边距
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function margin_right($value)
    {
        $this->margin[1] = $value;
        return $this;
    }

    /**
     * 设置下边距
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function margin_bottom($value)
    {
        $this->margin[2] = $value;
        return $this;
    }

    /**
     * 设置左边距
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function margin_left($value)
    {
        $this->margin[3] = $value;
        return $this;
    }

    /**
     * 内填充
     * 
     * @var string|array 数组采用上右左下序列
     */
    public $padding;

    /**
     * 设置内填充
     * 
     * @param string|integer $top            
     * @param string|integer $right            
     * @param string|integer $bottom            
     * @param string|integer $left            
     * @return $this
     */
    public function padding($top, $right, $bottom, $left)
    {
        $this->padding[0] = $top;
        $this->padding[1] = $right;
        $this->padding[2] = $bottom;
        $this->padding[3] = $left;
        return $this;
    }

    /**
     * 设置上填充
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function padding_top($value)
    {
        $this->margin[0] = $value;
        return $this;
    }

    /**
     * 设置右填充
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function padding_right($value)
    {
        $this->margin[1] = $value;
        return $this;
    }

    /**
     * 设置下填充
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function padding_bottom($value)
    {
        $this->margin[2] = $value;
        return $this;
    }

    /**
     * 设置左填充
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function padding_left($value)
    {
        $this->margin[3] = $value;
        return $this;
    }

    /**
     * 定位
     * 
     * @var string|array
     */
    public $position;

    /**
     * 定位类型
     * 
     * @var string - static : 静态
     *      - absolute : 绝对定位
     *      - fixed : 游览器固定定位
     *      - relative : 相对定位
     */
    public $position_type;

    /**
     * 设置定位类型
     * 
     * @param string $value            
     * @return $this
     */
    public function positionType($value)
    {
        $this->position_type = $value;
        return $this;
    }

    /**
     * 设置定位
     * 
     * @param string $type
     *            定位类型
     * @param string|integer $top            
     * @param string|integer $right            
     * @param string|integer $bottom            
     * @param string|integer $left            
     * @return $this
     */
    public function position($type, $top, $right, $bottom, $left)
    {
        $this->positionType($type);
        $this->position[0] = $top;
        $this->position[1] = $right;
        $this->position[2] = $bottom;
        $this->position[3] = $left;
        return $this;
    }

    /**
     * 顶部定位位置
     * 
     * @param string $value            
     * @return $this
     */
    public function top($value)
    {
        $this->position[0] = $value;
        return $this;
    }

    /**
     * 右侧定位位置
     * 
     * @param string $value            
     * @return $this
     */
    public function right($value)
    {
        $this->position[1] = $value;
        return $this;
    }

    /**
     * 底部定位位置
     * 
     * @param string $value            
     * @return $this
     */
    public function bottom($value)
    {
        $this->position[2] = $value;
        return $this;
    }

    /**
     * 左侧定位位置
     * 
     * @param string $value            
     * @return $this
     */
    public function left($value)
    {
        $this->position[3] = $value;
        return $this;
    }

    public $float;

    /**
     * 设置浮动
     * 
     * @param string $left_right_none
     *            浮动方向
     * @return $this
     */
    public function float($left_right_none)
    {
        $this->float = $left_right_none;
        return $this;
    }

    public $clear;

    /**
     * 清除浮动
     * 
     * @param string $left_right_both_none
     *            清除的方向
     * @return $this
     */
    public function clear($left_right_both_none)
    {
        $this->clear = $left_right_both_none;
        return $this;
    }

    /**
     * 内容溢出处理
     * 
     * @var string|array - string: x和y相同的处理
     *      - array: [0]x 横向 [1]y 纵向
     *      兼容:
     *      带xy后缀的css在IE8以及更早不支持.
     *      - 绝对定位[absolute|fixed]总是不会被父级剪切.
     *      解决1:给父级设置position:absolute或fixed或relative
     *      解决2:在绝对定位元素和overflow元素之间增加一个元素并设置position:absolute或fixed或relative
     */
    public $overflow;

    /**
     * 内容溢出处理
     * 
     * @param string $type
     *            可选类型:
     *            - visible : 溢出部分显示
     *            - hidden : 溢出部分不显示
     *            - scroll : 显示滚动条
     *            - auto : 如果溢出才显示滚动条
     *            - no-display : 如果溢出删除整个框[类似于添加了display:none].目前没有浏览器支持
     *            - no-content : 如果溢出隐藏内容[类似于添加了visibility: hidden],目前没有浏览器支持
     * @return $this
     */
    public function overflow($type = 'visible')
    {
        $this->overflow = $type;
        return $this;
    }

    /**
     * 水平溢出处理
     * 
     * @param string $type            
     * @return $this
     */
    public function overflow_x($type)
    {
        $this->overflow[0] = $type;
        return $this;
    }

    /**
     * 垂直溢出处理
     * 
     * @param string $type            
     * @return $this
     */
    public function overflow_y($type)
    {
        $this->overflow[1] = $type;
        return $this;
    }

    /**
     * 元素是否可见
     * 
     * @var string
     */
    public $visibility;

    /**
     * 设置元素是否可见
     * 
     * @param string $type
     *            可选值:
     *            - visible : 默认,可见
     *            - hidden : 不可见,占用页面空间
     *            - collapse : 对一般元素表现的与'hidden'是一样的,但对表格会表现为'display:none',不占用空间.
     *            - inherit : 从父元素继承 visibility 属性的值
     * @return $this
     */
    public function visibility($type)
    {
        $this->visibility = $type;
        return $this;
    }

    /**
     * 元素堆叠顺序, 值高在前面
     *
     * - 仅能在定位元素上奏效
     * - auto: 默认, 堆叠与父元素相等
     * 
     * @var integer
     */
    public $z_index;

    /**
     * 元素堆叠顺序, 值高在前面
     * 
     * @param integer $value            
     * @return $this
     */
    public function zindex($value)
    {
        $this->z_index = $value;
        return $this;
    }

    /**
     * 透明度
     * 
     * @var float - 兼容;ie8以及更早支持替代的 filter 属性。例如：filter:Alpha(opacity=50)
     */
    public $opacity;

    /**
     * 设置透明度
     * 
     * @param float $value
     *            完全透明0.0 ~ 1.0不透明
     * @return $this
     */
    public function opacity($value)
    {
        $this->opacity = $value;
        return $this;
    }
}