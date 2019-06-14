<?php
namespace qpf\htmc\base;

/**
 * Iframe 元素
 */
class Iframe extends Element
{
    /**
     * 指定iframe的功能策略
     * @param string $value
     * @return $this
     */
    public function allow($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定嵌入式文档必须同意对其自身强制执行的内容安全策略
     * @param string $value CSP策略指令的字符串
     * @return $this
     */
    public function csp($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示资源的相对提取优先级
     * @param string $value 可能的值:
     * - `auto` : 表示没有首选项。浏览器可以使用其自己的启发式来确定资源的优先级
     * - `high` : 向浏览器指示资源具有高优先级
     * - `low` : 向浏览器指示资源具有低优先级
     * @return $this
     */
    public function importance($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示是否应该懒惰地加载元素
     * @param string $value
     * @return $this
     */
    public function lazyload($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 启用额外限制
     * @param string $value 可能的值: 
     * - `allow-forms`: 允许嵌入式浏览上下文提交表单。如果未使用此关键字，则不允许此操作。
     * - 更多.. https://devdocs.io/html/element/iframe
     * @return $this
     */
    public function sandbox($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 可嵌入内容的URL
     * @param string $value
     * @return $this
     */
    public function src($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 嵌入式上下文要包含的页面内容
     * - 期望该属性通常与sandbox属性一起使用
     * @param string $value
     * @return $this
     */
    public function srcdoc($value)
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