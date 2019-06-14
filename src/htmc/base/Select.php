<?php
namespace qpf\htmc\base;

/**
 * Select 下拉框元素
 */
class Select extends Element
{
    /**
     * 页面加载后，元素应自动聚焦
     * @param string $value
     * @return $this
     */
    public function autofocus($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 是否表示多个值可以在类型的输入侧来输入侧email或file
     * @param bool $value
     * @return $this
     */
    public function multiple($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 是否需要填写此元素
     * @param bool $value
     * @return $this
     */
    public function required($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}