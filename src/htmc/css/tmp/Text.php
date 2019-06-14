<?php
namespace qpf\htmc\css;

/**
 * CSS文本属性
 *
 * 已实现属性:
 * -
 *
 * @author qiun
 */
class Text
{

    /**
     * 文本颜色
     * 
     * @var string
     */
    public $color;

    /**
     * 设置文本的颜色
     * 
     * @param string $value
     *            可能的值:
     *            - color: 颜色名称, 例`red`
     *            - hex: 十六进制颜色, 例`#ff0000`
     *            - rgb: rgb代码颜色, 例rgb(255,0,0);//红,绿,蓝0~255
     *            ~~~
     *            - 颜色英文: background-color:red 共17种.
     *            - rgb格式: 红,绿,蓝 { background-color：rgb（100,100,100）}
     *            用黄色，绿色，蓝色的值的百分比设定color的值{ background-color:rgb(10%,10%,50%) }
     *            - 十六进制: 以#开头,0123456789abcdef 分别表示0~16的数字,
     *            前两位:红色的值,中间两位:绿色的值,最后两位:蓝色
     *            例: #cc 00 66
     *            红的部分的值为12*16+12=204,204就是red的值
     *            绿色部分的值为0*16+0=0，0就是green部分的值
     *            蓝色部分的值为6*16+6=112；112就是blue的值
     *            例如#c06;
     *            这样的颜色表示方法，这是因为红色、绿色、蓝色部分的俩个值相同，
     *            其值为#cc0066，#c06为他的缩写形式；
     *            例如:#a
     *            这种表示方法是因为所有的六个值都相等，其值为#aaaaaa，#a是其缩写；
     *            ~~~
     * @return $this
     */
    public function color($value)
    {
        $this->color = $value;
        return $this;
    }

    public $font_family;

    public $font_style;

    public $font_variant;

    public $font_weight;

    public $font_size;

    public $line_height;

    public $text_align;

    public $text_decoration;

    public $text_indent;
}