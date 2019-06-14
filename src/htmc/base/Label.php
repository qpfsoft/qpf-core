<?php
namespace qpf\htmc\base;

/**
 * Label 元素
 */
class Label extends Element
{
    /**
     * 描述属于这个的元素
     * @param string $value
     * @return $this
     */
    public function for($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}