<?php
namespace qpf\htmc\base;

/**
 * Meter 元素
 */
class Meter extends Element
{
    /**
     * 表示上限范围的下限
     * @param string $value
     * @return $this
     */
    public function high($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 表示较低范围的上限
     * @param string $value
     * @return $this
     */
    public function low($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 允许的最大值
     * @param string $value
     * @return $this
     */
    public function max($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 允许的最小值
     * @param string $value
     * @return $this
     */
    public function min($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 此属性指示最佳数值。它必须在范围内（由min属性和max属性定义）
     * @param string $value
     * @return $this
     */
    public function optimum($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}