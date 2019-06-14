<?php
namespace qpf\htmc\build;


/**
 * 表格生成器
 */
class Table extends Builder
{
    
    /**
     * 内联样式
     * @var array
     */
    protected $style = [];
    
    /**
     * 内容结构树
     * @var array
     */
    protected $tree = [
        'caption' => null,
        'thead' => null,
        'tbody' => null,
        'tfoot' => null,
    ];
    
    /**
     * 内容流
     * @var array
     */
    protected $contents = [];
    
    /**
     * 构造函数
     */
    public function __construct()
    {

    }
    
    /**
     * 设置表格属性
     * @param string|array $attrs 表格属性
     */
    public function setAttr($attrs)
    {
        
    }
    
    /**
     * 表格对齐 - 属性已优化为css样式
     * @param string $value 可能的值:
     * - `left` : 表格显示在文档左侧
     * - `center` : 表格显示在文档中心
     * - `rigth` : 表格显示在文档右侧
     * @return $this
     */
    public function align($value)
    {
        switch ($value) {
            case 'left':
                $this->style[] = 'float: left';
                break;
            case 'right':
                $this->style[] = 'float: right';
                break;
            case 'center':
                $this->style[] = 'margin: 0 auto';
                break;
        }
        return $this;
    }
    
    /**
     * 表的背景颜色 - 属性已优化为css样式
     * @param string $value 6位十六进制代码组成，前缀为“＃”
     * @return $this
     */
    public function bgcolor($value)
    {
        $this->style[] = 'background-color: ' . $value;
        return $this;
    }
    
    /**
     * 表的边框 - 属性已优化为css样式
     * @param int $value 像素px, `0`代表无边框
     * @return $this
     */
    public function border($width, $color = '#000', $style = 'solid')
    {
        $this->style[] = $this->spliceBlank('border:', $width, $style, $color);
        return $this;
    }
    
    /**
     * 表的单元格边框显示方式 - 属性已优化为css样式
     * @param string $value 可能的值:
     * - `collapse` : 合并边框
     * - `separate` :  单元格独立边框
     * @return $this
     */
    public function cellpadding($value = 'collapse')
    {
        $this->style[] = 'border-collapse: ' . $value;
        return $this;
    }
    
    /**
     * 表的宽度
     * @param string $value 宽度, 需后缀单位.
     * @return $this
     */
    public function width($value)
    {
        $this->style[] = 'width: ' . $value;
        return $this;
    }
    
    
    /**
     * 表的标题
     * @param string $title 标题
     * @return $this
     */
    public function caption($title)
    {
        $this->tree[__FUNCTION__] = $title;
        return $this;
    }
    
    /**
     * 表的标题 - caption的别名
     * @param string $title 标题
     * @return $this
     */
    public function title($title)
    {
        return $this->caption($title);
    }
    
    /**
     * 添加行到表的表头
     * @param array $rows 行数据
     * @return $this
     */
    public function thead($rows)
    {
        $this->tree[__FUNCTION__][] = $this->tr($rows);
        return $this;
    }
    
    /**
     * 添加行到表的主体
     * @param array $rows 行数据
     * @return $this
     */
    public function tbody($rows)
    {
        $this->tree[__FUNCTION__][] = $this->tr($rows);
        return $this;
    }
    
    /**
     * 添加行到表的页脚
     * @param array $rows 行数据
     * @return $this
     */
    public function tfoot($rows)
    {
        $this->tree[__FUNCTION__][] = $this->tr($rows);
        return $this;
    }
    
    /**
     * 添加一行到表中
     * @param array $rows 行数据
     * @return $this
     */
    public function row($rows)
    {
        $this->contents[] = $this->tr($rows);
        return $this;
    }
    
    /**
     * 生成td标签
     * @param string $value 内容
     * @return string
     */
    public function td($value)
    {
        return '<td>' . $value . '</td>';
    }
    
    /**
     * 生成tr代码段
     * @param array $tds td标签数据集合
     * @return string
     */
    public function tr(array $tds)
    {
        $code = '<tr>';
        
        if(!empty($tds)) {
            $code .= PHP_EOL;
            foreach ($tds as $td) {
                $code .= $this->td($td) . PHP_EOL;
            }
        }
        
        $code .= '</tr>';
        
        return $code;
    }
    
    /**
     * 生成表格代码
     * 
     * ```格式
     * [
     *      'caption' => null, // `null`代表不设置
     *      'thead'   => [['td1', 'td2'], ..] // 值为内容设置
     *      'tbody'   => null,
     *      'tfoot'   => null,
     *      0         => ['td1', 'td2'], // 普通行
     *      1         => ['td1', 'td2'],
     *      ...
     * ]
     * ```
     * 
     * @param array $table 表格数据
     */
    public function build(array $table = [])
    {
        if(empty($table)) {
            $table = array_merge($this->tree, $this->contents);
        }
        
        $html = [];
        
        // 创建表格元素
        $tab = new \qpf\htmc\base\Table();
        // 设置表格样式类
        $tab->class(implode(' ', $this->class));

        $eol = PHP_EOL;
        
        if(isset($table['caption'])) {
            $tab->setContent('<caption>' . $table['caption'] . '</caption>');
        }
        
        if(isset($table['thead'])) {
            $tab->setContent('<thead>' . $eol . implode($eol, $table['thead']) . $eol . '</thead>');
        }
        
        if(isset($table['tbody'])) {
            $tab->setContent('<tbody>' . $eol . implode($eol, $table['tbody']) . $eol . '</tbody>');
        }
        
        if(isset($table['tfoot'])) {
            $tab->setContent('<tfoot>' . $eol . implode($eol, $table['tfoot']) . $eol . '</tfoot>');
        }
        
        
        if (!empty($table)) {
            foreach ($table as $i => $tr) {
                if (is_numeric($i)) {
                    $tab->setContent($tr);
                }
            }
        }

        return $tab->build();
    }
    
    /**
     * 生成表格代码
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}