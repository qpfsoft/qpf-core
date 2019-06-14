<?php
namespace qpf\htmc\css\attr;

/**
 * 表格样式
 *
 * - 表格边框的设置在[Border]对象中。
 * 
 * @author qiun
 *        
 */
class Table extends CssAttr
{

    /**
     * 表格的布局算法
     *
     * 通常fixed算法会比auto算法高效，尤其是对于那些长表格来说。fixed算法使得表格可以像其它元素一样一行一行的渲染。
     *
     * @param string $value
     *            可能的值：
     *            - auto：默认的自动算法。布局将基于各单元格的内容，换言之，可能你给某个单元格定义宽度为100px，
     *            但结果可能并不是100px。表格在每一单元格读取计算之后才会显示出来，速度很慢
     *            - fixed：固定布局的算法。在这算法中，水平布局是仅仅基于表格的宽度，表格边框的宽度，单元格间距，列的宽度，而和表格内容无关。也就是说，内容可能被裁切
     * @return string
     */
    public function table_layout($value)
    {
        return 'table-layout' . self::space() . $value . self::end;
    }

    /**
     * 表格的caption对象是在表格的那一边
     * 
     * @param string $value
     *            可能的值：
     *            - top：指定caption在表格上边
     *            - bottom：指定caption在表格下边
     * @return string
     */
    public function caption_side($value)
    {
        return 'caption-side' . self::space() . $value . self::end;
    }

    /**
     * 表格的单元格无内容时，是否显示该单元格的边框
     * 
     * @param string $value
     *            可能的值：
     *            - hide：指定当表格的单元格无内容时，隐藏该单元格的边框。
     *            - show：指定当表格的单元格无内容时，显示该单元格的边框。
     * @return string
     */
    public function empty_cells($value)
    {
        return 'empty-cells' . self::space() . $value . self::end;
    }
}