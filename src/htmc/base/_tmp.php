<?php
namespace qpf\htmc\base;

/**
 *  元素
 */
class Tmp extends Element
{
    /**
     * a
     * @param string $value
     * @return $this
     */
    public function aaaaa($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}