<?php
namespace qpf\htmc\base;

/**
 * Details 元素
 */
class Details extends Element
{
    /**
     * 指示是否在页面加载时显示详细信息
     * @param bool $value 当前是否可见
     * @return $this
     */
    public function open($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}