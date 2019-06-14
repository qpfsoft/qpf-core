<?php
namespace qpf\htmc\base;

/**
 * Html 元素
 */
class Html extends Element
{
    /**
     * 指定文档缓存清单的URL
     * @param string $value
     * @return $this
     */
    public function manifest($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}