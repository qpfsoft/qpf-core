<?php
namespace qpf\htmc\base;

/**
 * Button 按钮元素
 *
 */
class Button extends Element
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
     * 指示元素的提交地址, 覆盖form的设置
     * @param string $value
     * @return $this
     */
    public function formaction($value)
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