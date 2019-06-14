<?php
namespace qpf\htmc\base;

/**
 * Td 表格行
 */
class Td extends Element
{
    /**
     * 单元格应跨越的列数
     * @param string $value
     * @return $this
     */
    public function colspan($value){
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 适用于此元素的元素的ID
     * @param string $value
     * @return $this
     */
    public function headers($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义表格单元格应跨越的行数
     * @param string $value
     * @return $this
     */
    public function rowspan($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义标题与之相关的单元格
     * @param string $value 可能的值:
     * - `row` : 标题涉及它所属行的所有单元格
     * - `col` : 标题涉及它所属列的所有单元格
     * - `rowgroup` : 标题属于一个行组，并与其所有单元格相关
     * - `colgroup` : 标题属于colgroup并与其所有单元格相关
     * - `auto`
     * @return $this
     */
    public function scope($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}