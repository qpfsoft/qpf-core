<?php
namespace qpf\htmc\base;

/**
 * Table 表格元素
 */
class Table extends Element
{
    /**
     * 边框宽度 - 遗留属性, 用css代替
     * @param string $value 宽度
     * @return $this
     */
    public function border($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
}