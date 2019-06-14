<?php
namespace qpf\htmc\base;

/**
 * Option 元素
 */
class Option extends Element
{
    /**
     * 页面加载时选择的值
     * @param string $value
     * @return $this
     */
    public function selected($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 页面加载时, 元素的默认值
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}