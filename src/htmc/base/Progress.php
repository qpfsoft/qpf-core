<?php
namespace qpf\htmc\base;

/**
 * Progress 进度 元素
 * - https://devdocs.io/html/element/progress
 */
class Progress extends Element
{
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
}