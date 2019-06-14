<?php
namespace qpf\htmc\css\attr;

/**
 * CSS布局样式
 * 
 * @author qiun
 *        
 */
class Layout extends CssAttr
{

    /**
     * 设置宽度
     * 
     * @param string $value
     *            宽度, 例`100px`
     * @return string
     */
    public function width($value)
    {
        return 'width' . self::space() . $value . self::end;
    }

    /**
     * 设置高度
     * 
     * @param string $value
     *            高度, 例`100px`
     * @return string
     */
    public function height($value)
    {
        return 'height' . self::space() . $value . self::end;
    }

    /**
     * 设置最小宽度
     * 
     * @param string $value
     *            设置元素的最小宽度
     * @return string
     */
    public function min_width($value)
    {
        return 'min-width' . self::space() . $value . self::end;
    }

    /**
     * 设置最小高度
     * 
     * @param string $value
     *            设置元素的最小高度
     * @return string
     */
    public function min_height($value)
    {
        return 'min-height' . self::space() . $value . self::end;
    }

    /**
     * 设置最大宽度
     * 
     * @param string $value
     *            设置元素的最小宽度
     * @return string
     */
    public function max_width($value)
    {
        return 'max-width' . self::space() . $value . self::end;
    }

    /**
     * 设置最小高度
     * 
     * @param string $value
     *            设置元素的最小高度
     * @return string
     */
    public function max_height($value)
    {
        return 'max-height' . self::space() . $value . self::end;
    }

    /**
     * 元素显示类型
     *
     * 全兼容的inline-block：
     * ~~~
     * div {
     * display: inline-block;
     * *display: inline;
     * *zoom: 1;
     * }
     *
     * Basic Support包含值：none | inline | block | list-item | inline-block
     * table系包含值：table | inline-table | table-caption | table-cell | table-row |
     * table-row-group | table-column | table-column-group | table-footer-group | table-header-group
     *
     * ~~~
     * 
     * @param string $value 参数:
     * - none : 此元素不会被显示,与visibility属性的hidden值不同，其不为被隐藏的对象保留其物理空间
     * - block : 此元素将显示为块级元素，此元素前后会带有换行符
     * - inline : 默认。在一排显示。此元素会被显示为内联元素，元素前后没有换行符
     * - inline-block : 行内块元素。（CSS2.1 新增的值）
     * - list-item : 将块对象指定为列表项目。并可以添加可选项目标志
     * - run-in : 此元素会根据上下文作为块级元素或内联元素显示，css3
     * - inherit : 规定应该从父元素继承 display 属性的值
     * - box ：将对象作为弹性伸缩盒显示，css3
     * - inline-box ： 将对象作为内联块级弹性伸缩盒显示，css3
     * - flexbox ：将对象作为弹性伸缩盒显示，css3
     * - inline-flexbox ：将对象作为内联块级弹性伸缩盒显示，css3
     * - inline-flex ： 将对象作为内联块级弹性伸缩盒显示，css3
     * @return string
     */
    public function display($value)
    {
        return 'display' . self::space() . $value . self::end;
    }

    /**
     * 设置盒子计算模式 - CSS3
     *
     * - ie6~7 不支持，火狐老版本需要前缀`-moz-`, 谷歌老版本需要前缀`-webkit-`
     *
     * @param string $content_border_box
     *            可选参数:
     *            - content-box : padding和border不被包含在定义的width和height之内。标准模式下的盒模型
     *            对象的实际宽度等于设置的width值和border、padding之和，即 ( Element width = width + border + padding )
     *            - border-box : padding和border被包含在定义的width和height之内。怪异模式下的盒模型
     *            对象的实际宽度就等于设置的width值，即使定义有border和padding也不会改变对象的实际宽度，即 ( Element width = width )
     * @return $this;
     */
    public function box_sizing($content_border_box)
    {
        return 'box-sizing' . self::space() . $content_border_box . self::end;
    }

    /**
     * 设置边距
     *
     * - 推荐要设置四个位置时使用, 值会合并和简写
     * - 顺序为上右下左(顺时针)
     * - 值可为`auto` 或 标量`1px` , 或`0`.
     * - 可负值
     * 
     * @param string|integer $top 上边距
     * @param string|integer $right 右边距
     * @param string|integer $bottom 下边距
     * @param string|integer $left 左边距
     * @return string
     */
    public function margin($top, $right, $bottom, $left)
    {
        $value = $this->parseTRBL($top, $right, $bottom, $left);
        return 'margin' . self::space() . $value . self::end;
    }
    
    /**
     * 设置间距 - 水平居中
     * @param string $top_bottom 上下的间距
     * @param string $left_right 左右的间距
     * @return string
     */
    public function margin_auto()
    {
        return 'margin' . self::space() . '0 auto' . self::end;
    }
    
    /**
     * 设置间距 - 2个参数, 上下 左右 两两相等
     * @param string $top_bottom 上下的间距
     * @param string $left_right 左右的间距
     * @return string
     */
    public function margin2($top_bottom , $left_right)
    {
        $value = "{$top_bottom} {$left_right}";
        return 'margin' . self::space() . $value . self::end;
    }

    /**
     * 设置边距 - 四边值相同
     * 
     * @param string $value            
     * @return string
     */
    public function margin_all($value)
    {
        return 'margin' . self::space() . $value . self::end;
    }

    /**
     * 快速生成边距 - 每个属性单独一条样式
     *
     * - 值为`null`时不会处理指定方向
     * 
     * @param string $top
     *            上
     * @param string $right
     *            右
     * @param string $bottom
     *            下
     * @param string $left
     *            左
     * @return string 返回一条字符串可能包含多个方向间距的设置
     */
    public function margin_fast($top, $right, $bottom, $left)
    {
        $value = '';
        
        if (! is_null($top)) {
            $value .= $this->margin_top($top);
        }
        
        if (! is_null($right)) {
            $value .= $this->margin_right($right);
        }
        
        if (! is_null($bottom)) {
            $value .= $this->margin_bottom($bottom);
        }
        
        if (! is_null($left)) {
            $value .= $this->margin_left($left);
        }
        
        return $value;
    }

    /**
     * 设置上边距
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function margin_top($value)
    {
        return 'margin-top' . self::space() . $value . self::end;
    }

    /**
     * 设置右边距
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function margin_right($value)
    {
        return 'margin-right' . self::space() . $value . self::end;
    }

    /**
     * 设置下边距
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function margin_bottom($value)
    {
        return 'margin-bottom' . self::space() . $value . self::end;
    }

    /**
     * 设置左边距
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function margin_left($value)
    {
        return 'margin-left' . self::space() . $value . self::end;
    }

    /**
     * 设置内填充
     * - 推荐要设置四个位置时使用, 值会合并和简写
     * - 顺序为上右下左(顺时针)
     * - 值可为`auto` 或 标量`1px` , 或`0`.
     * 
     * @param string|integer $top 上填充
     * @param string|integer $right 右填充
     * @param string|integer $bottom 下填充
     * @param string|integer $left 左填充
     * @return $this
     */
    public function padding($top, $right, $bottom, $left)
    {
        $value = $this->parseTRBL($top, $right, $bottom, $left);
        return 'padding' . self::space() . $value . self::end;
    }
    
    /**
     * 设置内填充 - 2参数
     *
     * @param string|integer $top_bottom 上下填充
     * @param string|integer $left_right 左右填充
     * @return $this
     */
    public function padding2($top_bottom, $left_right)
    {
        $value = $top_bottom . ' ' . $left_right;
        return 'padding' . self::space() . $value . self::end;
    }
    
    /**
     * 设置四边的填充
     * @param string $value '1px' 或 `0` , `auto`
     * @return string
     */
    public function padding_all($value)
    {
        return 'padding' . self::space() . $value . self::end;
    }

    /**
     * 设置上填充
     * 
     * @param string|integer $value '1px' 或 `0` , `auto`
     * @return string
     */
    public function padding_top($value)
    {
        return 'padding-top' . self::space() . $value . self::end;
    }

    /**
     * 设置右填充
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function padding_right($value)
    {
        return 'padding-right' . self::space() . $value . self::end;
    }

    /**
     * 设置下填充
     * 
     * @param string|integer $value
     *            '1px' 或 `0` , `auto`
     * @return string
     */
    public function padding_bottom($value)
    {
        return 'padding-bottom' . self::space() . $value . self::end;
    }

    /**
     * 设置左填充
     * 
     * @param string|integer $value            
     * @return $this
     */
    public function padding_left($value)
    {
        return 'padding-left' . self::space() . $value . self::end;
    }

    /**
     * 设置定位类型
     * 
     * - relative : 可偏移位置, 自身文档流位置继续占用. 一般内部需要定位,父可设置此类型,不用去偏移.
     * 
     * @param string $value 可选值:
     * - absolute : 绝对定位, 脱离常规流, 相对于 static 定位以外的第一个父元素进行定位。
     *            此时偏移属性参照的是离自身最近的定位祖先元素，如果没有定位的祖先元素，则一直回溯到body元素。
     *            盒子的偏移位置不影响常规流中的任何元素，其margin不与其他任何margin折叠。
     * - fixed : 绝对定位，与absolute一致，但偏移定位是以窗口为参考。当出现滚动条时，对象不会随着滚动。
     * - relative : 相对定位,遵循常规流，并且参照自身在常规流中的位置通过top，right，bottom，left这4个定位偏移属性进行偏移时不会影响常规流中的任何元素。
     * - static : 没有定位,遵循常规流，元素出现在正常的流中（忽略 top, bottom, left, right 或者 z-index 声明）。
     *            兼容有问题:
     * - center ： 与absolute一致，但偏移定位是以定位祖先元素的中心点为参考。盒子在其包含容器垂直水平居中。（CSS3）
     * - page : 与absolute一致。元素在分页媒体或者区域块内，元素的包含块始终是初始包含块，否则取决于每个absolute模式。（CSS3）
     * - sticky : 对象在常态时遵循常规流。它就像是relative和fixed的合体，当在屏幕中时按常规流排版，
     *            当卷动到屏幕外时则表现如fixed。该属性的表现是现实中你见到的吸附效果。（CSS3）
     * @return string
     */
    public function position($value)
    {
        return 'position' . self::space() . $value . self::end;
    }

    /**
     * 顶部定位位置
     * 
     * @param string $value
     *            '1px' 或 `auto`
     * @return string
     */
    public function top($value)
    {
        return 'top' . self::space() . $value . self::end;
    }

    /**
     * 右侧定位位置
     * 
     * @param string $value
     *            '1px' 或 `auto`
     * @return string
     */
    public function right($value)
    {
        return 'right' . self::space() . $value . self::end;
    }

    /**
     * 底部定位位置
     * 
     * @param string $value
     *            '1px' 或 `auto`
     * @return string
     */
    public function bottom($value)
    {
        return 'bottom' . self::space() . $value . self::end;
    }

    /**
     * 左侧定位位置
     * 
     * @param string $value            
     * @return string
     */
    public function left($value)
    {
        return 'left' . self::space() . $value . self::end;
    }

    /**
     * 设置浮动
     * 
     * @param string $left_right_none
     *            浮动方向
     * @return string
     */
    public function float($left_right_none)
    {
        return 'float' . self::space() . $left_right_none . self::end;
    }

    /**
     * 清除浮动
     * 
     * @param string $left_right_both_none 清除的方向
     * @return string
     */
    public function clear($left_right_both_none)
    {
        return 'clear' . self::space() . $left_right_both_none . self::end;
    }
    
    /**
     * 内容溢出处理
     * @param string $value 可选类型:
     * - visible : 溢出部分显示
     * - hidden : 溢出部分不显示
     * - scroll : 显示滚动条
     * - auto : 如果溢出才显示滚动条
     * @return string
     */
    public function overflow($value)
    {
        return 'overflow' . self::space() . $value . self::end;
    }

    /**
     * 水平溢出处理
     * 
     * @param string $value 可选类型:
     * - visible : 溢出部分显示
     * - hidden : 溢出部分不显示
     * - scroll : 显示滚动条
     * - auto : 如果溢出才显示滚动条
     * @return string
     */
    public function overflow_x($value)
    {
        return 'overflow-x' . self::space() . $value . self::end;
    }

    /**
     * 垂直溢出处理
     * 
     * @param string $value
     *            可选类型:
     *            - visible : 溢出部分显示
     *            - hidden : 溢出部分不显示
     *            - scroll : 显示滚动条
     *            - auto : 如果溢出才显示滚动条
     * @return string
     */
    public function overflow_y($value)
    {
        return 'overflow-y' . self::space() . $value . self::end;
    }

    /**
     * 设置元素是否可见
     * 
     * @param string $value 可选值:
     * - visible : 默认,可见
     * - hidden : 不可见,占用页面空间
     * - collapse : 对一般元素表现的与'hidden'是一样的,但对表格会表现为'display:none',不占用空间.
     * @return string
     */
    public function visibility($value)
    {
        return 'visibility' . self::space() . $value . self::end;
    }

    /**
     * 元素堆叠顺序, 值高在前面
     * 
     * @param integer $value            
     * @return string
     */
    public function z_index($value)
    {
        return 'z-index' . self::space() . $value . self::end;
    }

    /**
     * 设置透明度 - css3
     *
     * ~~~兼容IE
     * div{filter:alpha(opacity=50);} // IE8 以及以下
     * div{opacity:.5;} // IE9 以及以上
     * ~~~
     * 
     * @param float $value
     *            完全透明0.0 ~ 1.0不透明， 0可以不写`.5`
     * @return $this
     */
    public function opacity($value)
    {
        return 'opacity' . self::space() . $value . self::end;
    }
}