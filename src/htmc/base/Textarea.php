<?php
namespace qpf\htmc\base;

/**
 * Textarea 文本域元素
 */
class Textarea extends Element
{
    /**
     * 是否可以由浏览器自动完成其值
     * @param string $value 可能的值:
     * - `off` : 浏览器不会自动完成输入
     * - `on` : 浏览器可以根据用户先前在表单中输入的值自动完成值
     * @return $this
     */
    public function autocomplete($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
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
     * 定义列数
     * @param string $value
     * @return $this
     */
    public function cols($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素中允许的最大字符数
     * @param string $value
     * @return $this
     */
    public function maxlength($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素中允许的最小字符数
     * @param string $value
     * @return $this
     */
    public function minlength($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 向用户提供可在该字段中输入的内容的提示
     * @param string $value
     * @return $this
     */
    public function placeholder($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 是否可以编辑元素
     * @param bool $value
     * @return $this
     */
    public function readonly($value = true)
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
    
    /**
     * 定义文本区域中的行数
     * @param string $value
     * @return $this
     */
    public function rows($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 是否应该包装文本
     * @param string $value
     * @return $this
     */
    public function wrap($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}