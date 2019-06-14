<?php
namespace qpf\htmc\base;

/**
 * Base 元素
 */
class Base extends Element
{
    /**
     * 链接资源的URL
     * @param string $value
     * @return $this
     */
    public function href($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 显示链接URL的位置
     * @param string $value 可能的值:
     * - `_self` : 默认, 当前页面
     * - `_blank` : 新页面
     * - `_parent` : 父页面
     * - `_top` : 顶级窗口
     * @return $this
     */
    public function target($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}