<?php
namespace qpf\htmc\base;

/**
 * Input 元素
 *
 */
class Input extends Element
{
    /**
     * 服务器接受的类型列表，通常是文件类型
     * @param string $value
     * @return $this
     */
    public function accept($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    /**
     * 图片的替代文本
     * @param string $value 代替文字
     * @return $this
     */
    public function alt($value){
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
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
     * 指示是否应在页面加载时选中元素
     * @param string $value
     * @return $this
     */
    public function checked($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 提交的内容书写的方向性，ltr或rtl
     * @param string $value
     * @return $this
     */
    public function dirname($value)
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
     * 预定义选项列表
     * @param string $value
     * @return $this
     */
    public function list($value)
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
     * 正则表达式
     * @param string $value
     * @return $this
     */
    public function pattern($value)
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
     * 元素的宽度, 如果元素的type属性是text，password那么它是字符数
     * @param string $value
     * @return $this
     */
    public function size($value)
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
    
    /**
     * 元素的宽度
     * @param string $value
     * @return $this
     */
    public function width($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}