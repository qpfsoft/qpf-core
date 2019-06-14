<?php
namespace qpf\htmc\base;

/**
 * Td 表格单元格
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
}